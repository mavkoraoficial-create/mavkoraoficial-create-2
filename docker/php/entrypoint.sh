#!/bin/sh

echo "==================================="
echo "MAVKORA CONTAINER STARTING"
echo "==================================="

cd /var/www

if [ ! -f .env ]; then
    cp .env.docker .env
fi

if [ ! -d vendor ]; then
    composer install
fi

php artisan key:generate --force || true

php artisan storage:link || true

php artisan optimize:clear

php artisan migrate --force || true

exec "$@"