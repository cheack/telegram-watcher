# Stage 1: Build frontend assets
FROM node:22-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources/ resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY public/ public/

RUN npm run build

# Stage 2: PHP production image
# serversideup/php includes igbinary, pgsql, pcntl, intl and other extensions pre-built
FROM serversideup/php:8.4-fpm-alpine AS production

USER root

# Install supervisor
RUN apk add --no-cache supervisor

# Install igbinary (required by MadelineProto) via pre-built binary
RUN install-php-extensions igbinary

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev, optimized autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy application code
COPY . .

# Copy built frontend assets from node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Clear stale bootstrap cache (may contain dev-only service providers from host)
RUN rm -f bootstrap/cache/*.php

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy supervisor config and startup script
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 9000

CMD ["/start.sh"]
