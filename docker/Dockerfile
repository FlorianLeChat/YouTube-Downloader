# syntax=docker/dockerfile:1@sha256:4edf897a3ffa55b89f906fc8cc78afdb3f1834cc9c7083565e611a8a7d5fe99e

FROM php:8.2-apache@sha256:89e5f7bde208a146519d12100c11e71a1d0119a10703cfc454d1bdca274147ec AS base

# renovate: datasource=pypi depName=yt-dlp
ARG YT_DLP_VERSION=2026.10.26

RUN apt-get update && \
    apt-get install -y --no-install-recommends ffmpeg python3 python3-mutagen python3-pip && \
    rm -rf /var/lib/apt/lists/* && \
    python3 -m pip install --no-cache-dir --break-system-packages "yt-dlp==${YT_DLP_VERSION}"

COPY --from=mlocati/php-extension-installer:2@sha256:35821db0661308f64aa18483bb3af56bedc3b40090ff441ed961327d1ccbceb8 \
    /usr/bin/install-php-extensions /usr/local/bin/

WORKDIR /var/www/html

FROM base AS development

RUN install-php-extensions zip xdebug @composer && \
    mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    echo "xdebug.mode=develop,debug" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
    echo "xdebug.client_port=9003" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
    echo "xdebug.log=/tmp/xdebug.log" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
    echo "xdebug.start_with_request=yes" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
    echo "xdebug.client_host=host.docker.internal" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"

COPY --chown=www-data:www-data . .

FROM base AS production

RUN a2enmod rewrite && \
    a2dissite 000-default.conf && \
    sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf && \
    install-php-extensions zip opcache

COPY composer.json composer.lock ./

RUN --mount=type=bind,from=composer:2,source=/usr/bin/composer,target=/usr/local/bin/composer \
    --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache composer install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --no-autoloader

COPY docker/configuration/vhost-default.conf /etc/apache2/sites-available/
COPY . .

RUN --mount=type=bind,from=composer:2,source=/usr/bin/composer,target=/usr/local/bin/composer \
    composer dump-autoload --no-dev --no-scripts --classmap-authoritative && \
    a2ensite vhost-default.conf && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    printf '%s\n' \
      'realpath_cache_ttl = 600' \
      'expose_php = Off' \
      'opcache.validate_timestamps = 0' \
      'opcache.memory_consumption = 256' \
      'opcache.interned_strings_buffer = 16' \
      'opcache.max_accelerated_files = 20000' \
    > "$PHP_INI_DIR/conf.d/zz-custom.ini"
