#!/bin/bash
set -e

# =============================================================================
# Laravel Docker Entrypoint
# =============================================================================
# Runs one-time setup tasks before starting PHP-FPM:
#   - Create .env if not exists
#   - Generate app key if not set
#   - Create SQLite database if using SQLite
#   - Run migrations
#   - Create storage symlink
#   - Clear & cache config
# =============================================================================

cd /var/www/html

# 1. Create .env from example if not mounted
if [ ! -f .env ]; then
    echo "[entrypoint] .env not found, copying from .env.example..."
    cp .env.example .env
fi

# 2. Generate app key if empty
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "[entrypoint] Generating APP_KEY..."
    php artisan key:generate --force
fi

# 3. Create SQLite database if using SQLite
if grep -q "^DB_CONNECTION=sqlite" .env; then
    DB_FILE="database/database.sqlite"
    if [ ! -f "$DB_FILE" ]; then
        echo "[entrypoint] Creating SQLite database..."
        touch "$DB_FILE"
        chown www-data:www-data "$DB_FILE"
    fi
fi

# 4. Run migrations
echo "[entrypoint] Running migrations..."
php artisan migrate --force --no-interaction 2>/dev/null || true

# 5. Storage link
if [ ! -L public/storage ]; then
    echo "[entrypoint] Creating storage symlink..."
    php artisan storage:link --force 2>/dev/null || true
fi

# 6. Cache config & routes for production
if [ "$APP_ENV" = "production" ]; then
    echo "[entrypoint] Caching config & routes..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan config:clear 2>/dev/null || true
fi

# 7. Fix permissions
chown -R www-data:www-data storage bootstrap/cache database 2>/dev/null || true

echo "[entrypoint] Ready!"
exec "$@"
