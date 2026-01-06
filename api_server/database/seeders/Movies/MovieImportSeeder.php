<?php

namespace Database\Seeders\Movies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Movie;
use Carbon\Carbon;

class MovieImportSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = storage_path('app/movies.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("❌ movies.csv dosyası bulunamadı! storage/app/movies.csv yoluna koyun.");
            return;
        }

        $this->command->info("📂 CSV dosyası bulundu: " . $csvFile);
        $this->command->info("🗑️ Mevcut filmler temizleniyor...");

        // Foreign key constraint'leri güvenli şekilde temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('movies')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            $this->command->error("❌ CSV dosyası açılamadı!");
            return;
        }

        // İlk satırı (başlıkları) al
        $headers = fgetcsv($handle);
        $this->command->info("📋 Başlıklar: " . implode(', ', $headers));

        $batchSize = 100;
        $batch = [];
        $totalProcessed = 0;
        $totalInserted = 0;

        while (($data = fgetcsv($handle)) !== FALSE) {
            if ($totalInserted >= 100) break; // 🚨 100 filmden sonra dur
        
            try {
                $totalProcessed++;
        
                // Boş satırları atla
                if (empty($data) || count($data) < 2) {
                    continue;
                }
        
                // Kolon sayısı uyuşmuyorsa atla
                if (count($headers) !== count($data)) {
                    $this->command->info("⚠️ Satır {$totalProcessed}: Kolon sayısı uyuşmuyor");
                    continue;
                }
        
                // CSV satırını associative array'e çevir
                $row = array_combine($headers, $data);
        
                // Veri temizleme ve dönüştürme
                $movieData = $this->processMovieData($row);
        
                if ($movieData) {
                    $batch[] = $movieData;
                    $totalInserted++;
                }
        
                // Batch insert
                if (count($batch) >= $batchSize) {
                    Movie::insert($batch);
                    $this->command->info("📦 {$totalInserted} film eklendi... (Toplam işlenen: {$totalProcessed})");
                    $batch = [];
                }
        
            } catch (\Exception $e) {
                $this->command->info("⚠️ Satır {$totalProcessed} atlandı: " . $e->getMessage());
                continue;
            }
        }

        // Son batch'i ekle
        if (!empty($batch)) {
            Movie::insert($batch);
        }

        fclose($handle);

        $this->command->info("✅ Import tamamlandı!");
        $this->command->info("📊 Toplam işlenen: {$totalProcessed}");
        $this->command->info("📊 Başarıyla eklenen: {$totalInserted}");

        // İstatistikleri göster
        $this->showStatistics();
    }

    private function processMovieData($row): ?array
    {
        // Gerekli alanları kontrol et
        if (empty($row['title']) || empty($row['release_date'])) {
            return null;
        }

        // Runtime'ı kontrol et
        $runtime = (int)($row['runtime'] ?? 0);
        if ($runtime <= 0 || $runtime > 300) {
            $runtime = 120; // Varsayılan süre
        }

        // Release date'i kontrol et ve format et
        $releaseDate = null;
        if (!empty($row['release_date'])) {
            try {
                $releaseDate = Carbon::parse($row['release_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $releaseDate = '2020-01-01'; // Varsayılan tarih
            }
        }

        // Genres'i temizle
        $genres = $this->extractGenres($row['genres'] ?? '');

        // IMDB rating'i vote_average'dan al
        $imdbRating = null;
        if (!empty($row['vote_average']) && is_numeric($row['vote_average'])) {
            $imdbRating = round((float)$row['vote_average'], 1);
        }

        // Language'i kontrol et
        $language = $row['original_language'] ?? 'en';
        if (strlen($language) > 5) {
            $language = 'en';
        }

        return [
            'title' => substr($row['title'], 0, 255),
            'description' => substr($row['overview'] ?? 'Açıklama mevcut değil.', 0, 1000),
            'duration' => $runtime,
            'language' => $language,
            'release_date' => $releaseDate,
            'genre' => $genres,
            'poster_url' => $row['homepage'] ?? null,
            'imdb_raiting' => $imdbRating,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function extractGenres($genresJson): string
    {
        // JSON formatındaki genre'ları düz string'e çevir
        if (empty($genresJson) || $genresJson === '[]') {
            return 'Drama';
        }

        try {
            $genres = json_decode($genresJson, true);
            if (is_array($genres) && count($genres) > 0) {
                if (isset($genres[0]['name'])) {
                    return $genres[0]['name'];
                }
            }
        } catch (\Exception $e) {
            // JSON decode hatası
        }

        return 'Drama';
    }

    private function showStatistics(): void
    {
        $stats = [
            'Toplam Film' => Movie::count(),
            'Aktif Filmler' => Movie::where('status', 'active')->count(),
            'En Yüksek IMDB' => Movie::max('imdb_raiting'),
            'Ortalama IMDB' => round(Movie::avg('imdb_raiting'), 1),
            'Ortalama Süre' => round(Movie::avg('duration')) . ' dakika',
        ];

        $this->command->info("\n📊 FİLM İSTATİSTİKLERİ:");
        foreach ($stats as $key => $value) {
            $this->command->info("   {$key}: {$value}");
        }

        // En popüler türler
        $genres = Movie::select('genre', DB::raw('COUNT(*) as count'))
            ->groupBy('genre')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $this->command->info("\n🎭 EN POPÜLER TÜRLER:");
        foreach ($genres as $genre) {
            $this->command->info("   {$genre->genre}: {$genre->count} film");
        }
    }
}