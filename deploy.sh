#!/bin/bash
cd "$(dirname "$0")" || exit
git pull
chown -R www-data:www-data ./
chown -R 0774 storage
composer install -o --no-dev
composer dump-auto --apcu
yarn install
php artisan optimize
