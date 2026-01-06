@echo off
echo ========================================
echo   SINEMA UYGULAMASI - DATABASE UPDATE
echo   (Kullanicilar ve Biletler Korunur)
echo ========================================
echo.

echo [1/3] Migrating new tables (if any)...
REM Ensure migrations table exists (silently fails if already exists)
php artisan migrate:install >nul 2>&1
REM Run migrations - this will create all tables if database is empty
php artisan migrate --force
if errorlevel 1 (
    echo.
    echo HATA: Migration basarisiz oldu!
    echo.
    echo Eger veritabani tamamen bos ise, once first_setup.bat calistirin.
    echo first_setup.bat tum tablolari olusturur ve test verilerini ekler.
    echo.
    pause
    exit /b 1
)
echo.

echo [2/3] Loading 2025-2026 movies from TMDB...
php artisan db:seed --class=Database\Seeders\Movies\Movies2025Seeder
echo.

echo [3/3] Loading coming soon movies from TMDB...
php artisan db:seed --class=Database\Seeders\Movies\FutureMoviesSeeder
echo.

echo ========================================
echo   NOT: Yeni seanslar olusturmak icin:
echo   php artisan db:seed --class=Database\Seeders\Showtimes\ShowtimeSeeder
echo ========================================
echo.

echo ========================================
echo   DATABASE UPDATE COMPLETED!
echo ========================================
echo.
echo Test Accounts:
echo   Admin:    admin@cinema.com / password
echo   Manager:  manager@cinema.com / password
echo   Cashier:  cashier@cinema.com / password
echo   Customer: customer@cinema.com / password
echo.
echo API Server: http://127.0.0.1:8000/api
echo.
echo To start the server, run: php artisan serve
echo.
pause

