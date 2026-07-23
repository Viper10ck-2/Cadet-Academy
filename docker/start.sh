#!/bin/bash

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground (keeps container alive)
exec nginx -c /app/nginx.conf -g "daemon off;"
