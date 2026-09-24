const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const QRCodeGen = require('qrcode');
const express = require('express');
const axios = require('axios');
const fs = require('fs');
const path = require('path');
const app = express();

app.use(express.json());

// Enable CORS for dashboard status polling
app.use((req, res, next) => {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    next();
});

// Map to store WhatsApp clients keyed by clientId
const clients = new Map();

// === RESOURCE LIMITS ===
// Server 8GB RAM: max 3 Chromium browsers bersamaan (~300MB each)
const MAX_CONCURRENT_CLIENTS = parseInt(process.env.MAX_WA_SESSIONS || '1', 10);
const STAGGER_DELAY_MS = 10000; // Jeda 10 detik antar inisialisasi saat startup
const IDLE_EVICT_MS = 15 * 60 * 1000; // Evict sesi idle > 15 menit jika melebihi limit

// Helper to execute with retry if execution context was destroyed momentarily
async function executeWithRetry(fn, retries = 3) {
    for (let attempt = 1; attempt <= retries; attempt++) {
        try {
            return await fn();
        } catch (err) {
            const isContextError = err.message && (
                err.message.includes('Execution context was destroyed') ||
                err.message.includes('Session closed') ||
                err.message.includes('Target closed') ||
                err.message.includes('Evaluation failed')
            );
            if (isContextError && attempt < retries) {
                console.warn(`[Retry] Terjadi perpindahan konteks browser (${err.message}). Menunggu 1 detik & mengulang otomatis (${attempt}/${retries})...`);
                await new Promise(r => setTimeout(r, 1200));
                continue;
            }
            throw err;
        }
    }
}

// Helper to execute WhatsApp actions sequentially per client with a settling delay
function queueClientAction(clientData, fn) {
    if (!clientData.actionQueue) {
        clientData.actionQueue = Promise.resolve();
    }

    const nextAction = clientData.actionQueue.then(async () => {
        const res = await executeWithRetry(fn, 3);
        await new Promise(resolve => setTimeout(resolve, 800));
        return res;
    }).catch(async (err) => {
        await new Promise(resolve => setTimeout(resolve, 800));
        throw err;
    });

    clientData.actionQueue = nextAction.catch(() => {});
    return nextAction;
}

// Count currently active (initialized) clients
function getActiveClientCount() {
    let count = 0;
    for (const [, cd] of clients.entries()) {
        if (!cd.placeholder) count++;
    }
    return count;
}

// Evict the least recently active client to make room for a new one
async function evictLeastActiveClient(excludeClientId) {
    let oldest = null;
    let oldestTime = Infinity;
    for (const [id, cd] of clients.entries()) {
        if (id === excludeClientId || cd.placeholder) continue;
        if (cd.lastActive < oldestTime) {
            oldestTime = cd.lastActive;
            oldest = id;
        }
    }
    if (oldest) {
        console.log(`[Manager] ⚠️ Evicting sesi paling lama tidak aktif: ${oldest} (idle ${Math.round((Date.now() - oldestTime) / 1000)}s)`);
        const cd = clients.get(oldest);
        clients.delete(oldest);
        try { await cd.client.destroy(); } catch (_) {}
    }
}

// Helper to find ANY ready client (for shared clinic session)
function findAnyReadyClient() {
    for (const [id, cd] of clients.entries()) {
        if (cd.isReady && !cd.placeholder && !cd.reconnecting) {
            return { clientId: id, clientData: cd };
        }
    }
    return null;
}

// Helper to get or create client instance (with concurrency guard)
function getOrCreateClient(clientId) {
    if (clients.has(clientId)) {
        const clientData = clients.get(clientId);
        // If it was a placeholder (lazy), now actually initialize it
        if (clientData.placeholder) {
            console.log(`[Manager] Lazy-init: client '${clientId}' diminta, memulai Chromium sekarang...`);
            clients.delete(clientId);
            // Evict if at limit (async, best-effort)
            if (getActiveClientCount() >= MAX_CONCURRENT_CLIENTS) {
                evictLeastActiveClient(clientId);
            }
            return createNewClient(clientId);
        }
        clientData.lastActive = Date.now();
        return clientData;
    }

    // Evict if at limit
    if (getActiveClientCount() >= MAX_CONCURRENT_CLIENTS) {
        evictLeastActiveClient(clientId);
    }
    return createNewClient(clientId);
}

