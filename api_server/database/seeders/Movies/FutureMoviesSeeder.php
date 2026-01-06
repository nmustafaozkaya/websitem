<?php
// database/seeders/Movies/FutureMoviesSeeder.php

namespace Database\Seeders\Movies;

use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\FutureMovie;
use Carbon\Carbon;
use Faker\Factory as Faker;

class FutureMoviesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $this->command->info('🎬 Future Movies tablosu dolduruluyor...');

        // Önce tabloyu temizle
        FutureMovie::truncate();

        // Mevcut filmlerden örnek al
        $existingMovies = Movie::limit(30)->get();
        
        $futureMovies = [];
        $count = 0;

        // 1. Mevcut filmlerden sekuel/prequel oluştur
        foreach ($existingMovies as $movie) {
            $futureMovies[] = [
                'title' => $this->generateSequelTitle($movie->title),
                'description' => $this->generateSequelDescription($movie->description),
                'duration' => $movie->duration + rand(-20, 30),
                'language' => $movie->language,
                'release_date' => $this->generateFutureDate(),
                'genre' => $movie->genre,
                'poster_url' => $movie->poster_url, // ✅ Orijinal posteri aynen kullan
                'imdb_raiting' => $this->adjustRating($movie->imdb_raiting),
                'status' => $this->getRandomStatus(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $count++;
        }

        // 2. Tamamen yeni filmler ekle
        $newMovieTitles = [
            'Quantum Realm Warriors',
            'The Last Samurai: Rising',
            'Digital Dreams',
            'Space Odyssey 2025',
            'The Time Keeper',
            'Neon City Chronicles',
            'The Crystal Prophecy',
            'Underground Heroes',
            'The Cyber Hunter',
            'Mystic Forest Legends',
            'Solar Storm',
            'The AI Revolution',
            'Dark Matter',
            'Future Earth',
            'The Virtual Reality',
            'Galactic Empire',
            'The Lost Kingdom',
            'Cyber Wars',
            'The Memory Thief',
            'Quantum Jump'
        ];

        $genres = ['Action', 'Sci-Fi', 'Adventure', 'Drama', 'Comedy', 'Thriller', 'Fantasy', 'Horror'];
        $languages = ['en', 'tr'];

        foreach ($newMovieTitles as $title) {
            $futureMovies[] = [
                'title' => $title,
                'description' => $this->generateDescription($title),
                'duration' => rand(90, 180),
                'language' => $faker->randomElement($languages),
                'release_date' => $this->generateFutureDate(),
                'genre' => $faker->randomElement($genres),
                'poster_url' => $this->generateRealPosterUrl(), // ✅ Gerçek posterler
                'imdb_raiting' => number_format(rand(50, 95) / 10, 1),
                'status' => $this->getRandomStatus(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $count++;
        }

        // Batch insert (daha hızlı)
        $chunks = array_chunk($futureMovies, 20);
        foreach ($chunks as $chunk) {
            FutureMovie::insert($chunk);
        }

        $this->command->info("✅ {$count} gelecek film eklendi!");
        
        // İstatistikleri göster
        $this->showStatistics();
    }

    private function generateSequelTitle($originalTitle)
    {
        $sequelTypes = [
            ' 2',
            ' Returns', 
            ' Reborn',
            ' Legacy',
            ' Revolution',
            ' Rising',
            ' Awakening',
            ' Unleashed',
            ': The Beginning',
            ': New Era',
            ': Next Generation'
        ];

        return $originalTitle . Faker::create()->randomElement($sequelTypes);
    }

    private function generateSequelDescription($originalDescription)
    {
        if (!$originalDescription) {
            return 'An exciting continuation of the beloved story with new adventures and challenges.';
        }

        $continuations = [
            'The saga continues with even more thrilling adventures. ',
            'Our heroes return for an epic new chapter. ',
            'The story evolves with new challenges and discoveries. ',
            'A new generation takes on the legacy. ',
            'The adventure continues in this highly anticipated sequel. '
        ];

        return Faker::create()->randomElement($continuations) . $originalDescription;
    }

    private function generateDescription($title)
    {
        $templates = [
            "An epic adventure that follows the journey of heroes in a world where {concept}.",
            "A thrilling story about {concept} that will change everything you know about {element}.",
            "When {element} threatens the world, only {concept} can save humanity.",
            "A groundbreaking film that explores {concept} in ways never seen before.",
            "The ultimate battle between good and evil unfolds as {concept} rises."
        ];

        $concepts = [
            'artificial intelligence', 'time travel', 'parallel universes', 'alien contact',
            'quantum physics', 'virtual reality', 'genetic engineering', 'space exploration',
            'ancient mysteries', 'supernatural powers', 'technology fusion', 'digital consciousness'
        ];

        $elements = [
            'technology', 'humanity', 'nature', 'reality', 'consciousness', 'existence',
            'civilization', 'evolution', 'destiny', 'truth', 'power', 'knowledge'
        ];

        $faker = Faker::create();
        $template = $faker->randomElement($templates);
        $concept = $faker->randomElement($concepts);
        $element = $faker->randomElement($elements);

        return str_replace(['{concept}', '{element}'], [$concept, $element], $template);
    }

    private function generateFutureDate()
    {
        // 1 ay ile 2 yıl arası gelecek tarihler - MYSQL DATE formatında
        $futureDate = Carbon::now()->addDays(rand(30, 730));
        return $futureDate->format('Y-m-d');
    }

    private function adjustRating($originalRating)
    {
        if (!$originalRating) {
            return number_format(rand(60, 90) / 10, 1);
        }

        $rating = floatval($originalRating);
        $adjustment = (rand(-10, 15) / 10); // -1.0 ile +1.5 arası
        $newRating = max(1.0, min(10.0, $rating + $adjustment));
        
        return number_format($newRating, 1);
    }

    private function getRandomStatus()
    {
        $statuses = [
            'upcoming' => 60,      // %60 şans
            'pre_production' => 20, // %20 şans
            'in_production' => 15,  // %15 şans
            'post_production' => 5   // %5 şans
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($statuses as $status => $chance) {
            $cumulative += $chance;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'upcoming';
    }

    // ✅ YENİ METOD - Gerçek TMDB posterler
    private function generateRealPosterUrl()
    {
        $realPosterUrls = [
            'https://image.tmdb.org/t/p/w500/6EiRUJpuoeQPghrs3YNktKdnIDV.jpg', // Avatar: The Way of Water
            'https://image.tmdb.org/t/p/w500/xFw9RXKZDvevAGocgBK0zteto4U.jpg', // Top Gun: Maverick
            'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg', // Black Panther: Wakanda Forever
            'https://image.tmdb.org/t/p/w500/rktDFPbfHfUbArZ6OOOKsXcv0Bm.jpg', // Spider-Man: No Way Home
            'https://image.tmdb.org/t/p/w500/qNBAXBIQlnOThrVvA6mA2B5ggV6.jpg', // The Batman
            'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg', // Spider-Man: Across the Spider-Verse
            'https://image.tmdb.org/t/p/w500/4m1Au3YkjqsxF8iwQy0fPYSxE0h.jpg', // John Wick: Chapter 4
            'https://image.tmdb.org/t/p/w500/teCy1egGQa0y8ULJvlrDHQKnxBL.jpg', // Guardians of the Galaxy Vol. 3
            'https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg', // Oppenheimer
            'https://image.tmdb.org/t/p/w500/5gzzkjEkpT1nZAWArC6jlTUgUto.jpg', // The Little Mermaid
            'https://image.tmdb.org/t/p/w500/yF1eOkaYvwiORauRCPWznV9xVvi.jpg', // The Woman King
            'https://image.tmdb.org/t/p/w500/r2J02Z2OpNTctfOSN1Ydgii51I3.jpg', // Glass Onion
            'https://image.tmdb.org/t/p/w500/lYhA2Oy4r5T9s6ENxdxmQe95Kf6.jpg', // Dune: Part Two
            'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg', // Indiana Jones 5
            'https://image.tmdb.org/t/p/w500/wjQXZTlFM3PVEUmKf1sUajjygqT.jpg', // Fast X
            'https://image.tmdb.org/t/p/w500/2CAL2433ZeIihfX1Hb2139CX0pW.jpg', // Scream VI
            'https://image.tmdb.org/t/p/w500/vZloFAK7NmvMGKE7VkF5UHaz0I.jpg', // Dungeons & Dragons
            'https://image.tmdb.org/t/p/w500/6DrHO1jr3qVrViUO6s6kFiAGM7.jpg', // Shazam! Fury of the Gods
            'https://image.tmdb.org/t/p/w500/A7AoNT06aRAc4SV89Dwxj3EYAgC.jpg', // Evil Dead Rise
            'https://image.tmdb.org/t/p/w500/nDP33LmQwNsnPv29GQazz59HjJI.jpg'  // Air
        ];

        return Faker::create()->randomElement($realPosterUrls);
    }

    private function showStatistics()
    {
        $total = FutureMovie::count();
        $byStatus = FutureMovie::select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->get();

        $byGenre = FutureMovie::select('genre')
            ->selectRaw('count(*) as count')
            ->groupBy('genre')
            ->orderBy('count', 'desc')
            ->get();

        $this->command->info("\n📊 Future Movies İstatistikleri:");
        $this->command->info("📈 Toplam: {$total}");
        
        $this->command->info("\n📋 Status Dağılımı:");
        foreach ($byStatus as $stat) {
            $this->command->info("  - {$stat->status}: {$stat->count}");
        }

        $this->command->info("\n🎭 Tür Dağılımı:");
        foreach ($byGenre->take(5) as $stat) {
            $this->command->info("  - {$stat->genre}: {$stat->count}");
        }
    }
}