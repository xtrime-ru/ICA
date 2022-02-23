#!/bin/bash
cd "$(dirname "$0")" || exit
git pull
chown -R www-data:www-data ./
chown -R 0774 storage
docker-compose restart npm
docker-compose restart ica