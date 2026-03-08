# syntax=docker/dockerfile:1

FROM php:8.2-apache AS base

RUN apt update && apt install -y ffmpeg python3 python3-mutagen python3-pip && apt clean && \
    python3 -m pip install --break-system-packages https://github.com/yt-dlp/yt-dlp/archive/master.tar.gz

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

WORKDIR /var/www/html

FROM base AS development

COPY . .

RUN install-php-extensions zip xdebug @composer && \
    mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
	echo "xdebug.mode=develop,debug" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
	echo "xdebug.client_port=9003" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
	echo "xdebug.log=/tmp/xdebug.log" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
	echo "xdebug.start_with_request=yes" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
	echo "xdebug.client_host=host.docker.internal" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"

FROM base AS production

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN a2enmod rewrite cache cache_disk && \
    a2dissite 000-default.conf && \
    install-php-extensions zip opcache @composer && \
    composer install --no-dev --optimize-autoloader

COPY . .
COPY docker/vhost-default.conf /etc/apache2/sites-available/

RUN a2ensite vhost-default.conf && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i \
        -e "s/;realpath_cache_ttl = 120/realpath_cache_ttl = 600/g" \
        -e "s/expose_php = On/expose_php = Off/g" \
        -e "s/;opcache.revalidate_freq=2/opcache.revalidate_freq=60/g" \
        -e "s/;opcache.validate_timestamps=1/opcache.validate_timestamps=0/g" \
        -e "s/;opcache.memory_consumption=128/opcache.memory_consumption=256/g" \
        -e "s/;opcache.interned_strings_buffer=8/opcache.interned_strings_buffer=16/g" \
        -e "s/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=20000/g" \
        "$PHP_INI_DIR/php.ini"