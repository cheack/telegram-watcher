#!/bin/sh
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "SomeRandomString" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear stale bootstrap cache (may contain dev-only service providers)
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php \
      bootstrap/cache/config.php bootstrap/cache/routes-v7.php \
      bootstrap/cache/events.php bootstrap/cache/views.php

# Sync built frontend assets into the shared volume for nginx
cp -r /var/www/html/public/build_dist/. /var/www/html/public/build/

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Cache configuration for production
if [ "$APP_ENV" = "production" ]; then
    php artisan package:discover --ansi
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Start supervisord (manages php-fpm and telegram-handler)
echo "Starting supervisord..."
exec supervisord -c /etc/supervisor/supervisord.conf
