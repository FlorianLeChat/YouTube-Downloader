# syntax=docker/dockerfile:1

FROM php:8.4-fpm

RUN apt update && apt install -y ffmpeg python3 python3-mutagen python3-pip && apt clean && \
	python3 -m pip install --break-system-packages https://github.com/yt-dlp/yt-dlp/archive/master.tar.gz

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip opcache @composer

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;realpath_cache_ttl = 120/realpath_cache_ttl = 600/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/expose_php = On/expose_php = Off/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.revalidate_freq=2/opcache.revalidate_freq=60/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.validate_timestamps=1/opcache.validate_timestamps=0/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.memory_consumption=128/opcache.memory_consumption=256/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.interned_strings_buffer=8/opcache.interned_strings_buffer=16/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=20000/g" "$PHP_INI_DIR/php.ini"

USER www-data

RUN --mount=type=cache,target=.composer composer install --no-dev --optimize-autoloader