<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\FutureMovie;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MovieController extends Controller
{
    /**
     * Display a listing of movies
     */
    public function index(Request $request): JsonResponse
    {
        $query = Movie::query();

        // ✅ NOW SHOWING: Sadece release_date'i bugün veya öncesi olan filmler
        // release_date DATE tipinde ve Carbon cast ediliyor, bu yüzden whereDate kullanabiliriz
        $today = now()->startOfDay();
        
        // DATE tipi Carbon'a cast edildiği için whereDate direkt çalışır
        // Ayrıca string formatında saklanan tarihler için de STR_TO_DATE kontrolü ekliyoruz
        $query->where(function($q) use ($today) {
            // Carbon date cast için (DATE tipi)
            $q->whereDate('release_date', '<=', $today)
              // Eğer string formatında saklanıyorsa (d-m-Y formatı)
              ->orWhereRaw("STR_TO_DATE(release_date, '%d-%m-%Y') <= ?", [$today->format('Y-m-d')])
              // NULL değerler için - bunlar Now Showing olarak kabul edilir
              ->orWhereNull('release_date');
        });

        // Arama filtresi
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Tür filtresi
        if ($request->filled('genre')) {
            $query->where('genre', 'like', '%' . $request->genre . '%');
        }

        // Yıl filtresi
        if ($request->filled('year')) {
            $query->where(function($q) use ($request) {
                $q->whereYear('release_date', $request->year)
                  ->orWhereRaw("YEAR(STR_TO_DATE(release_date, '%d-%m-%Y')) = ?", [$request->year]);
            });
        }

        // IMDB rating filtresi
        if ($request->filled('min_rating')) {
            $query->where('imdb_raiting', '>=', $request->min_rating);
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Varsayılan olarak sadece aktif filmler
            $query->where('status', 'active');
        }

        // Şehir filtresi - Bu şehirdeki sinemalarda gösterilen filmler
        if ($request->filled('city_id') && $request->city_id != '0') {
            $cityId = (int) $request->city_id;
            \Log::info('MovieController - Şehir filtresi aktif:', ['city_id' => $cityId]);
            
            $query->whereHas('showtimes', function ($q) use ($cityId) {
                $q->where('status', 'active')
                  ->where('start_time', '>', now())
                  ->whereHas('hall.cinema', function ($cq) use ($cityId) {
                      $cq->where('city_id', $cityId);
                  });
            });
        } else {
            \Log::info('MovieController - Şehir filtresi yok, tüm filmler');
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'release_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Release date için özel sıralama
        if ($sortBy === 'release_date') {
            $query->orderByRaw("STR_TO_DATE(release_date, '%Y-%m-%d') DESC, STR_TO_DATE(release_date, '%d-%m-%Y') DESC");
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Sayfalama - Toplam 100 film için limit kullan
        // Eğer total_movies parametresi varsa, bu toplam film sayısıdır (örn: 100)
        // ve bu sayıya göre Now Showing ve Coming Soon'a dağıtılacak
        $totalMovies = $request->get('total_movies', null);
        
        if ($totalMovies !== null) {
            // Toplam film sayısı belirtilmişse, sadece limit uygula (sayfalama yok)
            $limit = min((int)$totalMovies, 100);
            $movies = $query->limit($limit)->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Movies retrieved successfully',
                'data' => [
                    'data' => $movies,
                    'total' => $movies->count(),
                    'current_page' => 1,
                    'per_page' => $limit,
                ]
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                ->header('Content-Type', 'application/json; charset=utf-8');
        } else {
            // Normal sayfalama
            $perPage = $request->get('per_page', 100);
            $movies = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Movies retrieved successfully',
                'data' => $movies
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                ->header('Content-Type', 'application/json; charset=utf-8');
        }
    }

    /**
     * Get distributed movies - Toplam 100 filmi tarihe göre dağıt
     * Now Showing ve Coming Soon filmlerini birleştirip toplam 100 film döndür
     * Tarihi geçen filmler kesinlikle Coming Soon'a atılmaz
     */
    public function distributed(Request $request): JsonResponse
    {
        try {
            $today = now()->startOfDay();
            $sixMonthsAgo = now()->subMonths(6)->startOfDay(); // Son 6 ay - çok eski filmleri hariç tut
            $nowShowingLimit = 70; // Now Showing için 70 film
            $comingSoonLimit = 50; // Coming Soon için 50 film
            
            // Şehir filtresi
            $cityId = $request->filled('city_id') && $request->city_id != '0' ? (int)$request->city_id : null;
            
            // ✅ NOW SHOWING: release_date <= bugün VE >= 6 ay önce (çok eski filmleri hariç tut)
            // Sadece movies tablosundan çek - yakın tarihli filmler (son 6 ay)
            $nowShowingQuery = Movie::where('status', 'active')
                ->where(function($q) use ($today, $sixMonthsAgo) {
                    // Son 6 ay içindeki filmler (bugün dahil, 6 ay öncesinden sonra)
                    $q->whereBetween('release_date', [$sixMonthsAgo, $today])
                      // NULL değerler - bunlar Now Showing olarak kabul edilir (yakın tarihli sayılır)
                      ->orWhereNull('release_date');
                });
            
            // Şehir filtresi varsa uygula
            if ($cityId) {
                $nowShowingQuery->whereHas('showtimes', function ($q) use ($cityId) {
                    $q->where('status', 'active')
                      ->where('start_time', '>', now())
                      ->whereHas('hall.cinema', function ($cq) use ($cityId) {
                          $cq->where('city_id', $cityId);
                      });
                });
            }
            
            // ✅ COMING SOON: release_date > bugün (KESİNLİKLE gelecekte olmalı)
            // Hem FutureMovie hem de Movie tablosundan gelecek tarihli filmleri çek
            // Tarihi geçen filmler kesinlikle Coming Soon'a GİTMEZ
            
            // FutureMovie tablosundan gelecek tarihli filmler
            $comingSoonQuery = FutureMovie::where(function($q) use ($today) {
                // DATE tipi Carbon cast için - KESİNLİKLE bugünden SONRA
                $q->whereDate('release_date', '>', $today)
                  // String formatı (d-m-Y) - KESİNLİKLE bugünden SONRA
                  ->orWhereRaw("STR_TO_DATE(release_date, '%d-%m-%Y') > STR_TO_DATE(?, '%Y-%m-%d')", [$today->format('Y-m-d')]);
            });
            
            // Movie tablosundan gelecek tarihli filmler (bunlar da Coming Soon'a gider)
            $futureMoviesFromMoviesTable = Movie::where('status', 'active')
                ->where(function($q) use ($today) {
                    // KESİNLİKLE bugünden SONRA olmalı - tarihi geçen filmler buraya GİRMEZ
                    $q->whereDate('release_date', '>', $today)
                      ->orWhereRaw("STR_TO_DATE(release_date, '%d-%m-%Y') > STR_TO_DATE(?, '%Y-%m-%d')", [$today->format('Y-m-d')]);
                });
            
            // Şehir filtresi varsa Movie tablosundaki gelecek filmlere de uygula
            if ($cityId) {
                $futureMoviesFromMoviesTable->whereHas('showtimes', function ($q) use ($cityId) {
                    $q->where('status', 'active')
                      ->where('start_time', '>', now())
                      ->whereHas('hall.cinema', function ($cq) use ($cityId) {
                          $cq->where('city_id', $cityId);
                      });
                });
            }
            
            // Toplam film sayılarını kontrol et
            $nowShowingCount = $nowShowingQuery->count();
            $comingSoonCount = $comingSoonQuery->count();
            $futureMoviesFromMoviesCount = $futureMoviesFromMoviesTable->count();
            $totalComingSoon = $comingSoonCount + $futureMoviesFromMoviesCount;
            
            // Limitleri uygula - Now Showing 70, Coming Soon 50
            $finalNowShowingLimit = min($nowShowingLimit, $nowShowingCount);
            $finalComingSoonLimit = min($comingSoonLimit, $totalComingSoon);
            
            // Now Showing filmlerini çek - son 6 ay içindeki filmler (yeni tarihli olanlar)
            $nowShowingMovies = $nowShowingQuery
                ->orderByRaw("STR_TO_DATE(release_date, '%Y-%m-%d') DESC, STR_TO_DATE(release_date, '%d-%m-%Y') DESC")
                ->limit($finalNowShowingLimit)
                ->get();
            
            // Coming Soon filmlerini çek - hem FutureMovie hem Movie tablosundan, KESİNLİKLE gelecekteki filmler
            // Önce FutureMovie'den, sonra Movie'den
            $comingSoonLimitFromFuture = min($finalComingSoonLimit, $comingSoonCount);
            $comingSoonLimitFromMovies = $finalComingSoonLimit - $comingSoonLimitFromFuture;
            
            $comingSoonMoviesFromFuture = $comingSoonQuery
                ->orderByRaw("STR_TO_DATE(release_date, '%Y-%m-%d') ASC, STR_TO_DATE(release_date, '%d-%m-%Y') ASC")
                ->limit($comingSoonLimitFromFuture)
                ->get();
            
            $comingSoonMoviesFromMovies = $futureMoviesFromMoviesTable
                ->orderByRaw("STR_TO_DATE(release_date, '%Y-%m-%d') ASC, STR_TO_DATE(release_date, '%d-%m-%Y') ASC")
                ->limit($comingSoonLimitFromMovies)
                ->get();
            
            // İki kaynaktan gelen filmleri birleştir
            $comingSoonMovies = $comingSoonMoviesFromFuture->concat($comingSoonMoviesFromMovies);

            // ✅ Double-check classification to avoid swapped lists on mobile
            $nowShowingMovies = $this->filterMoviesByCategory($nowShowingMovies, $today, 'now_showing');
            $comingSoonMovies = $this->filterMoviesByCategory($comingSoonMovies, $today, 'coming_soon');
            
            return response()->json([
                'success' => true,
                'message' => 'Distributed movies retrieved successfully',
                'data' => [
                    'now_showing' => [
                        'data' => $nowShowingMovies,
                        'total' => $nowShowingMovies->count(),
                    ],
                    'coming_soon' => [
                        'data' => $comingSoonMovies,
                        'total' => $comingSoonMovies->count(),
                    ],
                    'total' => $nowShowingMovies->count() + $comingSoonMovies->count(),
                    'distribution' => [
                        'now_showing_count' => $nowShowingMovies->count(),
                        'coming_soon_count' => $comingSoonMovies->count(),
                    ]
                ]
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                ->header('Content-Type', 'application/json; charset=utf-8');
            
        } catch (\Exception $e) {
            \Log::error('MovieController::distributed error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve distributed movies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created movie
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'language' => 'required|string|max:10',
            'release_date' => 'required|date',
            'genre' => 'required|string|max:255',
            'poster_url' => 'nullable|url',
            'imdb_raiting' => 'nullable|numeric|between:0,10',
            'status' => 'nullable|in:active,inactive'
        ]);

        $movie = Movie::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Movie created successfully',
            'data' => $movie
        ], 201);
    }

    /**
     * Display the specified movie
     */
    public function show(string $id): JsonResponse
    {
        $movie = Movie::with(['showtimes.hall.cinema.city'])->find($id);

        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Movie not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Movie retrieved successfully',
            'data' => $movie
        ]);
    }

    /**
     * Update the specified movie
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Movie not found'
            ], 404);
        }

        $movie->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Movie updated successfully',
            'data' => $movie
        ]);
    }
    

    /**
     * Remove the specified movie
     */
    public function destroy(string $id): JsonResponse
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Movie not found'
            ], 404);
        }

        // Eğer aktif seansları varsa silmeyi engelle
        if ($movie->showtimes()->where('status', 'active')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete movie with active showtimes'
            ], 422);
        }

        $movie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Movie deleted successfully'
        ]);
    }

    /**
     * Get movies by genre
     */
    public function byGenre(string $genre): JsonResponse
    {
        $movies = Movie::where('genre', 'like', "%{$genre}%")
                      ->where('status', 'active')
                      ->orderBy('imdb_raiting', 'desc')
                      ->get();

        return response()->json([
            'success' => true,
            'message' => "Movies in {$genre} genre retrieved successfully",
            'data' => $movies
        ]);
    }

    /**
     * Get popular movies (high IMDB rating)
     */
    public function popular(): JsonResponse
    {
        $movies = Movie::where('imdb_raiting', '>=', 7.0)
                      ->where('status', 'active')
                      ->orderBy('imdb_raiting', 'desc')
                      ->limit(20)
                      ->get();

        return response()->json([
            'success' => true,
            'message' => 'Popular movies retrieved successfully',
            'data' => $movies
        ]);
    }

    /**
     * Get now showing movies (have active showtimes)
     */
    public function nowShowing(): JsonResponse
    {
        $movies = Movie::whereHas('showtimes', function ($query) {
                        $query->where('status', 'active')
                              ->where('start_time', '>', now());
                    })
                    ->where('status', 'active')
                    ->with(['showtimes' => function ($query) {
                        $query->where('status', 'active')
                              ->where('start_time', '>', now())
                              ->orderBy('start_time');
                    }])
                    ->orderBy('release_date', 'desc')
                    ->get();

        return response()->json([
            'success' => true,
            'message' => 'Now showing movies retrieved successfully',
            'data' => $movies
        ]);
    }


