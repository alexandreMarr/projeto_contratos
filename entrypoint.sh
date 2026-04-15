#!/bin/sh
set -e

# Ensure only mpm_event is active — disable all MPMs first, then re-enable
# mpm_event. This guards against the base image re-enabling a second MPM
# at runtime, which causes "More than one MPM loaded" and crashes Apache.
a2dismod mpm_prefork mpm_worker mpm_event 2>/dev/null || true
a2enmod mpm_event 2>/dev/null || true

cd /var/www/html

mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

php artisan config:clear || true
php artisan cache:clear || true

php artisan migrate --force || true

exec "$@"
