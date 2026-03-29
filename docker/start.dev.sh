#!/bin/sh
set -e

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "SomeRandomString" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

rm -f bootstrap/cache/*.php

if [ ! -f vendor/autoload.php ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction
fi

echo "Running migrations..."
php artisan migrate

(
    sleep 5
    SESSIONS=$(php -r "
        \$s = json_decode(file_get_contents('/var/www/html/settings.json'), true);
        echo count(\$s['telegram']['sessions'] ?? []);
    " 2>/dev/null || echo 0)
    if [ "$SESSIONS" -gt "0" ]; then
        echo "Auto-starting telegram-handler ($SESSIONS session(s))..."
        supervisorctl -c /etc/supervisor/supervisord.conf start telegram-handler
    fi
) &

echo "Starting supervisord..."
exec supervisord -c /etc/supervisor/supervisord.conf
