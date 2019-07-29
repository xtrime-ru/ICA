#!/bin/bash
cd "$(dirname "$0")"
git pull
chown -R www-data:www-data ./
chown -R 0774 storage
composer install -o --no-dev
yarn install
php artisan optimize