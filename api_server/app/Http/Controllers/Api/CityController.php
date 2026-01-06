<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    /**
     * Display a listing of cities
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = City::query();

            // Arama filtresi
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Aktif şehirler
            if ($request->boolean('active_only', true)) {
                $query->whereNull('deleted_at');
            }

            // Sinema sayısı ile birlikte getir
            if ($request->boolean('with_cinema_count', false)) {
                $query->withCount('cinemas');
            }

            $cities = $query->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'message' => 'Cities retrieved successfully',
                'data' => $cities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Şehirler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities with cinemas
     */
    public function withCinemas(): JsonResponse
    {
        try {
            $cities = City::whereHas('cinemas')
                         ->withCount('cinemas')
                         ->with(['cinemas' => function($query) {
                             $query->select('id', 'name', 'city_id');
                         }])
                         ->orderBy('name')
                         ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cities with cinemas retrieved successfully',
                'data' => $cities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sinemaları olan şehirler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created city
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:cities,name',
                'code' => 'nullable|string|max:10|unique:cities,code',
                'country' => 'nullable|string|max:255'
            ]);

            $city = City::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'City created successfully',
                'data' => $city
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Şehir oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified city
     */
    public function show(string $id): JsonResponse
    {
        try {
            $city = City::with(['cinemas.halls'])->find($id);

            if (!$city) {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'City retrieved successfully',
                'data' => $city
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Şehir yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified city
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $city = City::find($id);

            if (!$city) {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found'
                ], 404);
            }

            $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:cities,name,' . $id,
                'code' => 'nullable|string|max:10|unique:cities,code,' . $id,
                'country' => 'nullable|string|max:255'
            ]);

            $city->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'City updated successfully',
                'data' => $city
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Şehir güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified city
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $city = City::find($id);

            if (!$city) {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found'
                ], 404);
            }

            // Eğer bu şehirde sinema varsa silmeyi engelle
            if ($city->cinemas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete city with existing cinemas'
                ], 422);
            }

            $city->delete();

            return response()->json([
                'success' => true,
                'message' => 'City deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Şehir silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}