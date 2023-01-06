# syntax=docker/dockerfile:1

# Use an customized image of PHP 8.2 with Nginx
# https://github.com/webdevops/Dockerfile/blob/master/docker/php-nginx/8.2-alpine/Dockerfile
ARG PHP_VERSION
FROM webdevops/php-nginx:${PHP_VERSION}-alpine

# Install Python 3 (required for yt-dl) and ffmpeg (required for audio extraction)
RUN apk add python3 py3-pip ffmpeg

# Install yt-dl (patched version)
RUN python3 -m pip install https://github.com/yt-dlp/yt-dlp/archive/master.tar.gz

# Copy the website files to the container
COPY ./ /app

# Install Composer and run it to install the dependencies
RUN composer install -d /app