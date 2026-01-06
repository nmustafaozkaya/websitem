<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\City;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CinemaController extends Controller
{
    /**
     * Display a listing of cinemas
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Memory limit ve execution time artır (büyük response'lar için)
            $originalMemoryLimit = ini_get('memory_limit');
            $originalMaxExecutionTime = ini_get('max_execution_time');
            ini_set('memory_limit', '512M');
            set_time_limit(60);

            $query = Cinema::with(['city:id,name']);

            // Şehir filtresi
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }

            // Şehir adı ile filtre
            if ($request->filled('city_name')) {
                $query->whereHas('city', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->city_name . '%');
                });
            }

            // Arama filtresi
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('address', 'like', '%' . $request->search . '%');
                });
            }

            // Aktif sinemalar
            if ($request->boolean('active_only', true)) {
                $query->whereNull('deleted_at');
            }

            // Sadece gerekli alanları select et (performans için)
            $cinemas = $query->select([
                'id',
                'name',
                'address',
                'phone',
                'email',
                'city_id'
            ])->get();

            // Response'u optimize et - JSON encoding için
            $responseData = [
                'success' => true,
                'message' => 'Cinemas retrieved successfully',
                'data' => $cinemas
            ];

            // JSON encoding sırasında hata olursa yakala
            $jsonResponse = json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($jsonResponse === false) {
                $error = json_last_error_msg();
                throw new \Exception("JSON encoding error: $error");
            }

            // Ayarları geri yükle
            if ($originalMemoryLimit) {
                ini_set('memory_limit', $originalMemoryLimit);
            }
            if ($originalMaxExecutionTime) {
                set_time_limit($originalMaxExecutionTime);
            }

            return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
        } catch (\Exception $e) {
            // Ayarları geri yükle (hata durumunda)
            if (isset($originalMemoryLimit)) {
                ini_set('memory_limit', $originalMemoryLimit);
            }
            if (isset($originalMaxExecutionTime)) {
                set_time_limit($originalMaxExecutionTime);
            }

            return response()->json([
                'success' => false,
                'message' => 'Sinemalar yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created cinema
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'city_id' => 'required|exists:cities,id'
        ]);

        $cinema = Cinema::create($request->all());
        $cinema->load(['city', 'halls']);

        return response()->json([
            'success' => true,
            'message' => 'Cinema created successfully',
            'data' => $cinema
        ], 201);
    }

    /**
     * Display the specified cinema
     */
    public function show(string $id): JsonResponse
    {
        $cinema = Cinema::with([
            'city',
            'halls.seats',
            'halls.showtimes.movie'
        ])->find($id);

        if (!$cinema) {
            return response()->json([
                'success' => false,
                'message' => 'Cinema not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cinema retrieved successfully',
            'data' => $cinema
        ]);
    }

    /**
     * Update the specified cinema
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $cinema = Cinema::find($id);

        if (!$cinema) {
            return response()->json([
                'success' => false,
                'message' => 'Cinema not found'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'city_id' => 'sometimes|required|exists:cities,id'
        ]);

        $cinema->update($request->all());
        $cinema->load(['city', 'halls']);

        return response()->json([
            'success' => true,
            'message' => 'Cinema updated successfully',
            'data' => $cinema
        ]);
    }

    /**
     * Remove the specified cinema
     */
    public function destroy(string $id): JsonResponse
    {
        $cinema = Cinema::find($id);

        if (!$cinema) {
            return response()->json([
                'success' => false,
                'message' => 'Cinema not found'
            ], 404);
        }

        // Aktif seansları kontrol et
        $hasActiveShowtimes = $cinema->halls()
            ->whereHas('showtimes', function ($query) {
                $query->where('status', 'active')
                      ->where('start_time', '>', now());
            })->exists();

        if ($hasActiveShowtimes) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete cinema with active showtimes'
            ], 422);
        }

        $cinema->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cinema deleted successfully'
        ]);
    }

    /**
     * Get cinemas by city
     */
    public function byCity(string $cityId): JsonResponse
    {
        $city = City::find($cityId);

        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ], 404);
        }

        $cinemas = Cinema::where('city_id', $cityId)
                         ->with(['halls'])
                         ->get();

        return response()->json([
            'success' => true,
            'message' => "Cinemas in {$city->name} retrieved successfully",
            'data' => $cinemas
        ]);
    }

    /**
     * Get all cities with cinema count
     */
    public function cities(): JsonResponse
    {
        $cities = City::withCount('cinemas')
                     ->having('cinemas_count', '>', 0)
                     ->get();

        return response()->json([
            'success' => true,
            'message' => 'Cities with cinemas retrieved successfully',
            'data' => $cities
        ]);
    }

    /**
     * Get cinemas showing specific movie
     */
    public function showingMovie(string $movieId): JsonResponse
    {
        $cinemas = Cinema::whereHas('halls.showtimes', function ($query) use ($movieId) {
                         $query->where('movie_id', $movieId)
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
                             })->select(['id', 'name', 'cinema_id']); // Sadece gerekli alanlar
                         },
                         // Showtimes'ı yükleme - sadece cinema listesi için gereksiz
                         // Showtimes'ları ayrı bir endpoint'ten yükleyeceğiz
                     ])
                     ->select(['id', 'name', 'address', 'city_id']) // Sadece gerekli alanlar
                     ->limit(100) // Response boyutunu kontrol et
                     ->get();

        return response()->json([
            'success' => true,
            'message' => 'Cinemas showing this movie retrieved successfully',
            'data' => $cinemas
        ]);
    }

// MovieController.php içine eklenecek method

/**
 * Get cinemas showing specific movie
 */
public function getCinemasForMovie(string $movieId, Request $request): JsonResponse
{
    try {
        $query = Cinema::whereHas('halls.showtimes', function ($q) use ($movieId) {
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
                ]);

        // Şehir filtresi
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Şehir adı ile filtre
        if ($request->filled('city_name')) {
            $query->whereHas('city', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->city_name . '%');
            });
        }

        $cinemas = $query->get();

        // Mevcut şehirleri de döndür
        $availableCities = Cinema::whereHas('halls.showtimes', function ($q) use ($movieId) {
                                $q->where('movie_id', $movieId)
                                  ->where('status', 'active')
                                  ->where('start_time', '>', now());
                            })
                            ->with('city')
                            ->get()
                            ->pluck('city')
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

        // Şehir filtresi
        if ($request->filled('city_id')) {
            $query->whereHas('hall.cinema', function($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        // Tarih filtresi
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
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
}