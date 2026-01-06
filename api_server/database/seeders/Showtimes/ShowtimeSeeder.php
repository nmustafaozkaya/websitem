<?php

namespace Database\Seeders\Showtimes;

use Illuminate\Database\Seeder;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Hall;
use Carbon\Carbon;

class ShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎭 Seanslar oluşturuluyor...');

        $movies = Movie::where('status', 'active')->get();
        $halls = Hall::where('status', 'active')->get();

        if ($movies->isEmpty()) {
            $this->command->error('❌ Önce filmler oluşturulmalı! MovieImportSeeder çalıştır.');
            return;
        }

        if ($halls->isEmpty()) {
            $this->command->error('❌ Önce salonlar oluşturulmalı! HallSeeder çalıştır.');
            return;
        }

        // Tüm aktif filmlerden seanslar oluştur
        $availableMovies = $movies->shuffle();

        $this->command->info("📽️ {$availableMovies->count()} film için seanslar oluşturuluyor...");

        $showtimes = [];
        $totalShowtimes = 0;

        // Önümüzdeki 14 gün için seanslar (7 günden 14 güne çıkarıldı)
        for ($day = 0; $day < 14; $day++) {
            $date = Carbon::now()->addDays($day);
            
            foreach ($halls as $hall) {
                // Her salon için günde 4-5 seans (daha fazla seans için)
                $times = collect(['10:00', '11:30', '14:00', '16:30', '19:00', '21:30'])
                            ->shuffle()
                            ->take(rand(4, 5))
                            ->sort()
                            ->values()
                            ->toArray();
                
                foreach ($times as $time) {
                    // Akıllı film seçimi
                    $movie = $this->selectMovieForTime($availableMovies, $time, $date->dayOfWeek);
                    
                    if ($movie) {
                        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $time);
                        $endTime = $startTime->copy()->addMinutes($movie->duration + 15);
                        
                        $showtimes[] = [
                            'movie_id' => $movie->id,
                            'hall_id' => $hall->id,
                            'price' => $this->determinePrice($startTime),
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'date' => $date->format('Y-m-d'),
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        $totalShowtimes++;
                    }
                }
            }

            // Her 100 seans batch insert
            if (count($showtimes) >= 10) {
                Showtime::insert($showtimes);
                $showtimes = [];
            }
        }

        // Son batch'i ekle
        if (!empty($showtimes)) {
            Showtime::insert($showtimes);
        }

        $this->command->info("✅ {$totalShowtimes} seans oluşturuldu.");
    }
    
    private function determinePrice(Carbon $startTime): float
    {
        $hour = (int)$startTime->format('H');
        $dayOfWeek = (int)$startTime->dayOfWeek;

        // Hafta içi gündüz (öğrenci, indirimli)
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5 && $hour < 17) {
            return 60.00;
        }

        // Hafta sonu veya akşam (tam bilet)
        if ($dayOfWeek == 6 || $dayOfWeek == 0 || $hour >= 17) {
            return 90.00;
        }

        // Varsayılan fiyat
        return 75.00;
    }

    private function selectMovieForTime($movies, string $time, int $dayOfWeek)
    {
        $hour = (int)substr($time, 0, 2);
        
        // Akşam seansları için action/thriller filmleri tercih et
        if ($hour >= 19) {
            $actionMovies = $movies->filter(function($movie) {
                return stripos($movie->genre, 'action') !== false || 
                       stripos($movie->genre, 'thriller') !== false ||
                       stripos($movie->genre, 'adventure') !== false;
            });
            
            if ($actionMovies->count() > 0) {
                return $actionMovies->random();
            }
        }
        
        // Öğle seansları için drama/comedy
        if ($hour >= 12 && $hour <= 16) {
            $dramaMovies = $movies->filter(function($movie) {
                return stripos($movie->genre, 'drama') !== false || 
                       stripos($movie->genre, 'comedy') !== false ||
                       stripos($movie->genre, 'romance') !== false;
            });
            
            if ($dramaMovies->count() > 0) {
                return $dramaMovies->random();
            }
        }
        
        // Hafta sonu sabah seansları için aile filmleri
        if (($dayOfWeek == 0 || $dayOfWeek == 6) && $hour <= 12) {
            $familyMovies = $movies->filter(function($movie) {
                return stripos($movie->genre, 'family') !== false || 
                       stripos($movie->genre, 'animation') !== false ||
                       stripos($movie->genre, 'fantasy') !== false;
            });
            
            if ($familyMovies->count() > 0) {
                return $familyMovies->random();
            }
        }
        
        // Varsayılan: rastgele film
        return $movies->random();
    }
}