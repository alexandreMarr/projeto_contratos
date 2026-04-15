#!/bin/sh
set -e

# Ensure only mpm_prefork is active — disable threaded MPMs to prevent the
# "More than one MPM loaded" crash and the thread-safety conflict with the
# php:8.2-apache image, which ships PHP compiled for mpm_prefork (non-threaded).
a2dismod mpm_worker mpm_event 2>/dev/null || true

cd /var/www/html

mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# ---------------------------------------------------------------------------
# Generate .env from Railway environment variables if it does not exist.
# Railway exposes Postgres credentials as PG* variables; we map them to the
# DB_* names that Laravel expects.
# ---------------------------------------------------------------------------
if [ ! -f .env ]; then
    echo "INFO: .env not found — generating from environment variables."

    # Derive APP_KEY: use the env var if already set, otherwise generate one.
    if [ -z "${APP_KEY}" ]; then
        GENERATED_KEY="base64:$(head -c 32 /dev/urandom | base64)"
    else
        GENERATED_KEY="${APP_KEY}"
    fi

    cat > .env <<EOF
APP_NAME=${APP_NAME:-Laravel}
APP_ENV=${APP_ENV:-production}
APP_KEY=${GENERATED_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-${PGHOST:-127.0.0.1}}
DB_PORT=${DB_PORT:-${PGPORT:-5432}}
DB_DATABASE=${DB_DATABASE:-${PGDATABASE:-laravel}}
DB_USERNAME=${DB_USERNAME:-${PGUSER:-postgres}}
DB_PASSWORD=${DB_PASSWORD:-${PGPASSWORD:-}}

BROADCAST_DRIVER=${BROADCAST_DRIVER:-log}
CACHE_DRIVER=${CACHE_DRIVER:-file}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-public}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-127.0.0.1}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME:-}
MAIL_PASSWORD=${MAIL_PASSWORD:-}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-null}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-hello@example.com}
MAIL_FROM_NAME="${APP_NAME:-Laravel}"
EOF

    echo "INFO: .env generated successfully."
fi

php artisan config:clear || true
php artisan cache:clear || true

php artisan migrate --force || true

exec "$@"
