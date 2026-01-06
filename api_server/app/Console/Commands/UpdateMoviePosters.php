<?php
// app/Console/Commands/UpdateMoviePosters.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateMoviePosters extends Command
{
    //php artisan movies:update-posters --force
    protected $signature = 'movies:update-posters {--limit=50 : Number of movies to update} {--force : Update even if poster exists}';
    protected $description = 'Update movie posters from TMDB API';

    private $tmdbApiKey = 'fd906554dbafae73a755cb63e9a595df';
    private $tmdbBaseUrl = 'https://api.themoviedb.org/3';
    private $imageBaseUrl = 'https://image.tmdb.org/t/p/w500';

    public function handle()
    {
        $limit = $this->option('limit');
        $force = $this->option('force');

        $this->info("🎬 TMDB Poster güncellemesi başlıyor...");
        $this->info("📊 Limit: {$limit} film");

        // Poster'ı olmayan veya force ile tüm filmler
        $query = Movie::query();
        
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('poster_url')
                  ->orWhere('poster_url', '')
                  ->orWhere('poster_url', 'like', 'http://www.%'); // Mevcut film sitesi URL'leri
            });
        }

        $movies = $query->limit($limit)->get();

        $this->info("🔍 {$movies->count()} film bulundu");

        $progressBar = $this->output->createProgressBar($movies->count());
        $progressBar->start();

        $updated = 0;
        $failed = 0;

        foreach ($movies as $movie) {
            try {
                $posterUrl = $this->fetchPosterFromTMDB($movie);
                
                if ($posterUrl) {
                    $movie->update(['poster_url' => $posterUrl]);
                    $updated++;
                    $this->line("\n✅ {$movie->title} - Poster güncellendi");
                } else {
                    $failed++;
                    $this->line("\n❌ {$movie->title} - Poster bulunamadı");
                }

            } catch (\Exception $e) {
                $failed++;
                $this->line("\n💥 {$movie->title} - Hata: " . $e->getMessage());
                Log::error("Movie poster update failed", [
                    'movie_id' => $movie->id,
                    'title' => $movie->title,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
            
            // Rate limiting - TMDB allows 40 requests per 10 seconds
            usleep(300000); // 0.3 saniye bekle
        }

        $progressBar->finish();

        $this->info("\n\n🎉 Güncelleme tamamlandı!");
        $this->info("✅ Başarılı: {$updated}");
        $this->info("❌ Başarısız: {$failed}");
        $this->info("📊 Toplam: " . ($updated + $failed));
    }

    private function fetchPosterFromTMDB($movie)
    {
        try {
            // Film adını temizle
            $searchTitle = $this->cleanTitle($movie->title);
            
            // Çıkış yılını al
            $year = null;
            if ($movie->release_date) {
                $year = date('Y', strtotime($movie->release_date));
            }

            // TMDB'de ara
            $response = Http::timeout(10)->get("{$this->tmdbBaseUrl}/search/movie", [
                'api_key' => $this->tmdbApiKey,
                'query' => $searchTitle,
                'year' => $year,
                'language' => 'tr-TR' // Türkçe poster tercih et
            ]);

            if (!$response->successful()) {
                throw new \Exception("TMDB API error: " . $response->status());
            }

            $data = $response->json();
            
            if (empty($data['results'])) {
                // Türkçe bulunamazsa İngilizce dene
                $response = Http::timeout(10)->get("{$this->tmdbBaseUrl}/search/movie", [
                    'api_key' => $this->tmdbApiKey,
                    'query' => $searchTitle,
                    'year' => $year,
                    'language' => 'en-US'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                }
            }

            if (!empty($data['results'])) {
                $movie_result = $data['results'][0];
                
                if (!empty($movie_result['poster_path'])) {
                    return $this->imageBaseUrl . $movie_result['poster_path'];
                }
            }

            return null;

        } catch (\Exception $e) {
            throw new \Exception("TMDB fetch error: " . $e->getMessage());
        }
    }

    private function cleanTitle($title)
    {
        // Film adını arama için temizle
        $title = trim($title);
        
        // Yaygın gereksiz kısımları kaldır
        $title = preg_replace('/\s*\([^)]*\)/', '', $title); // Parantez içi
        $title = preg_replace('/\s*\[[^\]]*\]/', '', $title); // Köşeli parantez içi
        $title = preg_replace('/\s*\d{4}\s*$/', '', $title);  // Sondaki yıl
        
        return trim($title);
    }
}