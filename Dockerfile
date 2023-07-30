# syntax=docker/dockerfile:1

# Use an customized image of PHP 8.2 with Nginx
# https://github.com/webdevops/Dockerfile/blob/master/docker/php-nginx/8.2-alpine/Dockerfile
FROM webdevops/php-nginx:8.2-alpine

# Install Python 3 (required for yt-dl) and ffmpeg (required for audio extraction)
RUN apk add python3 py3-pip ffmpeg

# Install yt-dl (patched version)
RUN python3 -m pip install https://github.com/yt-dlp/yt-dlp/archive/master.tar.gz

# Set the working directory to the website files
WORKDIR /app

# Copy only files required to install dependencies
COPY composer*.json ./

# Install all dependencies
# Use cache mount to speed up installation of existing dependencies
RUN --mount=type=cache,target=/app/.composer \
	composer install

# Copy the remaining files AFTER installing dependencies
COPY . .