# syntax=docker/dockerfile:1

# Use an customized image of PHP
# https://hub.docker.com/_/php
ARG VERSION=apache
FROM php:${VERSION}

# Install dependencies
ARG VERSION
RUN if [ $VERSION = "apache" ]; then \
        apt update && apt install python3 python3-pip ffmpeg -y; \
    else \
		echo https://dl-4.alpinelinux.org/alpine/latest-stable/community/ >> /etc/apk/repositories && \
		apk update && \
        apk add --no-cache python3 py3-pip ffmpeg; \
    fi

# Install yt-dl (patched version)
RUN python3 -m pip install --break-system-packages https://github.com/yt-dlp/yt-dlp/archive/master.tar.gz

# Install some PHP extensions
RUN curl -sSLf \
		-o /usr/local/bin/install-php-extensions \
		https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
	chmod +x /usr/local/bin/install-php-extensions && \
	install-php-extensions zip opcache

# Install Composer for dependency management
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

# Use the PHP production configuration
RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# Set the working directory to the website files
WORKDIR /var/www/html

# Copy all files to the working directory
COPY --chown=www-data:www-data . .

# Use the PHP custom configuration (if exists)
RUN if [ -f "docker/php.ini" ]; then mv "docker/php.ini" "$PHP_INI_DIR/php.ini"; fi

# Create and set permissions for output directory
# https://github.com/docker/compose/issues/3270#issuecomment-206214034
RUN mkdir -p output/temp && chown www-data:www-data output/ -R

# Change current user to www-data
USER www-data

# Install all dependencies
# Use cache mount to speed up installation of existing dependencies
RUN --mount=type=cache,target=.composer \
	composer install --no-dev --optimize-autoloader