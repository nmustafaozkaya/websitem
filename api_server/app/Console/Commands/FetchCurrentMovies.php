<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FetchCurrentMovies extends Command
{
    protected $signature = 'movies:fetch-current {--year=2024 : Year to fetch movies from} {--pages=10 : Number of pages to fetch}';
    protected $description = 'Fetch current movies from TMDB API (English) and save to CSV';

    private $tmdbApiKey = 'fd906554dbafae73a755cb63e9a595df';
    private $tmdbBaseUrl = 'https://api.themoviedb.org/3';
    private $imageBaseUrl = 'https://image.tmdb.org/t/p/w500';

    public function handle()
    {
        $year = $this->option('year');
        $pages = $this->option('pages');

        $this->info("🎬 Fetching current movies from TMDB...");
        $this->info("📅 Year: {$year}");
        $this->info("📄 Number of pages: {$pages}");

        $allMovies = [];
        $totalFetched = 0;

        for ($page = 1; $page <= $pages; $page++) {
            $this->info("📥 Sayfa {$page} çekiliyor...");
            
            try {
                $movies = $this->fetchMoviesFromTMDB($year, $page);
                
                if (empty($movies)) {
                    $this->warn("⚠️ Page {$page} is empty, stopping.");
                    break;
                }

                $allMovies = array_merge($allMovies, $movies);
                $totalFetched += count($movies);
                
                $this->info("✅ Page {$page}: " . count($movies) . " movies added");
                
                // Rate limiting - TMDB allows 40 requests per 10 seconds
                sleep(1);
                
            } catch (\Exception $e) {
                $this->error("❌ Error on page {$page}: " . $e->getMessage());
                continue;
            }
        }

        if (empty($allMovies)) {
            $this->error("❌ No movies were fetched!");
            return;
        }

        $this->info("📊 Total movies fetched: {$totalFetched}");
        
        // CSV'ye kaydet
        $this->saveToCSV($allMovies, $year);
        
        $this->info("✅ Current movies saved successfully!");
    }

    private function fetchMoviesFromTMDB($year, $page)
    {
        $response = Http::timeout(30)->get("{$this->tmdbBaseUrl}/discover/movie", [
            'api_key' => $this->tmdbApiKey,
            'primary_release_year' => $year,
            'sort_by' => 'popularity.desc',
            'page' => $page,
            // Fetch movies in English
            'language' => 'en-US',
            'include_adult' => false,
            'vote_count.gte' => 10, // En az 10 oy almış filmler
        ]);

        if (!$response->successful()) {
            throw new \Exception("TMDB API error: " . $response->status());
        }

        $data = $response->json();
        return $data['results'] ?? [];
    }

    private function saveToCSV($movies, $year)
    {
        $csvFile = storage_path("app/movies_{$year}.csv");
        
        $handle = fopen($csvFile, 'w');
        
        // CSV başlıkları (mevcut format ile uyumlu)
        $headers = [
            'budget', 'genres', 'homepage', 'id', 'keywords', 'original_language',
            'original_title', 'overview', 'popularity', 'production_companies',
            'production_countries', 'release_date', 'revenue', 'runtime',
            'spoken_languages', 'status', 'tagline', 'title', 'vote_average', 'vote_count'
        ];
        
        fputcsv($handle, $headers);

        foreach ($movies as $movie) {
            $row = $this->formatMovieForCSV($movie);
            fputcsv($handle, $row);
        }

        fclose($handle);
        
        $this->info("💾 CSV file saved: {$csvFile}");
    }

    private function formatMovieForCSV($movie)
    {
        // Genres'i JSON format'a çevir
        $genres = [];
        if (isset($movie['genre_ids'])) {
            foreach ($movie['genre_ids'] as $genreId) {
                $genres[] = ['id' => $genreId, 'name' => $this->getGenreName($genreId)];
            }
        }

        // Production companies
        $productionCompanies = [];
        if (isset($movie['production_companies'])) {
            foreach ($movie['production_companies'] as $company) {
                $productionCompanies[] = ['name' => $company['name'], 'id' => $company['id']];
            }
        }

        // Production countries
        $productionCountries = [];
        if (isset($movie['production_countries'])) {
            foreach ($movie['production_countries'] as $country) {
                $productionCountries[] = ['iso_3166_1' => $country['iso_3166_1'], 'name' => $country['name']];
            }
        }

        // Spoken languages
        $spokenLanguages = [];
        if (isset($movie['spoken_languages'])) {
            foreach ($movie['spoken_languages'] as $lang) {
                $spokenLanguages[] = ['iso_639_1' => $lang['iso_639_1'], 'name' => $lang['name']];
            }
        }

        return [
            $movie['budget'] ?? 0,
            json_encode($genres),
            $movie['homepage'] ?? '',
            $movie['id'],
            '[]', // Keywords - TMDB'de ayrı endpoint'ten gelir
            $movie['original_language'] ?? 'en',
            $movie['original_title'] ?? $movie['title'],
            $movie['overview'] ?? '',
            $movie['popularity'] ?? 0,
            json_encode($productionCompanies),
            json_encode($productionCountries),
            $movie['release_date'] ?? '',
            $movie['revenue'] ?? 0,
            $movie['runtime'] ?? 0,
            json_encode($spokenLanguages),
            $movie['status'] ?? 'Released',
            $movie['tagline'] ?? '',
            $movie['title'],
            $movie['vote_average'] ?? 0,
            $movie['vote_count'] ?? 0
        ];
    }

    private function getGenreName($genreId)
    {
        $genres = [
            28 => 'Action', 12 => 'Adventure', 16 => 'Animation', 35 => 'Comedy',
            80 => 'Crime', 99 => 'Documentary', 18 => 'Drama', 10751 => 'Family',
            14 => 'Fantasy', 36 => 'History', 27 => 'Horror', 10402 => 'Music',
            9648 => 'Mystery', 10749 => 'Romance', 878 => 'Science Fiction',
            10770 => 'TV Movie', 53 => 'Thriller', 10752 => 'War', 37 => 'Western'
        ];
        
        return $genres[$genreId] ?? 'Unknown';
    }
}
