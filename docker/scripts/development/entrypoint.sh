#!/bin/sh

# Install Composer dependencies
composer install

# Start Apache server in foreground
apache2-foreground