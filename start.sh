#!/bin/bash
set -ex

echo "=== Starting deployment ==="
echo "PORT: $PORT"
echo "APP_ENV: $APP_ENV"

echo "=== Clearing all caches ==="
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

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

echo "=== Checking public/images directory ==="
ls -la public/images/ || echo "Images directory not found!"
echo "Image files:"
find public/images -type f || echo "No image files found!"

echo "=== Optimizing application ==="
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Starting Laravel server on port $PORT ==="
# Use router script to properly handle static files
cat > router.php << 'EOF'
<?php
// Router script for PHP built-in server to handle static files
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Otherwise, run Laravel
require_once __DIR__ . '/public/index.php';
EOF

php -S 0.0.0.0:$PORT -t public router.php