// MovieController.php dosyanızın sonuna, } 'den önce bu metotları ekleyin:

/**
 * Get cinemas showing specific movie
 */
public function getCinemasForMovie(string $movieId, Request $request): JsonResponse
{
    try {
        // Movie var mı kontrol et
        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Film bulunamadı'
            ], 404);
        }

        // Bu filme ait sinemalar
        $cinemas = Cinema::whereHas('halls.showtimes', function ($q) use ($movieId) {
                    $q->where('movie_id', $movieId)
                      ->where('status', 'active')
                      ->where('start_time', '>', now());
                })
                ->with([
                    'city',
                    'halls' => function ($query) use ($movieId) {
                        $query->whereHas('showtimes', function ($q) use ($movieId) {
                            $q->where('movie_id', $movieId)
                              ->where('status', 'active')
                              ->where('start_time', '>', now());
                        });
                    },
                    'halls.showtimes' => function ($query) use ($movieId) {
                        $query->where('movie_id', $movieId)
                              ->where('status', 'active')
                              ->where('start_time', '>', now())
                              ->orderBy('start_time');
                    }
                ])
                ->get();

        // Mevcut şehirleri de döndür
        $availableCities = $cinemas->pluck('city')
                                 ->filter()
                                 ->unique('id')
                                 ->values();

        return response()->json([
            'success' => true,
            'message' => 'Cinemas showing this movie retrieved successfully',
            'data' => [
                'cinemas' => $cinemas,
                'available_cities' => $availableCities,
                'total_cinemas' => $cinemas->count()
            ]
        ]);

    } catch (\Exception $e) {
     
        return response()->json([
            'success' => false,
            'message' => 'Sinemalar yüklenirken hata oluştu: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Get showtimes for movie
 */
public function getShowtimesForMovie(string $movieId, Request $request): JsonResponse
{
    try {
        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Film bulunamadı'
            ], 404);
        }

        $query = Showtime::where('movie_id', $movieId)
                    ->where('status', 'active')
                    ->where('start_time', '>', now())
                    ->with(['hall.cinema.city', 'movie']);

        // Sinema filtresi
        if ($request->filled('cinema_id')) {
            $query->whereHas('hall', function($q) use ($request) {
                $q->where('cinema_id', $request->cinema_id);
            });
        }

        $showtimes = $query->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'message' => 'Showtimes retrieved successfully',
            'data' => $showtimes
        ]);

    } catch (\Exception $e) {

        
        return response()->json([
            'success' => false,
            'message' => 'Seanslar yüklenirken hata oluştu: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Normalize release date strings/carbon instances coming from different models.
     */
    private function normalizeReleaseDate(mixed $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->copy()->startOfDay();
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->startOfDay();
        }

        $stringValue = is_string($value) ? trim($value) : (string) $value;

        if ($stringValue === '') {
            return null;
        }

        $formats = [
            'Y-m-d',
            'Y-m-d H:i:s',
            'd-m-Y',
            'd/m/Y',
            'm/d/Y',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $stringValue)->startOfDay();
            } catch (\Throwable $th) {
                continue;
            }
        }

        try {
            return Carbon::parse($stringValue)->startOfDay();
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Ensures that a collection only contains items that belong to the desired category.
     */
    private function filterMoviesByCategory(Collection $collection, Carbon $today, string $category): Collection
    {
        return $collection->filter(function ($movie) use ($today, $category) {
            $releaseDate = $this->normalizeReleaseDate($movie->release_date ?? null);
            $isNowShowing = !$releaseDate || $releaseDate->lessThanOrEqualTo($today);
            $isComingSoon = $releaseDate && $releaseDate->greaterThan($today);

            if ($category === 'now_showing' && $isNowShowing) {
                $movie->category = 'now_showing';
                return true;
            }

            if ($category === 'coming_soon' && $isComingSoon) {
                $movie->category = 'coming_soon';
                $movie->days_until_release = $movie->days_until_release
                    ?? $this->calculateDaysUntilRelease($releaseDate);
                return true;
            }

            return false;
        })->values();
    }

    private function calculateDaysUntilRelease(?Carbon $releaseDate): ?int
    {
        if (!$releaseDate) {
            return null;
        }

        $today = now()->startOfDay();

        if ($releaseDate->lessThanOrEqualTo($today)) {
            return 0;
        }

        return $today->diffInDays($releaseDate);
    }
}
