<?php

namespace Database\Seeders\Cinemas;

use Illuminate\Database\Seeder;
use App\Models\Hall;
use App\Models\Cinema;
use Faker\Factory as Faker;

class HallSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏛️ Salonlar oluşturuluyor...');

        $faker = Faker::create();
        $cinemas = Cinema::all();

        if ($cinemas->isEmpty()) {
            $this->command->error('❌ Önce sinemalar oluşturulmalı! CinemaSeeder çalıştır.');
            return;
        }

        $totalHalls = 0;

        foreach ($cinemas as $cinema) {
            // Her sinemada 2-4 salon
            $hallCount = $faker->numberBetween(2, 4);
            
            for ($i = 1; $i <= $hallCount; $i++) {
                $capacity = $faker->randomElement([60, 80, 100, 120, 150]);
                
                Hall::firstOrCreate([
                    'name' => "Salon {$i}",
                    'cinema_id' => $cinema->id
                ], [
                    'capacity' => $capacity,
                    'status' => 'active'
                ]);

                $totalHalls++;
            }
        }

        $this->command->info("✅ {$totalHalls} salon oluşturuldu.");
    }
}