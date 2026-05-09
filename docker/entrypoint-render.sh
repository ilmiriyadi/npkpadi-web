#!/bin/bash
set -e

# =============================================================================
# Entrypoint untuk Render.com
# =============================================================================
# - Baca DATABASE_URL dari Render env → tulis ke .env
# - Generate APP_KEY jika belum ada
# - Run migrations
# - Cache config (production)
# - Jalankan Apache di $PORT
# =============================================================================

cd /var/www/html

echo "[render] ========================================"
echo "[render] NPK Padi — Laravel Startup"
echo "[render] ========================================"

# ── 1. Buat .env dari environment variables Render ──────────────────────────
echo "[render] Membuat .env..."
cat > .env << EOF
APP_NAME="${APP_NAME:-NPK Padi}"
APP_ENV=production
APP_KEY=${APP_KEY:-}
APP_DEBUG=false
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=stderr
LOG_LEVEL=warning

# PostgreSQL dari Render (DATABASE_URL otomatis diset oleh Render PostgreSQL addon)
DB_CONNECTION=pgsql
DATABASE_URL=${DATABASE_URL:-}

SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local

# Sync token untuk Raspberry Pi
SYNC_API_TOKEN=${SYNC_API_TOKEN:-changeme}
SYNC_PI_USER_ID=${SYNC_PI_USER_ID:-1}
EOF

# ── 2. Generate APP_KEY jika belum ada ──────────────────────────────────────
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "[render] Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── 3. Parse DATABASE_URL → config database Laravel ────────────────────────
# Render set DATABASE_URL=postgresql://user:pass@host:port/dbname
# Laravel bisa baca langsung dari DATABASE_URL jika DB_CONNECTION=pgsql
# Tapi kita parse manual supaya lebih eksplisit
if [ -n "$DATABASE_URL" ]; then
    echo "[render] DATABASE_URL ditemukan, parsing..."
    # Format: postgresql://USER:PASS@HOST:PORT/DBNAME
    DB_USER=$(echo "$DATABASE_URL" | sed -E 's|postgresql://([^:]+):.*|\1|')
    DB_PASS=$(echo "$DATABASE_URL" | sed -E 's|postgresql://[^:]+:([^@]+)@.*|\1|')
    DB_HOST=$(echo "$DATABASE_URL" | sed -E 's|.*@([^:]+):.*|\1|')
    DB_PORT=$(echo "$DATABASE_URL" | sed -E 's|.*:([0-9]+)/.*|\1|')
    DB_NAME=$(echo "$DATABASE_URL" | sed -E 's|.*/([^?]+).*|\1|')

    # Tambahkan ke .env secara eksplisit
    echo "" >> .env
    echo "DB_HOST=${DB_HOST}" >> .env
    echo "DB_PORT=${DB_PORT:-5432}" >> .env
    echo "DB_DATABASE=${DB_NAME}" >> .env
    echo "DB_USERNAME=${DB_USER}" >> .env
    echo "DB_PASSWORD=${DB_PASS}" >> .env

    echo "[render] DB: ${DB_USER}@${DB_HOST}:${DB_PORT:-5432}/${DB_NAME}"
else
    echo "[render] WARN: DATABASE_URL tidak ditemukan!"
fi

# ── 4. Clear cache lama ─────────────────────────────────────────────────────
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# ── 5. Run migrations ───────────────────────────────────────────────────────
echo "[render] Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || {
    echo "[render] WARN: Migration gagal (mungkin DB belum siap), lanjut..."
}

# ── 6. Storage symlink ──────────────────────────────────────────────────────
if [ ! -L public/storage ]; then
    echo "[render] Creating storage symlink..."
    php artisan storage:link --force 2>/dev/null || true
fi

# ── 7. Cache untuk production ────────────────────────────────────────────────
echo "[render] Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 8. Permissions ──────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# ── 9. Set Apache port dari $PORT (Render default: 10000) ───────────────────
export PORT="${PORT:-10000}"
echo "[render] Starting Apache on port ${PORT}..."

# Update Apache ports.conf dengan PORT yang aktual
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Update VirtualHost port
sed -i "s/<VirtualHost \*:[^>]*>/<VirtualHost *:${PORT}>/" \
    /etc/apache2/sites-available/000-default.conf

echo "[render] Ready! 🌾"

# Jalankan command yang diteruskan (apache2-foreground)
exec "$@"
