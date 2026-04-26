# =============================================================================
# Dockerfile — Laravel Web App (VPS)
# =============================================================================
# Multi-stage build:
#   Stage 1: composer — install PHP dependencies
#   Stage 2: node    — build Vite/Tailwind assets
#   Stage 3: final   — PHP-FPM runtime
#
# Dipakai bersama docker-compose.yml + Nginx
# =============================================================================

# ── Stage 1: Composer dependencies ──
FROM composer:2 AS composer
WORKDIR /build
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ── Stage 2: Node.js — build frontend assets ──
FROM node:20-alpine AS node
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=composer /build/vendor ./vendor
RUN npm run build

# ── Stage 3: PHP-FPM runtime ──
FROM php:8.2-fpm-bookworm

LABEL maintainer="NPK Padi — Politeknik Negeri Banjarmasin"
LABEL description="Laravel Web App — Sistem Deteksi Defisiensi Nutrisi Daun Padi"

# Install PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libsqlite3-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    curl \
    unzip \
    && docker-php-ext-install \
        pdo_sqlite \
        pdo_mysql \
        mbstring \
        xml \
        bcmath \
        gd \
        zip \
        curl \
        opcache \
    && rm -rf /var/lib/apt/lists/*

# OPcache config for production
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=10000\n\
opcache.validate_timestamps=0\n\
opcache.save_comments=1\n\
opcache.fast_shutdown=1" > /usr/local/etc/php/conf.d/opcache.ini

# Upload size config
RUN echo "upload_max_filesize=20M\n\
post_max_size=25M\n\
memory_limit=256M" > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

# Copy vendor from composer stage
COPY --from=composer /build/vendor ./vendor

# Copy built assets from node stage
COPY --from=node /build/public/build ./public/build

# Copy application code
COPY . .

# Create required directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    storage/app/public/detections \
    bootstrap/cache \
    database

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
