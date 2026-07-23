#!/bin/bash
set -e

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx -c /app/nginx.conf

# Keep container alive
wait -n
