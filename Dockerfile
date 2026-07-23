# Stage 1: Build frontend assets
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources/ resources/
RUN npm run build

# Stage 2: PHP + Nginx production image
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    libpq-dev \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy application code
COPY . /app

# Copy built frontend assets
COPY --from=frontend /app/public/build /app/public/build

# Install composer dependencies (production only)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Setup directories and permissions
RUN mkdir -p /app/bootstrap/cache \
    && mkdir -p /app/storage/framework/cache \
    && mkdir -p /app/storage/framework/sessions \
    && mkdir -p /app/storage/framework/views \
    && mkdir -p /app/storage/logs \
    && chmod -R 775 /app/bootstrap/cache \
    && chmod -R 775 /app/storage \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copy start script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
