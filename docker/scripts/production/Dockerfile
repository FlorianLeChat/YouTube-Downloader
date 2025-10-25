# syntax=docker/dockerfile:1

# Use an customized image of PHP
# https://hub.docker.com/_/php
FROM php:8.4-fpm

# Install required dependencies
RUN apt update && apt install -y ffmpeg python3 python3-mutagen python3-pip && apt clean && \
	python3 -m pip install --break-system-packages https://github.com/yt-dlp/yt-dlp/archive/master.tar.gz

# Install some PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip opcache @composer

# Set the working directory to the website files
WORKDIR /var/www/html

# Copy all files to the working directory
COPY --chown=www-data:www-data . .

# Use the default production PHP configuration with some modifications
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
	# Cache a filesystem path to real path mapping for 10 minutes
	sed -i "s/;realpath_cache_ttl = 120/realpath_cache_ttl = 600/g" "$PHP_INI_DIR/php.ini" && \
	# Don't expose PHP version to HTTP response headers
	sed -i "s/expose_php = On/expose_php = Off/g" "$PHP_INI_DIR/php.ini" && \
	# Increase default OPcache settings to improve performance
	sed -i "s/;opcache.revalidate_freq=2/opcache.revalidate_freq=60/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.validate_timestamps=1/opcache.validate_timestamps=0/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.memory_consumption=128/opcache.memory_consumption=256/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.interned_strings_buffer=8/opcache.interned_strings_buffer=16/g" "$PHP_INI_DIR/php.ini" && \
	sed -i "s/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=20000/g" "$PHP_INI_DIR/php.ini"

# Change current user to www-data
USER www-data

# Install all dependencies
# Use cache mount to speed up installation of existing dependencies
RUN --mount=type=cache,target=.composer composer install --no-dev --optimize-autoloader