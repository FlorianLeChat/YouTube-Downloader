# syntax=docker/dockerfile:1

FROM php:8.4-apache

RUN apt update && apt install python3 python3-pip python3-mutagen ffmpeg -y && apt clean && \
	python3 -m pip install --break-system-packages https://github.com/yt-dlp/yt-dlp/archive/master.tar.gz

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip @composer

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    chmod +x ./docker/scripts/development/entrypoint.sh

USER www-data