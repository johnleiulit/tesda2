#!/bin/bash
set -ex

echo "=== Starting deployment ==="
echo "PORT: $PORT"
echo "APP_ENV: $APP_ENV"

echo "=== Running migrations ==="
php artisan migrate --force || echo "Migration failed but continuing..."

echo "=== Starting Laravel server on port $PORT ==="
php artisan serve --host=0.0.0.0 --port=$PORT 2>&1
