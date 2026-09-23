#!/bin/bash
# ==============================================================================
# Script Otomatis Deployment Cepat & Zero-Downtime untuk CFMS
# ==============================================================================
# Penggunaan:
#   ./deploy.sh                 -> Deploy cepat standar (< 5 detik)
#   ./deploy.sh --restart-wa    -> Deploy & paksa restart WhatsApp Gateway
#   ./deploy.sh --build         -> Deploy & paksa compile ulang Vite (Frontend)
#   ./deploy.sh --seed          -> Deploy & jalankan database seeder
# ==============================================================================

set -e

FORCE_RESTART_WA=false
REBUILD_ASSETS=false
RUN_SEEDER=false

for arg in "$@"; do
    case $arg in
        --restart-wa) FORCE_RESTART_WA=true ;;
        --build)      REBUILD_ASSETS=true ;;
        --seed)       RUN_SEEDER=true ;;
    esac
done

echo "==============================================="
echo "  MEMULAI UPDATE SISTEM CEPAT (DEPLOYMENT)     "
echo "==============================================="

# 1. Masuk ke folder root proyek
cd /var/www/clinical-system || exit 1

# 2. Rekam commit lama dan ambil pembaruan terbaru dari Git
echo "👉 Mengunduh pembaruan dari Git..."
PREV_COMMIT=$(git rev-parse HEAD 2>/dev/null || echo "")
git fetch origin main
git reset --hard origin/main
NEW_COMMIT=$(git rev-parse HEAD)

# 3. Pasang dependency PHP Composer HANYA jika composer.lock berubah
if [ -n "$PREV_COMMIT" ] && git diff --name-only "$PREV_COMMIT" "$NEW_COMMIT" 2>/dev/null | grep -q "composer.lock"; then
    echo "👉 Memperbarui dependensi Composer (PHP)..."
    export COMPOSER_ALLOW_SUPERUSER=1
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "👉 Dependensi Composer tidak berubah (dilewati)."
fi

# 4. Jalankan migrasi database
echo "👉 Menjalankan migrasi database..."
php artisan migrate --force

if [ "$RUN_SEEDER" = true ]; then
    echo "👉 Menjalankan seeder..."
    php artisan db:seed --class=RolePermissionSeeder --force
    php artisan db:seed --class=MasterDataSeeder --force
fi

# 5. Aset Frontend (Vite)
# Karena aset public/build sudah dikompilasi dan ada di Git, kita tidak perlu
# menjalankan npm install dan npm run build yang berat pada server produksi
if [ "$REBUILD_ASSETS" = true ] || [ ! -f "public/build/manifest.json" ]; then
    echo "👉 Mengompilasi aset frontend (Vite)..."
    npm install --no-audit --no-fund
    npm run build
else
    echo "👉 Aset frontend sudah siap dari Git (dilewati)."
fi

# 6. Bersihkan dan optimalkan cache Laravel
echo "👉 Mengoptimalkan cache Laravel..."
php artisan optimize:clear
php artisan optimize

# 7. WhatsApp Gateway (Smart Lifecycle)
# Jangan restart WhatsApp jika tidak ada perubahan kode di folder gateway!
# Ini menjaga koneksi WhatsApp tetap AKTIF & tidak membebani CPU/Chromium.
WA_CHANGED=false
if [ -n "$PREV_COMMIT" ] && git diff --name-only "$PREV_COMMIT" "$NEW_COMMIT" 2>/dev/null | grep -q "^whatsapp-gateway/"; then
    WA_CHANGED=true
fi

WA_RUNNING=false
if pm2 list 2>/dev/null | grep -q "whatsapp-gateway.*online"; then
    WA_RUNNING=true
fi

if [ "$FORCE_RESTART_WA" = true ] || [ "$WA_RUNNING" = false ] || [ "$WA_CHANGED" = true ]; then
    echo "👉 Mengupdate dan me-restart WhatsApp Gateway..."
    cd whatsapp-gateway || exit 1
    if [ "$WA_CHANGED" = true ]; then
        npm install --no-audit --no-fund
    fi
    pm2 restart whatsapp-gateway 2>/dev/null || pm2 start server.js --name "whatsapp-gateway"
    cd ..
else
    echo "👉 WhatsApp Gateway sudah berjalan online & tidak ada perubahan kode."
    echo "   (Sesi koneksi WhatsApp tetap dipertahankan AKTIF)."
fi

# 8. Restart Queue Worker (Supervisor)
echo "👉 Merestart Queue Worker (Supervisor)..."
sudo supervisorctl restart laravel-worker:* 2>/dev/null || true

# 9. Reload PHP-FPM secara graceful agar perubahan PHP langsung aktif
echo "👉 Me-reload PHP-FPM..."
for sock in php8.3-fpm php8.2-fpm php8.1-fpm php-fpm; do
    if systemctl is-active --quiet "$sock" 2>/dev/null; then
        sudo systemctl reload "$sock" 2>/dev/null || true
        break
    fi
done

# 10. Perbaiki perizinan berkas (HANYA folder storage dan bootstrap/cache)
echo "👉 Memperbaiki izin folder storage & cache..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "==============================================="
echo "  ✅ DEPLOYMENT SELESAI DALAM BEBERAPA DETIK!  "
echo "==============================================="
