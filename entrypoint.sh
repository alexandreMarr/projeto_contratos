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

php artisan config:clear || true
php artisan cache:clear || true

php artisan migrate --force || true

exec "$@"
