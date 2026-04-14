#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

php artisan config:clear || true
php artisan cache:clear || true

php artisan migrate --force || true

exec "$@"