// ensureClientReady: wake up placeholder or wait for initializing client, with shared fallback
async function ensureClientReady(clientId, timeoutMs = 25000) {
    // 1. If the exact clientId is already ready, use it
    if (clients.has(clientId)) {
        const cd = clients.get(clientId);
        if (cd.isReady && !cd.placeholder && !cd.reconnecting) {
            cd.lastActive = Date.now();
            return { clientId, clientData: cd, shared: false };
        }
    }

    // 2. Shared Clinic Fallback: if there's ANY other ready client, use it transparently
    const readyFallback = findAnyReadyClient();
    if (readyFallback) {
        console.log(`[SharedSession] Client '${clientId}' belum siap, menggunakan sesi aktif '${readyFallback.clientId}' sebagai fallback.`);
        readyFallback.clientData.lastActive = Date.now();
        return { clientId: readyFallback.clientId, clientData: readyFallback.clientData, shared: true };
    }

    // 3. No ready client anywhere — try to wake up the requested clientId
    if (clients.has(clientId) && clients.get(clientId).placeholder) {
        console.log(`[ensureClientReady] Membangunkan placeholder '${clientId}'...`);
        const cd = getOrCreateClient(clientId); // triggers lazy-init

        // Wait for 'ready' event with timeout
        const readyPromise = new Promise((resolve, reject) => {
            const timer = setTimeout(() => {
                reject(new Error(`Timeout ${timeoutMs}ms menunggu sesi '${clientId}' siap.`));
            }, timeoutMs);

            const checkInterval = setInterval(() => {
                const current = clients.get(clientId);
                if (current && current.isReady) {
                    clearTimeout(timer);
                    clearInterval(checkInterval);
                    resolve(current);
                }
            }, 500);
        });

        try {
            const readyClient = await readyPromise;
            readyClient.lastActive = Date.now();
            return { clientId, clientData: readyClient, shared: false };
        } catch (err) {
            console.warn(`[ensureClientReady] ${err.message}`);
            // Last-ditch: maybe another client became ready while we were waiting
            const lastChance = findAnyReadyClient();
            if (lastChance) {
                lastChance.clientData.lastActive = Date.now();
                return { clientId: lastChance.clientId, clientData: lastChance.clientData, shared: true };
            }
            throw err;
        }
    }

    // 4. No session folder exists at all — nothing to wake up
    throw new Error(`Tidak ada sesi WhatsApp yang tersedia. Silakan scan QR Code terlebih dahulu.`);
}

