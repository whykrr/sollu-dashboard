#!/bin/sh
set -e

# Pastikan folder storage dan bootstrap cache ada
mkdir -p /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/logs/query \
         /var/www/html/storage/app/public \
         /var/www/html/storage/app/private/exports \
         /var/www/html/storage/app/private/imports \
         /var/www/html/bootstrap/cache

# Sesuaikan permission storage & bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Pastikan symlink public/storage ada
if [ ! -L /var/www/html/public/storage ]; then
    ln -s /var/www/html/storage/app/public /var/www/html/public/storage || true
fi

# Jalankan command (default supervisord)
exec "$@"
