#!/usr/bin/env bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f .env ]; then
  cp .env.example .env
fi

php artisan key:generate --force || true
php artisan storage:link || true
php artisan migrate --force || true

exec "$@"
