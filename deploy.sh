#!/bin/bash
cd "$(dirname "$0")"
git pull
chown -R www-data:www-data ./
chown -R 0774 storage
composer install --no-dev
composer dump-auto --apcu
yarn install
php artisan optimize