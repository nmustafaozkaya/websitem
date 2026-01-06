<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Sinema otomasyonu sistemi oluşturuluyor...');

        // 1. Users Feature - Temel kullanıcı sistemi
        $this->command->info('👥 Kullanıcı sistemi...');
        $this->call([
            Users\RoleSeeder::class,
            Users\PermissionSeeder::class,
        ]);

        // 2. Movies Feature - Film verileri
        $this->command->info('🎬 Film sistemi...');
        $this->call([
            Movies\MovieImportSeeder::class,
            Movies\Movies2025Seeder::class, // 2025 filmleri
            Movies\FutureMoviesSeeder::class, 
        ]);

        // 3. Cinemas Feature - Sinema altyapısı
        $this->command->info('🏛️ Sinema sistemi...');
        $this->call([
            Cinemas\CitySeeder::class,
            Cinemas\CinemaSeeder::class,
            Cinemas\HallSeeder::class,
            Cinemas\SeatSeeder::class,
        ]);

        // 4. Showtimes Feature - Seans sistemi
        $this->command->info('🎭 Seans sistemi...');
        $this->call([
            Showtimes\ShowtimeSeeder::class,
        ]);

        // 5. Users & Tickets - Son aşama
        $this->command->info('🎫 Kullanıcı ve bilet sistemi...');
        $this->call([
            Users\UserSeeder::class,
            Users\PermissionSeeder::class,
            Users\CustomerTypeSeeder::class,
            Tickets\TaxSeeder::class,

        ]);

        $this->showFinalSummary();
    }

    private function showFinalSummary(): void
    {
        $this->command->info('');
        $this->command->info('🎉 SİNEMA OTOMASYONU SİSTEMİ HAZIR!');
        $this->command->info('');

        $stats = [
            'Filmler' => \App\Models\Movie::count(),
            'Gelecek Filmler' => \App\Models\FutureMovie::count(), 
            'Şehirler' => \App\Models\City::count(),
            'Sinemalar' => \App\Models\Cinema::count(),
            'Salonlar' => \App\Models\Hall::count(),
            'Koltuklar' => \App\Models\Seat::count(),
            'Seanslar' => \App\Models\Showtime::count(),
            'Kullanıcılar' => \App\Models\User::count(),
            'Roller' => \App\Models\Role::count(),
        ];

        foreach ($stats as $key => $value) {
            $this->command->info("   {$key}: " . number_format($value));
        }

        $this->command->info('');
        $this->command->info('🔑 Test Hesapları:');
        $this->command->info('   Admin: admin@cinema.com / password');
        $this->command->info('   Manager: manager@cinema.com / password');
        $this->command->info('   Cashier: cashier@cinema.com / password');
        $this->command->info('   Customer: customer@cinema.com / password');
        $this->command->info('');
        $this->command->info('🌐 API Endpoint:');
        $this->command->info('   http://127.0.0.1:8000/api');
        $this->command->info('   http://127.0.0.1:8000/api/future-movies'); 
        $this->command->info('');
        $this->command->info('🚀 Sistemi başlatmak için: php artisan serve');
    }
}