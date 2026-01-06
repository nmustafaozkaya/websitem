<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MergeMovieData extends Command
{
    protected $signature = 'movies:merge {--years=2022,2023,2024,2025 : Years to merge}';
    protected $description = 'Merge movie data from multiple years into main CSV';

    public function handle()
    {
        $years = explode(',', $this->option('years'));
        $allMovies = [];
        
        $this->info("🔄 Film verileri birleştiriliyor...");
        
        foreach ($years as $year) {
            $year = trim($year);
            $csvFile = storage_path("app/movies_{$year}.csv");
            
            if (!file_exists($csvFile)) {
                $this->warn("⚠️ {$year} yılı dosyası bulunamadı: {$csvFile}");
                continue;
            }
            
            $this->info("📂 {$year} yılı verileri okunuyor...");
            
            $handle = fopen($csvFile, 'r');
            if (!$handle) {
                $this->error("❌ {$year} yılı dosyası açılamadı!");
                continue;
            }
            
            // İlk satırı (başlıkları) atla
            $headers = fgetcsv($handle);
            $yearMovies = 0;
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $allMovies[] = $data;
                $yearMovies++;
            }
            
            fclose($handle);
            $this->info("✅ {$year}: {$yearMovies} film eklendi");
        }
        
        if (empty($allMovies)) {
            $this->error("❌ Hiç film verisi bulunamadı!");
            return;
        }
        
        // Ana CSV dosyasını güncelle
        $mainCsvFile = storage_path('app/movies.csv');
        $this->info("💾 Ana CSV dosyası güncelleniyor: {$mainCsvFile}");
        
        $handle = fopen($mainCsvFile, 'w');
        
        // Başlıkları yaz
        $headers = [
            'budget', 'genres', 'homepage', 'id', 'keywords', 'original_language',
            'original_title', 'overview', 'popularity', 'production_companies',
            'production_countries', 'release_date', 'revenue', 'runtime',
            'spoken_languages', 'status', 'tagline', 'title', 'vote_average', 'vote_count'
        ];
        fputcsv($handle, $headers);
        
        // Tüm filmleri yaz
        foreach ($allMovies as $movie) {
            fputcsv($handle, $movie);
        }
        
        fclose($handle);
        
        $this->info("✅ Toplam " . count($allMovies) . " film başarıyla birleştirildi!");
        $this->info("📊 Ana CSV dosyası güncellendi: {$mainCsvFile}");
        
        // İstatistikleri göster
        $this->showStatistics($allMovies);
    }
    
    private function showStatistics($movies)
    {
        $this->info("\n📊 FİLM İSTATİSTİKLERİ:");
        
        // Yıllara göre dağılım
        $years = [];
        foreach ($movies as $movie) {
            if (isset($movie[11]) && !empty($movie[11])) { // release_date
                $year = date('Y', strtotime($movie[11]));
                $years[$year] = ($years[$year] ?? 0) + 1;
            }
        }
        
        ksort($years);
        foreach ($years as $year => $count) {
            $this->info("   {$year}: {$count} film");
        }
        
        // En yüksek rating'li filmler
        $topRated = [];
        foreach ($movies as $movie) {
            if (isset($movie[18]) && is_numeric($movie[18]) && $movie[18] > 0) {
                $topRated[] = ['title' => $movie[17], 'rating' => $movie[18]];
            }
        }
        
        usort($topRated, function($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });
        
        $this->info("\n⭐ EN YÜKSEK RATING'Lİ FİLMLER:");
        for ($i = 0; $i < min(5, count($topRated)); $i++) {
            $this->info("   {$topRated[$i]['title']}: {$topRated[$i]['rating']}");
        }
    }
}
