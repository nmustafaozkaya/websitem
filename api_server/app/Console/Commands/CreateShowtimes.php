<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Hall;
use Carbon\Carbon;

class CreateShowtimes extends Command
{
    protected $signature = 'showtimes:create';
    protected $description = 'Create showtimes for all movies';

    public function handle()
    {
        $this->info("🎭 Seanslar oluşturuluyor...");
        
        $movies = Movie::all();
        $halls = Hall::all();
        
        if ($movies->isEmpty()) {
            $this->error("❌ Hiç film bulunamadı!");
            return;
        }
        
        if ($halls->isEmpty()) {
            $this->error("❌ Hiç salon bulunamadı!");
            return;
        }
        
        $this->info("📽️ {$movies->count()} film için seanslar oluşturuluyor...");
        $this->info("🏛️ {$halls->count()} salon mevcut");
        
        $showtimeCount = 0;
        $progressBar = $this->output->createProgressBar($movies->count());
        $progressBar->start();
        
        foreach ($movies as $movie) {
            // Her film için 3-5 salon seç
            $selectedHalls = $halls->random(rand(3, 5));
            
            foreach ($selectedHalls as $hall) {
                // Her salon için 2-4 seans oluştur
                $sessionCount = rand(2, 4);
                
                for ($i = 0; $i < $sessionCount; $i++) {
                    $startTime = Carbon::now()
                        ->addDays(rand(1, 30))
                        ->setHour(rand(10, 22))
                        ->setMinute(rand(0, 3) * 15);
                    
                    Showtime::create([
                        'movie_id' => $movie->id,
                        'hall_id' => $hall->id,
                        'date' => $startTime->toDateString(),
                        'start_time' => $startTime,
                        'end_time' => $startTime->copy()->addMinutes($movie->duration + 30),
                        'price' => rand(35, 65),
                        'status' => 'active'
                    ]);
                    
                    $showtimeCount++;
                }
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        
        $this->info("\n✅ Toplam {$showtimeCount} seans oluşturuldu!");
        
        // İstatistikler
        $this->info("\n📊 SEANS İSTATİSTİKLERİ:");
        $this->info("   Toplam Seans: " . Showtime::count());
        $this->info("   Aktif Seans: " . Showtime::where('status', 'active')->count());
        $this->info("   Gelecek Seans: " . Showtime::where('start_time', '>', now())->count());
        
        // Film başına ortalama seans
        $avgShowtimes = round($showtimeCount / $movies->count(), 1);
        $this->info("   Film Başına Ortalama: {$avgShowtimes} seans");
    }
}
