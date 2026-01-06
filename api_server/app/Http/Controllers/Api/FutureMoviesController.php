<?php
// app/Http/Controllers/Api/FutureMoviesController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FutureMovie;
use Illuminate\Http\Request;

class FutureMoviesController extends Controller
{
    /**
     * Gelecek filmleri listele
     */
    public function index(Request $request)
    {
        try {
            $query = FutureMovie::query();

            // ✅ COMING SOON: Sadece release_date'i bugünden SONRA olan filmler
            // release_date DATE tipinde ve Carbon cast ediliyor, bu yüzden whereDate kullanabiliriz
            $today = now()->startOfDay();
            
            // DATE tipi Carbon'a cast edildiği için whereDate direkt çalışır
            // Sadece gelecekteki tarihleri göster (bugünden SONRA)
            $query->whereDate('release_date', '>', $today);

            // Arama
            if ($request->has('search') && !empty($request->search)) {
                $query->search($request->search);
            }

            // Tür filtresi
            if ($request->has('genre') && !empty($request->genre)) {
                $query->byGenre($request->genre);
            }

            // Status filtresi
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Dil filtresi
            if ($request->has('language') && !empty($request->language)) {
                $query->where('language', $request->language);
            }

            // Sıralama
            $sortBy = $request->get('sort_by', 'release_date');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['release_date', 'title', 'imdb_raiting', 'created_at'])) {
                if ($sortBy === 'release_date') {
                    // Tarih sıralaması için özel işlem
                    $query->orderByRaw("STR_TO_DATE(release_date, '%Y-%m-%d') {$sortOrder}, STR_TO_DATE(release_date, '%d-%m-%Y') {$sortOrder}");
                } else {
                    $query->orderBy($sortBy, $sortOrder);
                }
            }

            // Sayfalama - Toplam 100 film için limit kullan
            $totalMovies = $request->get('total_movies', null);
            
            if ($totalMovies !== null) {
                // Toplam film sayısı belirtilmişse, sadece limit uygula (sayfalama yok)
                $limit = min((int)$totalMovies, 100);
                $movies = $query->limit($limit)->get();
                
                // Response'a ekstra bilgiler ekle
                $movies->transform(function ($movie) {
                    $movie->days_until_release = $movie->days_until_release;
                    $movie->status_label = $movie->status_label;
                    return $movie;
                });
                
                return response()->json([
                    'success' => true,
                    'message' => 'Future movies retrieved successfully',
                    'data' => [
                        'data' => $movies,
                        'total' => $movies->count(),
                        'current_page' => 1,
                        'per_page' => $limit,
                    ]
                ]);
            } else {
                // Normal sayfalama
                $perPage = min($request->get('per_page', 15), 100);
                $movies = $query->paginate($perPage);

                // Response'a ekstra bilgiler ekle
                $movies->getCollection()->transform(function ($movie) {
                    $movie->days_until_release = $movie->days_until_release;
                    $movie->status_label = $movie->status_label;
                    return $movie;
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Future movies retrieved successfully',
                    'data' => $movies
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve future movies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tekil gelecek film detayı
     */
    public function show($id)
    {
        try {
            $movie = FutureMovie::findOrFail($id);
            
            $movie->days_until_release = $movie->days_until_release;
            $movie->status_label = $movie->status_label;

            return response()->json([
                'success' => true,
                'message' => 'Future movie retrieved successfully',
                'data' => $movie
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Future movie not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Yakında çıkacak filmler (30 gün içinde)
     */
    public function comingSoon(Request $request)
    {
        try {
            $perPage = min($request->get('per_page', 10), 20);
            
            // comingSoon scope zaten bugünden sonraki 30 gün içindeki filmleri filtreler
            $movies = FutureMovie::comingSoon()
                ->orderBy('release_date', 'asc')
                ->paginate($perPage);

            $movies->getCollection()->transform(function ($movie) {
                $movie->days_until_release = $movie->days_until_release;
                $movie->status_label = $movie->status_label;
                return $movie;
            });

            return response()->json([
                'success' => true,
                'message' => 'Coming soon movies retrieved successfully',
                'data' => $movies
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve coming soon movies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pre-order için uygun filmler (1 hafta içinde)
     */
    public function preOrder(Request $request)
    {
        try {
            $perPage = min($request->get('per_page', 100), 100);
            
            $query = FutureMovie::preOrder();

            // Şehir filtresi - gelecekte seansları olan filmler için
            if ($request->filled('city_id') && $request->city_id != '0') {
                // FutureMovie'ler henüz seansları olmayabilir, bu yüzden şimdilik şehir filtresini uygulamıyoruz
                // İleride FutureMovie'ye showtime ilişkisi eklendiğinde buraya eklenebilir
            }

            $movies = $query->orderByRaw("STR_TO_DATE(release_date, '%d-%m-%Y') ASC")
                ->paginate($perPage);

            $movies->getCollection()->transform(function ($movie) {
                $movie->days_until_release = $movie->days_until_release;
                $movie->status_label = $movie->status_label;
                $movie->is_pre_order = true; // Pre-order için işaretle
                return $movie;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pre-order movies retrieved successfully',
                'data' => $movies
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pre-order movies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gelecek filmler için türler
     */
    public function genres()
    {
        try {
            $genres = FutureMovie::select('genre')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('genre')
                ->groupBy('genre')
                ->orderBy('count', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Future movie genres retrieved successfully',
                'data' => $genres
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve genres',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Status'leri listele
     */
    public function statuses()
    {
        $statuses = [
            [
                'value' => FutureMovie::STATUS_UPCOMING,
                'label' => 'Yakında',
                'count' => FutureMovie::where('status', FutureMovie::STATUS_UPCOMING)->count()
            ],
            [
                'value' => FutureMovie::STATUS_PRE_PRODUCTION,
                'label' => 'Ön Prodüksiyon', 
                'count' => FutureMovie::where('status', FutureMovie::STATUS_PRE_PRODUCTION)->count()
            ],
            [
                'value' => FutureMovie::STATUS_IN_PRODUCTION,
                'label' => 'Çekim Aşamasında',
                'count' => FutureMovie::where('status', FutureMovie::STATUS_IN_PRODUCTION)->count()
            ],
            [
                'value' => FutureMovie::STATUS_POST_PRODUCTION,
                'label' => 'Post Prodüksiyon',
                'count' => FutureMovie::where('status', FutureMovie::STATUS_POST_PRODUCTION)->count()
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Future movie statuses retrieved successfully',
            'data' => $statuses
        ]);
    }
}

