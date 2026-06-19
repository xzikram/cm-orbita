#!/bin/bash
# Script Otomatis Deployment untuk CFMS

# PENTING: Jalankan script ini sebagai user biasa yang memiliki akses sudo (misal root atau user dengan hak sudo)
# Jalankan dengan perintah: ./deploy.sh

echo "==============================================="
echo "  MEMULAI UPDATE SISTEM (DEPLOYMENT)           "
echo "==============================================="

# 1. Masuk ke folder root proyek
cd /var/www/clinical-system || exit

# 2. Ambil perubahan terbaru dari Git
echo "👉 Mengunduh pembaruan dari Git..."
git pull origin main

# 3. Pasang dependency PHP baru (jika ada)
echo "👉 Memperbarui dependensi Composer (PHP)..."
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Jalankan migrasi database & seeding
echo "👉 Menjalankan migrasi database..."
php artisan migrate --force
echo "👉 Menjalankan seeder role & permission..."
php artisan db:seed --class=RolePermissionSeeder --force
echo "👉 Menjalankan seeder data master..."
php artisan db:seed --class=MasterDataSeeder --force

# 5. Pasang dependensi Node.js & Compile aset frontend (Vite)
echo "👉 Memasang dependensi & mengompilasi aset frontend (Vite)..."
npm install --no-audit --no-fund
npm run build

# 5. Bersihkan dan bangun ulang cache Laravel
echo "👉 Membersihkan dan mengoptimalkan cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Perbarui dependensi WhatsApp Gateway & Restart Layanan secara Bersih
if [ -d "whatsapp-gateway" ]; then
    echo "👉 Memperbarui dependensi WhatsApp Gateway..."
    cd whatsapp-gateway
    npm install --no-audit --no-fund
    
    echo "👉 Langkah 1: Matikan semua proses terkait sepenuhnya..."
    pm2 delete whatsapp-gateway 2>/dev/null || true
    kill -9 $(pgrep -f "node.*server.js") 2>/dev/null || true
    kill -9 $(pgrep -f chromium) 2>/dev/null || true
    kill -9 $(pgrep -f chrome) 2>/dev/null || true
    
    echo "👉 Langkah 2: Memastikan port 3000 benar-benar kosong..."
    if command -v fuser >/dev/null 2>&1; then
        fuser -k 3000/tcp 2>/dev/null || true
    fi
    
    # Jangan menghapus seluruh folder .wwebjs_auth agar sesi login multi-user tidak terputus.
    # Jika ada sesi spesifik yang bermasalah, gunakan tombol "Reset Koneksi" di UI masing-masing pengguna.
    echo "👉 Langkah 3: Melewati pembersihan sesi (sesi dipertahankan)..."
    
    echo "👉 Langkah 4: Jalankan kembali dengan PM2..."
    pm2 start server.js --name "whatsapp-gateway"
    cd ..
fi

# 7. Restart Laravel Queue Worker (Supervisor)
echo "👉 Merestart Queue Worker (Supervisor)..."
sudo supervisorctl restart laravel-worker:*

# 8. Perbaiki perizinan berkas (Permission) agar tidak terjadi error log
echo "👉 Memperbaiki izin berkas (Permissions) untuk Nginx..."
sudo chown -R www-data:www-data /var/www/clinical-system
sudo find /var/www/clinical-system -type f -exec chmod 644 {} \;
sudo find /var/www/clinical-system -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/clinical-system/storage
sudo chmod -R 775 /var/www/clinical-system/bootstrap/cache

echo "==============================================="
echo "  DEPLOYMENT SELESAI DENGAN SUKSES!            "
echo "==============================================="
