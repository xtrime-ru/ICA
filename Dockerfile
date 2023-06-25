FROM php:8.2-cli

RUN apt-get update && apt-get upgrade -y

RUN  apt-get install apt-utils -y \
#
#    устанавливаем необходимые пакеты
    && apt-get install git zip vim libzip-dev  -y \
#
#    Включаем необходимые расширения
    && docker-php-ext-install -j$(nproc) zip pcntl bcmath pdo_mysql \
#
#    Расшерения через pecl ставятся так,
#    && PHP_OPENSSL=yes pecl install ev \
#    && docker-php-ext-enable ev \
#
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
#
#    Чистим временные файлы
    && docker-php-source delete \
    && apt-get autoremove --purge -y && apt-get autoclean -y && apt-get clean -y \


EXPOSE 8000

ENTRYPOINT ["/bin/bash", "-c"]