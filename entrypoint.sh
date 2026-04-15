#!/bin/sh
set -e

# Mantém apenas um MPM ativo
a2dismod mpm_worker mpm_event 2>/dev/null || true

cd /var/www/html

# Estrutura necessária do Laravel
mkdir -p \
    storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    bootstrap/cache

# Garante arquivo de log
touch storage/logs/laravel.log || true

# Permissões
chown -R www-data:www-data storage bootstrap/cache public || true
chmod -R 775 storage bootstrap/cache public || true
chmod 664 storage/logs/laravel.log || true

# Gera .env se não existir
if [ ! -f .env ]; then
    echo "INFO: .env not found — generating from environment variables."

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
DB_SCHEMA=${DB_SCHEMA:-public}

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

# Reforça storage link sempre no start
rm -rf public/storage || true
php artisan storage:link || true

# Limpa caches
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Migrações
php artisan migrate --force || true

exec "$@"
