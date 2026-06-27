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

// Helper to get or create client instance
function getOrCreateClient(clientId) {
    if (clients.has(clientId)) {
        const clientData = clients.get(clientId);
        clientData.lastActive = Date.now();
        return clientData;
    }

    return createNewClient(clientId);
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

// Load existing saved sessions on startup
function loadExistingSessions() {
    const authDir = path.join(__dirname, '.wwebjs_auth');
    if (!fs.existsSync(authDir)) {
        return;
    }

    try {
        const files = fs.readdirSync(authDir);
        for (const file of files) {
            if (file.startsWith('session-')) {
                const clientId = file.substring('session-'.length);
                if (clientId) {
                    console.log(`[Manager] Menemukan sesi tersimpan untuk: ${clientId}. Memulai koneksi...`);
                    getOrCreateClient(clientId);
                }
            }
        }
    } catch (err) {
        console.error('[Manager] Gagal memuat sesi tersimpan:', err.message);
    }
}

// Trigger loading saved sessions
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
        if (!clientData.isReady && !clientData.reconnecting && (now - clientData.lastActive > 10 * 60 * 1000)) {
            console.log(`[Manager] Menghapus client tidak aktif & belum terautentikasi: ${clientId}`);
            clients.delete(clientId);
            clientData.client.destroy().catch(() => {});
        }
    }
}, 5 * 60 * 1000);

// Endpoint: Check status of a client
app.get('/status', (req, res) => {
    const clientId = req.query.clientId;
    if (!clientId) {
        // Fallback for background system tasks: check if there is at least one client connected/ready
        let anyReady = false;
        for (const [id, clientData] of clients.entries()) {
            if (clientData.isReady) {
                anyReady = true;
                break;
            }
        }
        return res.json({ ready: anyReady, qr: null });
    }

    const clientData = getOrCreateClient(clientId);
    res.json({ ready: clientData.isReady, qr: clientData.latestQrDataUrl });
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

// Endpoint: Send text message
app.post('/send-message', async (req, res) => {
    let { clientId, phone, message } = req.body;
    
    // Fallback: If no clientId, use the first ready client
    if (!clientId) {
        for (const [id, clientData] of clients.entries()) {
            if (clientData.isReady) {
                clientId = id;
                break;
            }
        }
    }

    if (!clientId) {
        return res.status(503).json({ error: 'Tidak ada WhatsApp client yang siap / terhubung.' });
    }

    const clientData = clients.get(clientId);
    if (!clientData || !clientData.isReady) {
        return res.status(503).json({ error: `WhatsApp client untuk ${clientId} belum siap / terhubung.` });
    }

    if (!phone || !message) {
        return res.status(400).json({ error: 'Parameter phone dan message wajib diisi.' });
    }

    try {
        // Clean phone number format
        let cleanPhone = phone.replace(/\D/g, '');
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.slice(1);
        }
        const formattedPhone = `${cleanPhone}@c.us`;

        const response = await clientData.client.sendMessage(formattedPhone, message);
        res.json({ success: true, messageId: response.id.id });
    } catch (error) {
        console.error(`[${clientId}] Gagal mengirim pesan:`, error);
        res.status(500).json({ error: error.message });
    }
});

// Endpoint: Send document file (PDF, etc.)
app.post('/send-document', async (req, res) => {
    let { clientId, phone, fileUrl, filename, caption } = req.body;

    // Fallback: If no clientId, use the first ready client
    if (!clientId) {
        for (const [id, clientData] of clients.entries()) {
            if (clientData.isReady) {
                clientId = id;
                break;
            }
        }
    }

    if (!clientId) {
        return res.status(503).json({ error: 'Tidak ada WhatsApp client yang siap / terhubung.' });
    }

    const clientData = clients.get(clientId);
    if (!clientData || !clientData.isReady) {
        return res.status(503).json({ error: `WhatsApp client untuk ${clientId} belum siap / terhubung.` });
    }

    if (!phone || !fileUrl || !filename) {
        return res.status(400).json({ error: 'Parameter phone, fileUrl, dan filename wajib diisi.' });
    }

    try {
        // Clean phone number format
        let cleanPhone = phone.replace(/\D/g, '');
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.slice(1);
        }
        const formattedPhone = `${cleanPhone}@c.us`;

        console.log(`[${clientId}] Mengunduh file dari URL: ${fileUrl}`);
        
        // Fetch the file as an arraybuffer
        const fileResponse = await axios.get(fileUrl, { 
            responseType: 'arraybuffer',
            timeout: 15000 // 15 seconds timeout
        });
        
        const mimeType = fileResponse.headers['content-type'] || 'application/pdf';
        const base64Data = Buffer.from(fileResponse.data, 'binary').toString('base64');
        
        const media = new MessageMedia(mimeType, base64Data, filename);
        
        console.log(`[${clientId}] Mengirim berkas dokumen ke ${formattedPhone}...`);
        const response = await clientData.client.sendMessage(formattedPhone, media, { 
            caption: caption || '' 
        });
        
        res.json({ success: true, messageId: response.id.id });
    } catch (error) {
        console.error(`[${clientId}] Gagal mengirim dokumen:`, error);
        res.status(500).json({ error: error.message });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`\nWhatsApp Gateway API berjalan pada port ${PORT}`);
    console.log(`Status check: http://localhost:${PORT}/status\n`);
});
