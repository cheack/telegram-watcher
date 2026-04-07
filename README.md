# Telegram Watcher

A self-hosted web application for monitoring Telegram accounts and channels. It detects events (channel joins/leaves, messages from unknown contacts) and forwards notifications to a designated Telegram group.

## Features

- Monitor multiple Telegram accounts simultaneously via [MadelineProto](https://docs.madelineproto.xyz/)
- Detect channel joins/leaves and messages from unknown contacts
- Forward real-time notifications to a Telegram group via bot
- Web dashboard for managing tracked accounts and sessions
- Service control panel (start/stop/restart the event handler)
- Live log viewer with per-session filtering
- Telegram bot commands for remote management
- Dark/light theme, multi-language support

## Requirements

- Docker and Docker Compose
- A Telegram account and app credentials from [my.telegram.org](https://my.telegram.org)
- A Telegram bot token (from [@BotFather](https://t.me/BotFather))

## Quick Start (Docker)

### 1. Clone the repository

```bash
git clone <repo-url> telegram-watcher
cd telegram-watcher
```

### 2. Configure environment

```bash
cp .env.example .env
```

Open `.env` and fill in the required values:

```env
# Generate a secure random string (e.g. openssl rand -base64 32)
APP_KEY=

# PostgreSQL credentials — choose any values, they will be created automatically
DB_DATABASE=watcher
DB_USERNAME=watcher
DB_PASSWORD=your_secure_password

# Your Telegram app credentials from https://my.telegram.org
TELEGRAM_APP_ID=
TELEGRAM_APP_HASH=

# Telegram bot that sends notifications (token from @BotFather)
TELEGRAM_NOTIFY_BOT_TOKEN=

# ID of the group where notifications will be sent (negative number for groups)
TELEGRAM_NOTIFY_GROUP_ID=

# Your app's public URL + bot token (needed to set up the webhook)
# Example: https://example.com/telegram/webhook/<token>
TELEGRAM_NOTIFY_BOT_WEBHOOK_URL=

# Telescope admin email (used to restrict access to /telescope)
TELESCOPE_ADMIN_EMAIL=
```

> If `APP_KEY` is left empty, it will be generated automatically on first start.

### 3. Start the application

```bash
docker compose up -d --build
```

The app will be available at [http://localhost](http://localhost).

### 4. Create your user account

Open the app in your browser and register a new account.

### 5. Log in to Telegram sessions

Each monitored Telegram account requires an interactive login via CLI:

```bash
docker compose exec app php artisan telegram:login <session-name>
```

Follow the prompts — enter the phone number, the code sent to Telegram, and 2FA password if enabled. Sessions are stored in `storage/telegram_sessions/`.

### 6. Set up the webhook

```bash
docker compose exec app php artisan telegram:webhook set
```

### 7. Start the event handler

Open the **Service** page in the web UI and click **Start**, or run:

```bash
docker compose exec app php artisan telegram:manage --start
```

## Telegram Bot Commands

Once the webhook is set up, you can control the handler directly from the notification group:

| Command | Description |
|---|---|
| `/handler_get_status` | Show handler status and recent logs |
| `/handler_start` | Start the event handler |
| `/handler_stop` | Stop the event handler |
| `/handler_restart` | Restart the event handler |
| `/test [message]` | Send a test message to the group |
| `/test_notify [message]` | Send a test notification |

## Tracked Accounts

From the dashboard, you can add Telegram accounts to monitor:

- **Bot ID** — The Telegram user ID of the session's account
- **Account ID** — The target account to monitor
- **Account Name** — A friendly label
- **Notes** — Optional notes
- **Trusted Resources** — Channels/contacts to exclude from notifications

## CLI Reference

```bash
# Log in to a Telegram account
php artisan telegram:login <session-name>

# Log out from a session
php artisan telegram:logout <session-name>

# Manage the background event handler
php artisan telegram:manage --start
php artisan telegram:manage --stop
php artisan telegram:manage --restart
php artisan telegram:manage --status

# Webhook management
php artisan telegram:webhook info
php artisan telegram:webhook set

# Send a test notification
php artisan telegram:test
```

## Configuration Reference

| Variable | Description |
|---|---|
| `APP_KEY` | Laravel encryption key (auto-generated if empty) |
| `APP_PORT` | Port to expose the app on (default: `80`) |
| `DB_DATABASE` | PostgreSQL database name |
| `DB_USERNAME` | PostgreSQL username |
| `DB_PASSWORD` | PostgreSQL password |
| `TELEGRAM_APP_ID` | App ID from [my.telegram.org](https://my.telegram.org) |
| `TELEGRAM_APP_HASH` | App hash from [my.telegram.org](https://my.telegram.org) |
| `TELEGRAM_NOTIFY_BOT_TOKEN` | Bot token from @BotFather |
| `TELEGRAM_NOTIFY_GROUP_ID` | Target group ID for notifications |
| `TELEGRAM_NOTIFY_BOT_WEBHOOK_URL` | Public webhook URL for the bot |
| `TELEGRAM_LOGGER_BOT_TOKEN` | Bot token for logging (can be same as notify bot) |
| `TELEGRAM_LOGGER_CHAT_ID` | Chat ID for log messages |
| `TELESCOPE_ADMIN_EMAIL` | Email that can access `/telescope` debug panel |

## Data Persistence

Docker volumes used:

- `postgres_data` — PostgreSQL database files
- `storage` — Telegram sessions, logs, cache, framework files

To back up your sessions and database:

```bash
# Backup database
docker compose exec postgres pg_dump -U $DB_USERNAME $DB_DATABASE > backup.sql

# Backup storage volume (includes Telegram sessions)
docker run --rm -v telegram-watcher_storage:/data -v $(pwd):/backup alpine \
  tar czf /backup/storage-backup.tar.gz /data
```

## Tech Stack

- [Laravel 13](https://laravel.com/) — PHP framework
- [MadelineProto](https://docs.madelineproto.xyz/) — Telegram client library
- [Telegram Bot SDK](https://telegram-bot-sdk.readme.io/) — Bot API
- PostgreSQL — Database
- Nginx — Web server
- Alpine.js + Tailwind CSS — Frontend
