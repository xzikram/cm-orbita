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
            '--disable-gpu'
        ]
    }
});

let isReady = false;

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
});

client.on('disconnected', (reason) => {
    console.log('Koneksi WhatsApp terputus:', reason);
    isReady = false;
    latestQrDataUrl = null;
    // Re-initialize client
    client.initialize();
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
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`\nWhatsApp Gateway API berjalan pada port ${PORT}`);
    console.log(`Status check: http://localhost:${PORT}/status\n`);
});
