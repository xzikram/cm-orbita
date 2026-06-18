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
composer install --no-dev --optimize-autoloader

# 4. Jalankan migrasi database
echo "👉 Menjalankan migrasi database..."
php artisan migrate --force

# 5. Bersihkan dan bangun ulang cache Laravel
echo "👉 Membersihkan dan mengoptimalkan cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Perbarui dependensi WhatsApp Gateway & Restart Layanan
if [ -d "whatsapp-gateway" ]; then
    echo "👉 Memperbarui dependensi WhatsApp Gateway..."
    cd whatsapp-gateway
    npm install --no-audit --no-fund
    
    echo "👉 Merestart WhatsApp Gateway di background (PM2)..."
    pm2 restart whatsapp-gateway
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
