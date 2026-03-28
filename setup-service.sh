#!/bin/bash
# Creates and enables the telegram-handler systemd service.
# Run as root: sudo bash setup-service.sh

set -e

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
PHP="/usr/bin/php8.4"
LOG="$APP_DIR/storage/logs/telegram.log"
SERVICE="telegram-handler"

cat > /etc/systemd/system/$SERVICE.service << EOF
[Unit]
Description=Telegram Update Handler
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=$APP_DIR
ExecStart=$PHP $APP_DIR/artisan telegram:handle
Restart=no
StandardOutput=append:$LOG
StandardError=append:$LOG
KillSignal=SIGTERM
TimeoutStopSec=30

[Install]
WantedBy=multi-user.target
EOF

cat > /etc/sudoers.d/www-data-telegram << 'EOF'
www-data ALL=(root) NOPASSWD: /usr/bin/systemctl start telegram-handler, /usr/bin/systemctl stop telegram-handler, /usr/bin/systemctl restart telegram-handler
EOF
chmod 0440 /etc/sudoers.d/www-data-telegram

systemctl daemon-reload
systemctl enable $SERVICE
systemctl start $SERVICE

echo "Done. Status:"
systemctl status $SERVICE --no-pager