// Function to create a fresh client instance and setup listeners
function createNewClient(clientId) {
    console.log(`[Manager] Membuat client baru untuk clientId: ${clientId}`);
    
    const client = new Client({
        authStrategy: new LocalAuth({
            clientId: clientId,
            dataPath: './.wwebjs_auth'
        }),
        puppeteer: {
            handleSIGINT: false,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu',
                '--disable-extensions',
                '--disable-software-rasterizer',
                '--disable-default-apps',
                '--mute-audio'
            ]
        }
    });

    const clientData = {
        client,
        isReady: false,
        latestQrDataUrl: null,
        reconnecting: false,
        lastActive: Date.now()
    };

    clients.set(clientId, clientData);

    // Robust disconnect and reconnection handler
    async function handleDisconnect(reason) {
        if (clientData.reconnecting) return;
        clientData.reconnecting = true;
        clientData.isReady = false;
        clientData.latestQrDataUrl = null;
        
        console.log(`\n====================================================================`);
        console.log(`[${clientId}] Menangani pemutus koneksi WhatsApp. Alasan: ${reason}`);
        console.log(`====================================================================\n`);
        
        try {
            console.log(`[${clientId}] Mencoba menutup sesi browser Puppeteer sebelumnya...`);
            await client.destroy();
        } catch (err) {
            console.error(`[${clientId}] Gagal destroy client:`, err.message);
        }
        
        console.log(`[${clientId}] Menunggu 5 detik sebelum menginisialisasi ulang...`);
        setTimeout(() => {
            if (!clients.has(clientId)) return; // Client might have been deleted/reset
            
            console.log(`[${clientId}] Memulai inisialisasi ulang WhatsApp Client dengan instance baru...`);
            clients.delete(clientId);
            const newClientData = createNewClient(clientId);
            newClientData.reconnecting = false;
        }, 5000);
    }

    // Event: QR Code generation for scanning
    client.on('qr', (qr) => {
        clientData.lastActive = Date.now();
        console.log(`\n====================================================================`);
        console.log(`[${clientId}] SCAN QR CODE DI BAWAH INI DENGAN APLIKASI WHATSAPP DI HP ANDA:`);
        console.log(`====================================================================\n`);
        qrcode.generate(qr, { small: true });
        console.log(`\n====================================================================\n`);

        // Generate base64 Data URL for web dashboard
        QRCodeGen.toDataURL(qr, (err, url) => {
            if (!err) {
                clientData.latestQrDataUrl = url;
            }
        });
    });

    // Event: Client successfully authenticated and loaded
    client.on('ready', () => {
        console.log(`\n====================================================================`);
        console.log(`[${clientId}] WhatsApp Client siap digunakan dan terhubung!`);
        console.log(`====================================================================\n`);
        clientData.isReady = true;
        clientData.latestQrDataUrl = null; // Clear QR URL when ready
    });

    client.on('auth_failure', (msg) => {
        console.error(`[${clientId}] Gagal autentikasi WhatsApp:`, msg);
        clientData.latestQrDataUrl = null;
        clientData.isReady = false;
        
        // Hapus folder sesi jika autentikasi gagal secara permanen agar user bisa scan ulang
        const sessionDir = path.join(__dirname, '.wwebjs_auth', `session-${clientId}`);
        try {
            if (fs.existsSync(sessionDir)) {
                fs.rmSync(sessionDir, { recursive: true, force: true });
                console.log(`[${clientId}] Folder sesi dibersihkan karena gagal autentikasi.`);
            }
        } catch (err) {
            console.error(`[${clientId}] Gagal menghapus folder sesi:`, err.message);
        }

        handleDisconnect('Authentication failure');
    });

    client.on('disconnected', (reason) => {
        console.log(`[${clientId}] Koneksi WhatsApp terputus (Event):`, reason);
        handleDisconnect(reason);
    });

    // Initialize client connection
    client.initialize().catch(err => {
        console.error(`[${clientId}] Gagal inisialisasi awal client:`, err.message);
    });

    return clientData;
}

// Global exception handlers to keep server running
process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception Global Terdeteksi:', err);
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Promise Rejection Terdeteksi:', reason);
});

// Load existing saved sessions on startup — LAZY MODE
// Hanya mendaftarkan sesi sebagai placeholder, TANPA membuka Chromium.
// Chromium hanya dibuka saat sesi benar-benar diakses oleh user via API.
function loadExistingSessions() {
    const authDir = path.join(__dirname, '.wwebjs_auth');
    if (!fs.existsSync(authDir)) {
        return;
    }

    try {
        const files = fs.readdirSync(authDir);
        const validSessions = [];
        for (const file of files) {
            if (file.startsWith('session-')) {
                const clientId = file.substring('session-'.length);
                
                // Bersihkan sesi lama 'user-*' yang usang
                if (clientId.startsWith('user-')) {
                    console.log(`[Manager] Membersihkan folder sesi usang: ${file}`);
                    try {
                        fs.rmSync(path.join(authDir, file), { recursive: true, force: true });
                    } catch (e) {}
                    continue;
                }

                // Bersihkan sesi 'device-test' yang tidak diperlukan
                if (clientId === 'device-test') {
                    console.log(`[Manager] Membersihkan folder sesi test: ${file}`);
                    try {
                        fs.rmSync(path.join(authDir, file), { recursive: true, force: true });
                    } catch (e) {}
                    continue;
                }

                if (clientId) {
                    validSessions.push(clientId);
                }
            }
        }

        console.log(`[Manager] Ditemukan ${validSessions.length} sesi tersimpan.`);
        console.log(`[Manager] Mode: LAZY LOADING (Chromium hanya dibuka saat diakses).`);
        console.log(`[Manager] Batas sesi aktif bersamaan: ${MAX_CONCURRENT_CLIENTS}`);

        // Register sebagai placeholder (tanpa Chromium)
        for (const clientId of validSessions) {
            console.log(`[Manager] 📋 Mendaftarkan sesi (placeholder): ${clientId}`);
            clients.set(clientId, {
                placeholder: true,
                clientId: clientId,
                isReady: false,
                latestQrDataUrl: null,
                reconnecting: false,
                lastActive: 0 // Lowest priority for eviction
            });
        }
    } catch (err) {
        console.error('[Manager] Gagal memuat sesi tersimpan:', err.message);
    }
}

