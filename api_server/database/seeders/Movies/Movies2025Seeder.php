<?php

namespace Database\Seeders\Movies;

use Illuminate\Database\Seeder;
use App\Models\Movie;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Movies2025Seeder extends Seeder
{
    private $tmdbApiKey = 'fd906554dbafae73a755cb63e9a595df';
    private $tmdbBaseUrl = 'https://api.themoviedb.org/3';
    private $imageBaseUrl = 'https://image.tmdb.org/t/p/w500';

    public function run(): void
    {
        $this->command->info('🎬 Loading 2025-2026 movies from TMDB (English)...');

        $movies = [];
        $totalAdded = 0;
        
        // 2025 ve 2026 yıllarını kapsamak için tarih aralığı kullan
        $years = [2025, 2026];
        
        foreach ($years as $year) {
            $this->command->info("📅 Loading {$year} movies...");
            $page = 1;
            $maxPages = 10; // Her yıl için 10 sayfa ≈ 200 film
            
            while ($page <= $maxPages) {
                $this->command->info("📄 Loading page {$page} for {$year}...");

                try {
                    $response = Http::timeout(15)->get("{$this->tmdbBaseUrl}/discover/movie", [
                        'api_key' => $this->tmdbApiKey,
                        'primary_release_year' => $year,
                        'sort_by' => 'popularity.desc',
                        'page' => $page,
                        // İngilizce bilgilerle al
                        'language' => 'en-US',
                        'vote_count.gte' => 10, // En az 10 oy almış filmler
                    ]);

                if (!$response->successful()) {
                    $this->command->error("❌ TMDB API error: " . $response->status());
                    break;
                }

                $data = $response->json();
                
                if (empty($data['results'])) {
                    $this->command->info("⚠️ No movies found on page {$page}.");
                    break;
                }

                foreach ($data['results'] as $movieData) {
                    // Only add movies that have a poster
                    if (empty($movieData['poster_path'])) {
                        continue;
                    }

                    // Skip if movie with the same title already exists
                    $existingMovie = Movie::where('title', $movieData['title'])->first();
                    if ($existingMovie) {
                        continue;
                    }

                    // Get genre names
                    $genres = [];
                    if (isset($movieData['genre_ids']) && is_array($movieData['genre_ids'])) {
                        $genres = $this->getGenreNames($movieData['genre_ids']);
                    }

                    // Parse release date
                    $releaseDate = null;
                    if (!empty($movieData['release_date'])) {
                        try {
                            $releaseDate = Carbon::parse($movieData['release_date'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $releaseDate = '2025-01-01';
                        }
                    } else {
                        $releaseDate = '2025-01-01';
                    }

                    // Prepare movie payload
                    $movie = [
                        'title' => substr($movieData['title'] ?? 'Untitled Movie', 0, 255),
                        'description' => substr($movieData['overview'] ?? 'No description available.', 0, 1000),
                        'duration' => $movieData['runtime'] ?? 120,
                        'language' => substr($movieData['original_language'] ?? 'en', 0, 5),
                        'release_date' => $releaseDate,
                        'genre' => !empty($genres) ? implode(', ', array_slice($genres, 0, 3)) : 'Drama',
                        'poster_url' => $this->imageBaseUrl . $movieData['poster_path'],
                        'imdb_raiting' => isset($movieData['vote_average']) ? round($movieData['vote_average'], 1) : null,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    try {
                        Movie::create($movie);
                        $totalAdded++;
                        $releaseYear = date('Y', strtotime($releaseDate));
                        $this->command->info("✅ {$movie['title']} ({$releaseYear}) added - IMDB: {$movie['imdb_raiting']}");
                    } catch (\Exception $e) {
                        $this->command->warn("⚠️ {$movie['title']} could not be added: " . $e->getMessage());
                    }
                }

                    // Rate limiting
                    usleep(500000); // wait 0.5 seconds
                    $page++;

                } catch (\Exception $e) {
                    $this->command->error("❌ Error loading page {$page} for {$year}: " . $e->getMessage());
                    break;
                }
            }
        }

        $this->command->info("\n🎉 2025-2026 movies loading completed!");
        $this->command->info("📊 Total added: {$totalAdded} movies");
    }

    private function getGenreNames($genreIds): array
    {
        static $genreMap = null;

        if ($genreMap === null) {
            try {
                $response = Http::timeout(10)->get("{$this->tmdbBaseUrl}/genre/movie/list", [
                    'api_key' => $this->tmdbApiKey,
                    // İngilizce tür isimlerini al
                    'language' => 'en-US'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $genreMap = [];
                    foreach ($data['genres'] as $genre) {
                        $genreMap[$genre['id']] = $genre['name'];
                    }
                }
            } catch (\Exception $e) {
                $this->command->warn("⚠️ Could not fetch genre list, using default map.");
            }

            // Default genre map (English)
            if ($genreMap === null) {
                $genreMap = [
                    28 => 'Action',
                    12 => 'Adventure',
                    16 => 'Animation',
                    35 => 'Comedy',
                    80 => 'Crime',
                    99 => 'Documentary',
                    18 => 'Drama',
                    10751 => 'Family',
                    14 => 'Fantasy',
                    36 => 'History',
                    27 => 'Horror',
                    10402 => 'Music',
                    9648 => 'Mystery',
                    10749 => 'Romance',
                    878 => 'Science Fiction',
                    10770 => 'TV Movie',
                    53 => 'Thriller',
                    10752 => 'War',
                    37 => 'Western'
                ];
            }
        }

        $genres = [];
        foreach ($genreIds as $id) {
            if (isset($genreMap[$id])) {
                $genres[] = $genreMap[$id];
            }
        }

        return $genres;
    }
}

