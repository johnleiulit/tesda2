#!/bin/bash
set -ex

echo "=== Starting deployment ==="
echo "PORT: $PORT"
echo "APP_ENV: $APP_ENV"

echo "=== Setting up storage permissions ==="
chmod -R 775 storage bootstrap/cache

echo "=== Checking APP_KEY ==="
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
else
    echo "APP_KEY already set"
fi

echo "=== Running migrations ==="
php artisan migrate --force || echo "Migration failed but continuing..."

echo "=== Creating storage link ==="
php artisan storage:link || echo "Storage link already exists"

echo "=== Optimizing application ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Starting Laravel server on port $PORT ==="
php -S 0.0.0.0:$PORT -t public public/index.php