// Trigger loading saved sessions (lazy — NO Chromium spawned)
loadExistingSessions();

// Helper to trigger recreation from heartbeat
async function recreateClientFromHeartbeat(clientId, clientData) {
    clientData.isReady = false;
    clientData.latestQrDataUrl = null;
    clientData.reconnecting = true;
    try {
        await clientData.client.destroy();
    } catch (e) {}

    setTimeout(() => {
        if (!clients.has(clientId)) return;
        clients.delete(clientId);
        const newClientData = createNewClient(clientId);
        newClientData.reconnecting = false;
    }, 5000);
}

// Heartbeat check for all ready clients every 30 seconds
setInterval(async () => {
    for (const [clientId, clientData] of clients.entries()) {
        if (clientData.isReady && !clientData.reconnecting) {
            try {
                const state = await clientData.client.getState();
                console.log(`[Heartbeat - ${clientId}] Status: ${state}`);
                if (state !== 'CONNECTED') {
                    console.warn(`[Heartbeat - ${clientId}] Status tidak CONNECTED (${state}). Memicu re-koneksi...`);
                    await recreateClientFromHeartbeat(clientId, clientData);
                }
            } catch (error) {
                console.error(`[Heartbeat - ${clientId}] Gagal mendapatkan state client:`, error.message);
                await recreateClientFromHeartbeat(clientId, clientData);
            }
        }
    }
}, 30000);

// Cleanup interval: check every 5 minutes to clean up unauthenticated inactive clients
setInterval(() => {
    const now = Date.now();
    for (const [clientId, clientData] of clients.entries()) {
        if (clientData.placeholder) continue; // Skip placeholders
        if (!clientData.isReady && !clientData.reconnecting && (now - clientData.lastActive > 10 * 60 * 1000)) {
            console.log(`[Manager] Menghapus client tidak aktif & belum terautentikasi: ${clientId}`);
            clients.delete(clientId);
            clientData.client.destroy().catch(() => {});
        }
    }

    // Log resource usage
    const activeCount = getActiveClientCount();
    const placeholderCount = clients.size - activeCount;
    const memUsage = process.memoryUsage();
    console.log(`[Monitor] Sesi aktif: ${activeCount}/${MAX_CONCURRENT_CLIENTS} | Placeholder: ${placeholderCount} | RAM Node.js: ${Math.round(memUsage.rss / 1024 / 1024)}MB`);
}, 5 * 60 * 1000);

// Endpoint: Check status of a client (with shared session awareness)
app.get('/status', (req, res) => {
    const clientId = req.query.clientId;
    if (!clientId) {
        // Fallback for background system tasks: check if there is at least one client connected/ready
        const ready = findAnyReadyClient();
        return res.json({ ready: !!ready, qr: null });
    }

    // 1. Check if the exact clientId is ready
    if (clients.has(clientId)) {
        const cd = clients.get(clientId);
        if (cd.isReady && !cd.placeholder) {
            return res.json({ ready: true, qr: null, shared: false });
        }
        // If placeholder, check for shared fallback first
        if (cd.placeholder) {
            const sharedReady = findAnyReadyClient();
            if (sharedReady) {
                return res.json({
                    ready: true,
                    qr: null,
                    shared: true,
                    sharedClientId: sharedReady.clientId,
                    message: 'WhatsApp Klinik terhubung melalui sesi bersama.'
                });
            }
            return res.json({ 
                ready: false, 
                qr: null, 
                placeholder: true,
                message: 'Sesi tersimpan. Klik "Hubungkan" untuk memulai koneksi.'
            });
        }
        // Initializing (not placeholder, not ready yet)
        return res.json({ ready: false, qr: cd.latestQrDataUrl });
    }

    // 2. ClientId not in Map — check for shared fallback
    const sharedReady = findAnyReadyClient();
    if (sharedReady) {
        return res.json({
            ready: true,
            qr: null,
            shared: true,
            sharedClientId: sharedReady.clientId,
            message: 'WhatsApp Klinik terhubung melalui sesi bersama.'
        });
    }

    // 3. Optional auto-init via query param
    if (req.query.autoInit === 'true' || req.query.autoInit === '1') {
        console.log(`[Manager] autoInit diminta via /status untuk client: ${clientId}`);
        const cd = getOrCreateClient(clientId);
        return res.json({ ready: cd.isReady, qr: cd.latestQrDataUrl });
    }

    // 4. Nothing available at all
    return res.json({ ready: false, qr: null, message: 'Belum ada sesi WhatsApp yang terhubung.' });
});

