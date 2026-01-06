<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\Cinema;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HallController extends Controller
{
    /**
     * Display a listing of halls
     */
    public function index(Request $request): JsonResponse
    {
        $query = Hall::with(['cinema']);

        // Sinema filtresi
        if ($request->filled('cinema_id')) {
            $query->where('cinema_id', $request->cinema_id);
        }

        // Sinema adı ile filtre
        if ($request->filled('cinema_name')) {
            $query->whereHas('cinema', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->cinema_name . '%');
            });
        }

        // Arama filtresi
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Aktif salonlar
        if ($request->boolean('active_only', true)) {
            $query->where('status', 'active')
                  ->whereNull('deleted_at');
        }

        $halls = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Halls retrieved successfully',
            'data' => $halls
        ]);
    }

    /**
     * Store a newly created hall
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cinema_id' => 'required|exists:cinemas,id',
            'capacity' => 'required|integer|min:1|max:1000',
            'status' => 'sometimes|in:active,inactive,maintenance'
        ]);

        $hall = Hall::create($request->all());
        $hall->load(['cinema']);

        return response()->json([
            'success' => true,
            'message' => 'Hall created successfully',
            'data' => $hall
        ], 201);
    }

    /**
     * Display the specified hall
     */
    public function show(string $id): JsonResponse
    {
        $hall = Hall::with([
            'cinema',
            'seats',
            'showtimes.movie'
        ])->find($id);

        if (!$hall) {
            return response()->json([
                'success' => false,
                'message' => 'Hall not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hall retrieved successfully',
            'data' => $hall
        ]);
    }

    /**
     * Update the specified hall
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $hall = Hall::find($id);

        if (!$hall) {
            return response()->json([
                'success' => false,
                'message' => 'Hall not found'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'cinema_id' => 'sometimes|required|exists:cinemas,id',
            'capacity' => 'sometimes|required|integer|min:1|max:1000',
            'status' => 'sometimes|in:active,inactive,maintenance'
        ]);

        $hall->update($request->all());
        $hall->load(['cinema']);

        return response()->json([
            'success' => true,
            'message' => 'Hall updated successfully',
            'data' => $hall
        ]);
    }

    /**
     * Remove the specified hall
     */
    public function destroy(string $id): JsonResponse
    {
        $hall = Hall::find($id);

        if (!$hall) {
            return response()->json([
                'success' => false,
                'message' => 'Hall not found'
            ], 404);
        }

        // Aktif seansları kontrol et
        $hasActiveShowtimes = $hall->showtimes()
            ->where('status', 'active')
            ->where('start_time', '>', now())
            ->exists();

        if ($hasActiveShowtimes) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete hall with active showtimes'
            ], 422);
        }

        $hall->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hall deleted successfully'
        ]);
    }

    /**
     * Get halls by cinema
     */
    public function byCinema(string $cinemaId): JsonResponse
    {
        $cinema = Cinema::find($cinemaId);

        if (!$cinema) {
            return response()->json([
                'success' => false,
                'message' => 'Cinema not found'
            ], 404);
        }

        $halls = Hall::where('cinema_id', $cinemaId)
                    ->with(['seats'])
                    ->get();

        return response()->json([
            'success' => true,
            'message' => "Halls in {$cinema->name} retrieved successfully",
            'data' => $halls
        ]);
    }

    /**
     * Get halls with seat map
     */
    public function withSeatMap(string $id): JsonResponse
    {
        $hall = Hall::with([
            'cinema',
            'seats' => function ($query) {
                $query->orderBy('row')->orderBy('number');
            }
        ])->find($id);

        if (!$hall) {
            return response()->json([
                'success' => false,
                'message' => 'Hall not found'
            ], 404);
        }

        // Koltukları satır bazında grupla
        if ($hall->seats->count() > 0) {
            $seatMap = $hall->seats->groupBy('row')->map(function ($seats) {
                return $seats->sortBy('number')->values();
            });
            $hall->seat_map = $seatMap;
        }

        return response()->json([
            'success' => true,
            'message' => 'Hall with seat map retrieved successfully',
            'data' => $hall
        ]);
    }
}