<?php
// app/Console/Commands/UpdateFutureMoviePosters.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FutureMovie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateFutureMoviePosters extends Command
{
    protected $signature = 'futuremovies:update-posters {--limit=50 : Number of future movies to update} {--force : Update even if poster exists}';
    protected $description = 'Update future movie posters from TMDB API';

    private $tmdbApiKey = 'fd906554dbafae73a755cb63e9a595df';
    private $tmdbBaseUrl = 'https://api.themoviedb.org/3';
    private $imageBaseUrl = 'https://image.tmdb.org/t/p/w500';

    public function handle()
    {
        $limit = $this->option('limit');
        $force = $this->option('force');

        $this->info("🎬 Future Movie TMDB Poster güncellemesi başlıyor...");
        $this->info("📊 Limit: {$limit} film");

        $query = FutureMovie::query();

        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('poster_url')
                  ->orWhere('poster_url', '')
                  ->orWhere('poster_url', 'like', 'http://www.%');
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
                Log::error("FutureMovie poster update failed", [
                    'movie_id' => $movie->id,
                    'title' => $movie->title,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();

            usleep(300000); // Rate limit
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
            $searchTitle = $this->cleanTitle($movie->title);
            $year = null;
            if ($movie->release_date) {
                $year = date('Y', strtotime($movie->release_date));
            }

            $response = Http::timeout(10)->get("{$this->tmdbBaseUrl}/search/movie", [
                'api_key' => $this->tmdbApiKey,
                'query' => $searchTitle,
                'language' => 'tr-TR'
            ]);

            if (!$response->successful()) {
                throw new \Exception("TMDB API error: " . $response->status());
            }

            $data = $response->json();

            if (empty($data['results'])) {
                $response = Http::timeout(10)->get("{$this->tmdbBaseUrl}/search/movie", [
                    'api_key' => $this->tmdbApiKey,
                    'query' => $searchTitle,
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
        $title = trim($title);
        $title = preg_replace('/\s*\([^)]*\)/', '', $title);
        $title = preg_replace('/\s*\[[^\]]*\]/', '', $title);
        $title = preg_replace('/\s*\d{4}\s*$/', '', $title);

        return trim($title);
    }
}