// Endpoint: Explicitly initialize/connect a specific client (triggers Chromium)
app.post('/init', (req, res) => {
    const { clientId } = req.body;
    if (!clientId) {
        return res.status(400).json({ error: 'clientId wajib diisi.' });
    }

    console.log(`[Manager] 🚀 Init request untuk client: ${clientId}`);
    const activeCount = getActiveClientCount();
    console.log(`[Manager] Sesi aktif saat ini: ${activeCount}/${MAX_CONCURRENT_CLIENTS}`);

    const clientData = getOrCreateClient(clientId);
    res.json({ 
        success: true, 
        ready: clientData.isReady, 
        qr: clientData.latestQrDataUrl,
        activeSessions: getActiveClientCount(),
        maxSessions: MAX_CONCURRENT_CLIENTS
    });
});

// Endpoint: Reset a specific client session
app.post('/reset-session', async (req, res) => {
    const { clientId } = req.body;
    if (!clientId) {
        return res.status(400).json({ error: 'clientId wajib diisi.' });
    }

    console.log(`[Manager] Reset sesi untuk clientId: ${clientId}`);

    if (clients.has(clientId)) {
        const clientData = clients.get(clientId);
        clients.delete(clientId);
        try {
            await clientData.client.destroy();
        } catch (err) {
            console.error(`[Manager] Gagal destroy client saat reset:`, err.message);
        }
    }

    // Delete session folder
    const sessionDir = path.join(__dirname, '.wwebjs_auth', `session-${clientId}`);
    try {
        if (fs.existsSync(sessionDir)) {
            fs.rmSync(sessionDir, { recursive: true, force: true });
            console.log(`[Manager] Berhasil menghapus folder sesi: ${sessionDir}`);
        }
    } catch (err) {
        console.error(`[Manager] Gagal menghapus folder sesi:`, err.message);
    }

    res.json({ success: true, message: `Sesi ${clientId} berhasil direset.` });
});

// Helper to resolve exact target JID and verify registration in WA Web
async function resolveTargetJid(clientData, rawPhone) {
    let cleanPhone = (rawPhone || '').toString().split('@')[0].replace(/\D/g, '');
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '62' + cleanPhone.slice(1);
    }
    // Fix: nomor tanpa awalan 0/62 (misal 81234567890) → tambahkan 62
    if (/^8\d{8,12}$/.test(cleanPhone)) {
        cleanPhone = '62' + cleanPhone;
    }
    if (!cleanPhone || cleanPhone.length < 10) {
        return { valid: false, error: `Format nomor telepon tidak valid (${rawPhone}). Minimal 10 digit angka.`, code: 'INVALID_FORMAT' };
    }
    if (cleanPhone.length > 16) {
        return { valid: false, error: `Format nomor telepon terlalu panjang (${rawPhone}).`, code: 'INVALID_FORMAT' };
    }

    const standardPhoneJid = `${cleanPhone}@c.us`;

    try {
        let isRegistered = false;
        let resolvedLid = null;

        // Step 1: Use getContactLidAndPhone to populate LID mapping in WA Web internal store
        if (typeof clientData.client.getContactLidAndPhone === 'function') {
            try {
                const lidPhoneList = await queueClientAction(clientData, () =>
                    clientData.client.getContactLidAndPhone([standardPhoneJid])
                );
                if (lidPhoneList && lidPhoneList[0] && (lidPhoneList[0].lid || lidPhoneList[0].pn)) {
                    isRegistered = true;
                    resolvedLid = lidPhoneList[0].lid || null;
                }
            } catch (_) {}
        }

        // Step 2: Fallback to getNumberId to verify registration
        if (!isRegistered) {
            const numberId = await queueClientAction(clientData, () => 
                clientData.client.getNumberId(cleanPhone)
            );
            if (numberId) {
                isRegistered = true;
                if (numberId._serialized && numberId._serialized.endsWith('@lid')) {
                    resolvedLid = numberId._serialized;
                }
            }
        }

        if (!isRegistered) {
            return { valid: false, error: `Nomor telepon (${rawPhone}) tidak terdaftar di WhatsApp.`, code: 'UNREGISTERED' };
        }

        // Pre-warm contact stores so WhatsApp Web links phone JID and LID internally
        try {
            if (resolvedLid) {
                await queueClientAction(clientData, () => clientData.client.getContactById(resolvedLid).catch(() => {}));
            }
            await queueClientAction(clientData, () => clientData.client.getContactById(standardPhoneJid).catch(() => {}));
        } catch (_) {}

        // CRITICAL FIX: The target JID for client.sendMessage() MUST ALWAYS be the phone JID (${cleanPhone}@c.us)!
        // NEVER use @lid as the recipient target in sendMessage(), because WhatsApp Web requires
        // the canonical phone-based JID to initiate a 1-to-1 conversation.
        return { valid: true, jid: standardPhoneJid, cleanPhone, lid: resolvedLid };
    } catch (err) {
        console.warn(`[JID Resolver] Gagal cek nomor ${cleanPhone} (${err.message}). Fallback ke JID standar...`);
        return { valid: true, jid: standardPhoneJid, cleanPhone };
    }
}

