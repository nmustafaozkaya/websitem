@echo off
echo ========================================
echo   SINEMA UYGULAMASI - ILK KURULUM
echo   (TUM VERITABANI SIFIRLANIR!)
echo ========================================
echo.

echo UYARI: Bu script tum veritabanini sifirlar!
echo Kullanicilar, biletler, her sey silinecek.
echo.
pause

echo [1/5] Migrating database...
php artisan migrate:fresh
echo.

echo [2/5] Seeding cities and cinemas...
php artisan db:seed --class=Database\Seeders\Cinemas\CitySeeder
php artisan db:seed --class=Database\Seeders\Cinemas\CinemaSeeder
php artisan db:seed --class=Database\Seeders\Cinemas\HallSeeder
php artisan db:seed --class=Database\Seeders\Cinemas\SeatSeeder
echo.

echo [3/5] Loading 2024-2025 movies from TMDB...
php artisan db:seed --class=Database\Seeders\Movies\Movies2025Seeder
echo.

echo [4/5] Loading coming soon movies from TMDB...
php artisan db:seed --class=Database\Seeders\Movies\FutureMoviesSeeder
echo.

echo [5/5] Setting up users and tickets...
php artisan db:seed --class=Database\Seeders\Users\RoleSeeder
php artisan db:seed --class=Database\Seeders\Users\PermissionSeeder
php artisan db:seed --class=Database\Seeders\Users\UserSeeder
php artisan db:seed --class=Database\Seeders\Users\CustomerTypeSeeder
php artisan db:seed --class=Database\Seeders\Tickets\TaxSeeder
php artisan db:seed --class=Database\Seeders\Showtimes\ShowtimeSeeder
echo.

echo ========================================
echo   ILK KURULUM TAMAMLANDI!
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

