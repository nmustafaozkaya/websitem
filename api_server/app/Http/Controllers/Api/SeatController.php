<?php
// app/Http/Controllers/Api/SeatController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SeatController extends Controller
{
    public function releaseSeat($id): JsonResponse
    {
        try {
            $seat = Seat::findOrFail($id);
            
            // Sadece pending durumundaki koltukları serbest bırak
            if ($seat->status === Seat::STATUS_PENDING) {
                $seat->markAsAvailable();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Koltuk serbest bırakıldı',
                    'seat' => $seat
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Koltuk zaten müsait veya satılmış'
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Seat release error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Koltuk serbest bırakılırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 10 dakikayı geçen tüm pending koltukları temizle
     */
    public function cleanupExpiredSeats(): JsonResponse
    {
        try {
            // Model'deki scope kullanarak expired koltukları bul
            $cleanedCount = Seat::cleanupExpired();

            return response()->json([
                'success' => true,
                'message' => "{$cleanedCount} expired seat(s) cleaned up",
                'cleaned_seats' => $cleanedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Expired seats cleanup error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error cleaning up expired seats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Belirli bir salon için expired koltukları temizle
     */
    public function cleanupExpiredSeatsForHall($hallId): JsonResponse
    {
        try {
            // Bu salondaki expired pending koltukları bul
            $expiredSeats = Seat::where('hall_id', $hallId)
                ->expiredPending()
                ->get();

            $cleanedCount = 0;

            foreach ($expiredSeats as $seat) {
                $seat->markAsAvailable();
                $cleanedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$cleanedCount} expired seat(s) cleaned up for hall {$hallId}",
                'cleaned_seats' => $cleanedCount,
                'hall_id' => $hallId
            ]);

        } catch (\Exception $e) {
            Log::error('Expired seats cleanup for hall error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error cleaning up expired seats for hall: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kullanıcı salon sayfasını açtığında otomatik cleanup
     * Frontend'den çağrılacak
     */
    public function autoCleanupOnPageLoad(): JsonResponse
    {
        try {
            // Model'daki cleanup metodunu kullan
            $cleanedCount = Seat::cleanupExpired();

            return response()->json([
                'success' => true,
                'cleaned_seats' => $cleanedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Auto cleanup error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Auto cleanup failed'
            ], 500);
        }
    }

    /**
     * Pending koltukların durumunu kontrol et
     */
    public function checkPendingSeats(): JsonResponse
    {
        try {
            $pendingSeats = Seat::where('status', Seat::STATUS_PENDING)->get();
            $expiredSeats = Seat::expiredPending()->get();

            return response()->json([
                'success' => true,
                'total_pending' => $pendingSeats->count(),
                'expired_pending' => $expiredSeats->count(),
                'seats' => $pendingSeats->map(function($seat) {
                    return [
                        'id' => $seat->id,
                        'row' => $seat->row,
                        'number' => $seat->number,
                        'hall_id' => $seat->hall_id,
                        'reserved_at' => $seat->reserved_at,
                        'reserved_until' => $seat->reserved_until,
                        'is_expired' => $seat->isReservationExpired(),
                        'remaining_minutes' => $seat->getRemainingMinutes(),
                        'status_label' => $seat->getStatusLabel()
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking pending seats: ' . $e->getMessage()
            ], 500);
        }
    }
}