// Endpoint: Send text message (with ensureClientReady + shared session)
app.post('/send-message', async (req, res) => {
    let { clientId, phone, message } = req.body;
    
    if (!phone || !message) {
        return res.status(400).json({ error: 'Parameter phone dan message wajib diisi.' });
    }

    let clientData, resolvedClientId, shared = false;
    try {
        const resolved = await ensureClientReady(clientId || '__system__');
        clientData = resolved.clientData;
        resolvedClientId = resolved.clientId;
        shared = resolved.shared;
    } catch (err) {
        return res.status(503).json({ 
            error: err.message || 'Tidak ada WhatsApp client yang siap / terhubung.', 
            code: 'GATEWAY_DISCONNECTED' 
        });
    }

    let formattedPhone = `${phone}@c.us`;
    try {
        const jidResult = await resolveTargetJid(clientData, phone);
        if (!jidResult.valid) {
            return res.status(400).json({ error: jidResult.error, code: jidResult.code || 'INVALID_PHONE' });
        }
        formattedPhone = jidResult.jid;

        let response;
        try {
            response = await queueClientAction(clientData, () => 
                clientData.client.sendMessage(formattedPhone, message)
            );
        } catch (firstSendErr) {
            const msg = firstSendErr?.message || '';
            if (msg.includes('No LID for user') || msg.includes('static.whatsapp.net')) {
                console.warn(`[${resolvedClientId}] Terdeteksi kendala LID saat kirim pesan ke ${formattedPhone}. Mencoba refresh kontak dan kirim ulang...`);
                try {
                    if (typeof clientData.client.getContactLidAndPhone === 'function') {
                        await queueClientAction(clientData, () => clientData.client.getContactLidAndPhone([formattedPhone]));
                    }
                    await queueClientAction(clientData, () => clientData.client.getChatById(formattedPhone));
                } catch (_) {}
                response = await queueClientAction(clientData, () => 
                    clientData.client.sendMessage(formattedPhone, message)
                );
            } else {
                throw firstSendErr;
            }
        }

        const messageId = response?.id?._serialized || response?.id?.id || (typeof response?.id === 'string' ? response.id : null) || `selfhosted_msg_${Date.now()}`;
        res.json({ success: true, messageId, shared });
    } catch (error) {
        console.error(`[${resolvedClientId}] Gagal mengirim pesan ke ${formattedPhone}:`, error);
        let errorMsg = error.message || 'Gagal mengirim pesan via WhatsApp Gateway.';
        let errorCode = 'SEND_ERROR';
        if (errorMsg.includes('No LID for user') || errorMsg.includes('static.whatsapp.net')) {
            const cleanDisplay = formattedPhone.replace(/@.*$/, '');
            errorMsg = `Gagal mengirim ke ${cleanDisplay}: Kontak WhatsApp tidak dapat terverifikasi. Pastikan nomor HP terdaftar di WhatsApp.`;
            errorCode = 'UNREGISTERED';
        }
        res.status(500).json({ error: errorMsg, code: errorCode });
    }
});

