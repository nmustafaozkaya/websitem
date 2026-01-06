#!/bin/bash

echo "========================================"
echo "  SINEMA UYGULAMASI - DATABASE UPDATE"
echo "  (Kullanicilar ve Biletler Korunur)"
echo "========================================"
echo ""

echo "[1/3] Migrating new tables (if any)..."
# Ensure migrations table exists (silently fails if already exists)
php artisan migrate:install >/dev/null 2>&1
# Run migrations
php artisan migrate
if [ $? -ne 0 ]; then
    echo ""
    echo "HATA: Migration basarisiz oldu!"
    echo "Veritabani tamamen bos ise first_setup.sh kullanin."
    exit 1
fi
echo ""

echo "[2/3] Loading 2024-2025 movies from TMDB..."
php artisan db:seed --class=Database\\Seeders\\Movies\\Movies2025Seeder
echo ""

echo "[3/3] Loading coming soon movies from TMDB..."
php artisan db:seed --class=Database\\Seeders\\Movies\\FutureMoviesSeeder
echo ""

echo "========================================"
echo "  NOT: Yeni seanslar olusturmak icin:"
echo "  php artisan db:seed --class=Database\\Seeders\\Showtimes\\ShowtimeSeeder"
echo "========================================"
echo ""

echo "========================================"
echo "  DATABASE UPDATE COMPLETED!"
echo "========================================"
echo ""
echo "Test Accounts:"
echo "  Admin:    admin@cinema.com / password"
echo "  Manager:  manager@cinema.com / password"
echo "  Cashier:  cashier@cinema.com / password"
echo "  Customer: customer@cinema.com / password"
echo ""
echo "API Server: http://127.0.0.1:8000/api"
echo ""
echo "To start the server, run: php artisan serve"
echo ""

