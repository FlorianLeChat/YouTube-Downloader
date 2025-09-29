# syntax=docker/dockerfile:1

# Use an customized image of PHP
# https://hub.docker.com/_/php
FROM php:8.4-apache

# Install required dependencies
RUN apt update && apt install python3 python3-pip python3-mutagen ffmpeg -y && apt clean && \
	python3 -m pip install --break-system-packages https://github.com/yt-dlp/yt-dlp/archive/master.tar.gz

# Install some PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip @composer

# Set the working directory to the website files
WORKDIR /var/www/html

# Copy all files to the working directory
COPY --chown=www-data:www-data . .

# Use the default development PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Change current user to www-data
USER www-data

# Make the entrypoint script executable
RUN chmod +x ./docker/scripts/development/entrypoint.sh