// Endpoint: Send document file (PDF, etc.) — with ensureClientReady + shared session + __x_id sanitization
app.post('/send-document', async (req, res) => {
    let { clientId, phone, fileUrl, filePath, filename, caption } = req.body;

    if (!phone || (!fileUrl && !filePath) || !filename) {
        return res.status(400).json({ error: 'Parameter phone, file (filePath/fileUrl), dan filename wajib diisi.' });
    }

    let clientData, resolvedClientId, shared = false;
    try {
        const resolved = await ensureClientReady(clientId || '__system__');
        clientData = resolved.clientData;
        resolvedClientId = resolved.clientId;
        shared = resolved.shared;
    } catch (err) {
        return res.status(503).json({ 
            error: err.message || 'Tidak ada WhatsApp client yang siap / terhubung.', 
            code: 'GATEWAY_DISCONNECTED' 
        });
    }

    let formattedPhone = `${phone}@c.us`;
    try {
        const jidResult = await resolveTargetJid(clientData, phone);
        if (!jidResult.valid) {
            return res.status(400).json({ error: jidResult.error, code: jidResult.code || 'INVALID_PHONE' });
        }
        formattedPhone = jidResult.jid;

        let media = null;
        let localFound = false;

        // 1. PRIORITAS UTAMA: Baca langsung dari disk lokal (instan, bebas timeout)
        const candidatePaths = [];
        if (filePath) {
            if (path.isAbsolute(filePath)) {
                candidatePaths.push(filePath);
            }
            const cleanRel = filePath.replace(/^[/\\]+/, '').replace(/^storage[/\\]+/, '');
            candidatePaths.push(path.join(__dirname, '..', 'storage', 'app', 'public', cleanRel));
            candidatePaths.push(path.join(__dirname, '..', 'storage', 'app', cleanRel));
            candidatePaths.push(path.join(__dirname, '..', 'public', 'storage', cleanRel));
        }
        if (fileUrl) {
            try {
                const parsedUrl = new URL(fileUrl);
                const relPath = parsedUrl.pathname.replace(/^\/storage\//, '');
                candidatePaths.push(path.join(__dirname, '..', 'storage', 'app', 'public', relPath));
                candidatePaths.push(path.join(__dirname, '..', 'public', 'storage', relPath));
            } catch (_) {}
        }

        for (const testPath of candidatePaths) {
            if (testPath && fs.existsSync(testPath)) {
                try {
                    console.log(`[${resolvedClientId}] Membaca berkas langsung dari disk lokal: ${testPath}`);
                    const fileBuffer = fs.readFileSync(testPath);
                    const base64Data = fileBuffer.toString('base64');
                    media = new MessageMedia('application/pdf', base64Data, filename);
                    localFound = true;
                    break;
                } catch (readErr) {
                    console.warn(`[${resolvedClientId}] Gagal membaca berkas lokal ${testPath}:`, readErr.message);
                }
            }
        }

        // 2. FALLBACK: Jika tidak ditemukan di disk lokal, unduh via HTTP dengan timeout singkat
        if (!localFound && fileUrl) {
            console.log(`[${resolvedClientId}] Berkas lokal tidak ditemukan, mengunduh dari URL: ${fileUrl}`);
            try {
                const fileResponse = await axios.get(fileUrl, { 
                    responseType: 'arraybuffer',
                    timeout: 5000 // 5 seconds timeout
                });
                
                const mimeType = fileResponse.headers['content-type'] || 'application/pdf';
                const base64Data = Buffer.from(fileResponse.data, 'binary').toString('base64');
                media = new MessageMedia(mimeType, base64Data, filename);
            } catch (downloadErr) {
                console.error(`[${resolvedClientId}] Gagal mengunduh file via URL (${downloadErr.message})`);
                return res.status(400).json({ error: `Gagal memuat dokumen PDF: ${downloadErr.message}`, code: 'FILE_ERROR' });
            }
        }

        if (!media) {
            return res.status(400).json({ error: 'Dokumen PDF tidak ditemukan pada disk lokal maupun URL.', code: 'FILE_NOT_FOUND' });
        }

        // CRITICAL FIX: Sanitize __x_id from MessageMedia to prevent
        // "Data passed to getter must include an id property" error in newer WhatsApp Web builds.
        if (media && typeof media === 'object') {
            try {
                delete media.__x_id;
                delete media._data?.__x_id;
            } catch (_) {}
        }
        
        console.log(`[${resolvedClientId}] Mengirim berkas dokumen ke ${formattedPhone}...`);
        let response;
        try {
            response = await queueClientAction(clientData, () =>
                clientData.client.sendMessage(formattedPhone, media, { 
                    caption: caption || '' 
                })
            );
        } catch (firstSendErr) {
            const msg = firstSendErr?.message || '';
            if (msg.includes('No LID for user') || msg.includes('static.whatsapp.net')) {
                console.warn(`[${resolvedClientId}] Terdeteksi kendala LID saat kirim dokumen ke ${formattedPhone}. Mencoba refresh kontak dan kirim ulang...`);
                try {
                    if (typeof clientData.client.getContactLidAndPhone === 'function') {
                        await queueClientAction(clientData, () => clientData.client.getContactLidAndPhone([formattedPhone]));
                    }
                    await queueClientAction(clientData, () => clientData.client.getChatById(formattedPhone));
                } catch (_) {}
                response = await queueClientAction(clientData, () =>
                    clientData.client.sendMessage(formattedPhone, media, { 
                        caption: caption || '' 
                    })
                );
            } else if (msg.includes('id property') || msg.includes('__x_id')) {
                // __x_id fix: second attempt with deep-cleaned media object
                console.warn(`[${resolvedClientId}] Terdeteksi error __x_id pada media. Membersihkan dan mencoba ulang...`);
                const cleanMedia = new MessageMedia(media.mimetype, media.data, media.filename);
                response = await queueClientAction(clientData, () =>
                    clientData.client.sendMessage(formattedPhone, cleanMedia, {
                        caption: caption || ''
                    })
                );
            } else {
                throw firstSendErr;
            }
        }
        
        const messageId = response?.id?._serialized || response?.id?.id || (typeof response?.id === 'string' ? response.id : null) || `selfhosted_doc_${Date.now()}`;
        res.json({ success: true, messageId, shared });
    } catch (error) {
        console.error(`[${resolvedClientId}] Gagal mengirim dokumen ke ${formattedPhone}:`, error);
        let errorMsg = error.message || 'Gagal mengirim dokumen via WhatsApp Gateway.';
        let errorCode = 'SEND_ERROR';
        if (errorMsg.includes('No LID for user') || errorMsg.includes('static.whatsapp.net')) {
            const cleanDisplay = formattedPhone.replace(/@.*$/, '');
            errorMsg = `Gagal mengirim ke ${cleanDisplay}: Kontak WhatsApp tidak dapat terverifikasi. Pastikan nomor HP terdaftar di WhatsApp.`;
            errorCode = 'UNREGISTERED';
        }
        res.status(500).json({ error: errorMsg, code: errorCode });
    }
});

// Endpoint: Quick test send (for admin verification from status page)
app.post('/test-send', async (req, res) => {
    const { phone, message } = req.body;
    if (!phone) {
        return res.status(400).json({ error: 'Parameter phone wajib diisi.' });
    }

    const ready = findAnyReadyClient();
    if (!ready) {
        return res.status(503).json({ error: 'Tidak ada sesi WhatsApp yang aktif.', code: 'GATEWAY_DISCONNECTED' });
    }

    const testMessage = message || `✅ Tes koneksi WhatsApp Gateway berhasil!\n\n📅 ${new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' })}\n🖥️ Clinical Management System`;

    try {
        const jidResult = await resolveTargetJid(ready.clientData, phone);
        if (!jidResult.valid) {
            return res.status(400).json({ error: jidResult.error, code: jidResult.code });
        }

        const response = await queueClientAction(ready.clientData, () =>
            ready.clientData.client.sendMessage(jidResult.jid, testMessage)
        );

        const messageId = response?.id?._serialized || response?.id?.id || `test_${Date.now()}`;
        res.json({ success: true, messageId, usedSession: ready.clientId });
    } catch (error) {
        console.error(`[TestSend] Gagal mengirim tes ke ${phone}:`, error);
        res.status(500).json({ error: error.message || 'Gagal mengirim pesan tes.', code: 'SEND_ERROR' });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`\nWhatsApp Gateway API berjalan pada port ${PORT}`);
    console.log(`Status check: http://localhost:${PORT}/status\n`);
});
