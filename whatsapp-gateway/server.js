const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const QRCodeGen = require('qrcode');
const express = require('express');
const axios = require('axios');
const app = express();

let latestQrDataUrl = null;

app.use(express.json());

// Enable CORS for dashboard status polling
app.use((req, res, next) => {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    next();
});

// Initialize WhatsApp Client with Local Auth to persist session
const client = new Client({
    authStrategy: new LocalAuth({
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

let isReady = false;
let reconnecting = false;

// Robust disconnect and reconnection handler
async function handleDisconnect(reason) {
    if (reconnecting) return;
    reconnecting = true;
    console.log('\n====================================================================');
    console.log(`Menangani pemutusan koneksi WhatsApp. Alasan: ${reason}`);
    console.log('====================================================================\n');
    
    isReady = false;
    latestQrDataUrl = null;
    
    try {
        console.log('Mencoba menutup sesi browser Puppeteer sebelumnya...');
        await client.destroy();
    } catch (err) {
        console.error('Gagal destroy client (mungkin browser sudah tertutup):', err.message);
    }
    
    console.log('Menunggu 5 detik sebelum menginisialisasi ulang...');
    setTimeout(() => {
        reconnecting = false;
        console.log('Menginisialisasi ulang WhatsApp Client...');
        client.initialize().catch(err => {
            console.error('Gagal menginisialisasi ulang client:', err.message);
        });
    }, 5000);
}

// Global process exception handlers to prevent Node.js from crashing
process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception Global Terdeteksi:', err);
    if (err.message && (err.message.includes('Session closed') || err.message.includes('detached') || err.message.includes('Protocol error'))) {
        console.warn('Mendeteksi error protokol Puppeteer. Memulai pemulihan otomatis...');
        handleDisconnect('Uncaught exception: ' + err.message);
    }
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Promise Rejection Terdeteksi:', reason);
    const msg = (reason && reason.message) || String(reason);
    if (msg.includes('Session closed') || msg.includes('detached') || msg.includes('Protocol error')) {
        console.warn('Mendeteksi error protokol Puppeteer di promise. Memulai pemulihan otomatis...');
        handleDisconnect('Unhandled rejection: ' + msg);
    }
});

// Heartbeat check to monitor client health every 30 seconds
setInterval(async () => {
    if (isReady) {
        try {
            const state = await client.getState();
            console.log(`[Heartbeat] Status WhatsApp Client: ${state}`);
            if (state !== 'CONNECTED') {
                console.warn('[Heartbeat] Status tidak CONNECTED. Memicu re-koneksi...');
                handleDisconnect('State mismatch: ' + state);
            }
        } catch (error) {
            console.error('[Heartbeat] Gagal mendapatkan state client (browser mungkin hang/crash):', error.message);
            handleDisconnect('Heartbeat check failed: ' + error.message);
        }
    }
}, 30000);

// Event: QR Code generation for scanning
client.on('qr', (qr) => {
    console.log('\n====================================================================');
    console.log('SCAN QR CODE DI BAWAH INI DENGAN APLIKASI WHATSAPP DI HP ANDA:');
    console.log('====================================================================\n');
    qrcode.generate(qr, { small: true });
    console.log('\n====================================================================\n');

    // Generate base64 Data URL for web dashboard
    QRCodeGen.toDataURL(qr, (err, url) => {
        if (!err) {
            latestQrDataUrl = url;
        }
    });
});

// Event: Client successfully authenticated and loaded
client.on('ready', () => {
    console.log('\n====================================================================');
    console.log('WhatsApp Client siap digunakan dan terhubung!');
    console.log('====================================================================\n');
    isReady = true;
    latestQrDataUrl = null; // Clear QR URL when ready
});

client.on('auth_failure', (msg) => {
    console.error('Gagal autentikasi WhatsApp:', msg);
    latestQrDataUrl = null;
    isReady = false;
    handleDisconnect('Authentication failure');
});

client.on('disconnected', (reason) => {
    console.log('Koneksi WhatsApp terputus (Event):', reason);
    handleDisconnect(reason);
});

// Initialize client connection
client.initialize();

// Endpoint: Check status
app.get('/status', (req, res) => {
    res.json({ ready: isReady, qr: latestQrDataUrl });
});

// Endpoint: Send text message
app.post('/send-message', async (req, res) => {
    const { phone, message } = req.body;
    
    if (!isReady) {
        return res.status(503).json({ error: 'WhatsApp client belum siap / terhubung.' });
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

        const response = await client.sendMessage(formattedPhone, message);
        res.json({ success: true, messageId: response.id.id });
    } catch (error) {
        console.error('Gagal mengirim pesan:', error);
        res.status(500).json({ error: error.message });
        if (error.message && (error.message.includes('Protocol error') || error.message.includes('Session closed') || error.message.includes('detached') || error.message.includes('context was destroyed') || error.message.includes('Target closed'))) {
            handleDisconnect('Error during sendMessage: ' + error.message);
        }
    }
});

// Endpoint: Send document file (PDF, etc.)
app.post('/send-document', async (req, res) => {
    const { phone, fileUrl, filename, caption } = req.body;

    if (!isReady) {
        return res.status(503).json({ error: 'WhatsApp client belum siap / terhubung.' });
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

        console.log(`Mengunduh file dari URL: ${fileUrl}`);
        
        // Fetch the file as an arraybuffer
        const fileResponse = await axios.get(fileUrl, { 
            responseType: 'arraybuffer',
            timeout: 15000 // 15 seconds timeout
        });
        
        const mimeType = fileResponse.headers['content-type'] || 'application/pdf';
        const base64Data = Buffer.from(fileResponse.data, 'binary').toString('base64');
        
        const media = new MessageMedia(mimeType, base64Data, filename);
        
        console.log(`Mengirim berkas dokumen ke ${formattedPhone}...`);
        const response = await client.sendMessage(formattedPhone, media, { 
            caption: caption || '' 
        });
        
        res.json({ success: true, messageId: response.id.id });
    } catch (error) {
        console.error('Gagal mengirim dokumen:', error);
        res.status(500).json({ error: error.message });
        if (error.message && (error.message.includes('Protocol error') || error.message.includes('Session closed') || error.message.includes('detached') || error.message.includes('context was destroyed') || error.message.includes('Target closed'))) {
            handleDisconnect('Error during sendDocument: ' + error.message);
        }
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`\nWhatsApp Gateway API berjalan pada port ${PORT}`);
    console.log(`Status check: http://localhost:${PORT}/status\n`);
});
