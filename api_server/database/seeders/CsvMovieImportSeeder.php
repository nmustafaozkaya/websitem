<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CsvMovieImportSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = storage_path('app/movies.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("❌ movies.csv dosyası bulunamadı! storage/app/movies.csv yoluna koyun.");
            return;
        }

        $this->command->info("📂 CSV dosyası bulundu: " . $csvFile);

        // Foreign key constraint'leri güvenli şekilde temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tickets')->truncate();
        DB::table('sales')->truncate();
        DB::table('showtimes')->truncate();
        DB::table('movies')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info("🗑️ Tüm ilişkili veriler güvenli şekilde temizlendi.");

        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            $this->command->error("❌ CSV dosyası açılamadı!");
            return;
        }

        // İlk satırı (başlıkları) atla
        $headers = fgetcsv($handle);
        $this->command->info("📋 Başlıklar: " . implode(', ', $headers));

        $batchSize = 100;
        $batch = [];
        $totalProcessed = 0;
        $totalInserted = 0;

        while (($data = fgetcsv($handle)) !== FALSE) {
            try {
                $totalProcessed++;
                
                // Boş satırları atla
                if (empty($data) || count($data) < 2) {
                    continue;
                }
                
                // Kolon sayısı uyuşmuyorsa atla
                if (count($headers) !== count($data)) {
                    $this->command->info("⚠️ Satır {$totalProcessed}: Kolon sayısı uyuşmuyor (" . count($data) . " vs " . count($headers) . ")");
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
                    DB::table('movies')->insert($batch);
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
            DB::table('movies')->insert($batch);
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

        // Runtime'ı kontrol et (0 veya null olan filmleri atla)
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

        // Genres'i temizle (JSON format'tan normal string'e)
        $genres = $this->extractGenres($row['genres'] ?? '');

        // IMDB rating'i vote_average'dan al
        $imdbRating = null;
        if (!empty($row['vote_average']) && is_numeric($row['vote_average'])) {
            $imdbRating = round((float)$row['vote_average'], 1);
        }

        // Poster URL'i oluştur
        $posterUrl = null;
        if (!empty($row['homepage'])) {
            $posterUrl = $row['homepage'];
        }

        // Language'i kontrol et
        $language = $row['original_language'] ?? 'en';
        if (strlen($language) > 5) {
            $language = 'en';
        }

        // Status'u kontrol et
        $status = 'active';
        if (!empty($row['status']) && in_array(strtolower($row['status']), ['released', 'active'])) {
            $status = 'active';
        }

        return [
            'title' => substr($row['title'], 0, 255), // Maksimum 255 karakter
            'description' => substr($row['overview'] ?? 'Açıklama mevcut değil.', 0, 1000),
            'duration' => $runtime,
            'language' => $language,
            'release_date' => $releaseDate,
            'genre' => $genres,
            'poster_url' => $posterUrl,
            'imdb_raiting' => $imdbRating,
            'status' => $status,
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
                // İlk genre'ın name alanını al
                if (isset($genres[0]['name'])) {
                    return $genres[0]['name'];
                }
            }
        } catch (\Exception $e) {
            // JSON decode hatası
        }

        // Fallback
        return 'Drama';
    }

    private function showStatistics(): void
    {
        $stats = [
            'Toplam Film' => DB::table('movies')->count(),
            'Aktif Filmler' => DB::table('movies')->where('status', 'active')->count(),
            'En Yüksek IMDB' => DB::table('movies')->max('imdb_raiting'),
            'Ortalama IMDB' => round(DB::table('movies')->avg('imdb_raiting'), 1),
            'Ortalama Süre' => round(DB::table('movies')->avg('duration')) . ' dakika',
            'Türkçe Filmler' => DB::table('movies')->where('language', 'tr')->count(),
            'İngilizce Filmler' => DB::table('movies')->where('language', 'en')->count(),
        ];

        $this->command->info("\n📊 FİLM İSTATİSTİKLERİ:");
        foreach ($stats as $key => $value) {
            $this->command->info("   {$key}: {$value}");
        }

        // En popüler türler
        $genres = DB::table('movies')
            ->select('genre', DB::raw('COUNT(*) as count'))
            ->groupBy('genre')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $this->command->info("\n🎭 EN POPÜLER TÜRLER:");
        foreach ($genres as $genre) {
            $this->command->info("   {$genre->genre}: {$genre->count} film");
        }

        // Yıllara göre dağılım
        $years = DB::table('movies')
            ->select(DB::raw('YEAR(release_date) as year'), DB::raw('COUNT(*) as count'))
            ->where('release_date', '>', '1990-01-01')
            ->groupBy(DB::raw('YEAR(release_date)'))
            ->orderBy('year', 'desc')
            ->limit(10)
            ->get();

        $this->command->info("\n📅 SON 10 YILDA EN ÇOK FİLM:");
        foreach ($years as $year) {
            $this->command->info("   {$year->year}: {$year->count} film");
        }
    }
}