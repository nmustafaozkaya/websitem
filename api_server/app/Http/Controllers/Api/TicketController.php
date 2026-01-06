<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerType;
use App\Models\Ticket;
use App\Models\Showtime;
use App\Models\Seat;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'showtime_id' => 'required|exists:showtimes,id',
                // Maksimum 6 bilet sınırı kaldırıldı; sadece en az 1 bilet şartı kaldı
                'tickets' => 'required|array|min:1',
                'tickets.*.seat_id' => 'required|exists:seats,id',
                'tickets.*.customer_type' => 'required|in:adult,student,senior,child',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_phone' => 'required|string|max:20',
                'payment_method' => 'required|in:cash,card,online',
                'tax_calculation' => 'nullable|array',
            ]);

            return DB::transaction(function () use ($validated) {
                $showtime = Showtime::findOrFail($validated['showtime_id']);
                $seatIds = collect($validated['tickets'])->pluck('seat_id')->toArray();

                // Koltukların durumunu kontrol et
                $seats = Seat::whereIn('id', $seatIds)->get();

                foreach ($seats as $seat) {
                    // Koltuk ne müsait ne de pending ise hata ver
                    if (!in_array($seat->status, [Seat::STATUS_AVAILABLE, Seat::STATUS_PENDING])) {
                        return response()->json([
                            'success' => false,
                            'message' => "Koltuk {$seat->row}{$seat->number} müsait değil (Durum: {$seat->status})"
                        ], 400);
                    }
                }

                // Koltukların bu salona ait olup olmadığını kontrol et
                $validSeats = Seat::where('hall_id', $showtime->hall_id)
                    ->whereIn('id', $seatIds)
                    ->count();

                if ($validSeats !== count($seatIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Seçilen koltukların bazıları bu salona ait değil'
                    ], 400);
                }

                $tickets = [];
                $totalAmount = 0;

                // Her bilet için discount oranlarını belirle
                $discountRates = [
                    'adult' => 0,
                    'student' => 20, // %20 indirim
                    'senior' => 15,  // %15 indirim
                    'child' => 25    // %25 indirim
                ];

                // Sale kaydı oluştur
                $ticketBreakdown = [];
                foreach ($validated['tickets'] as $ticketData) {
                    $type = $ticketData['customer_type'];
                    $ticketBreakdown[$type] = ($ticketBreakdown[$type] ?? 0) + 1;
                }

                $sale = Sale::create([
                    'user_id' => Auth::id(),
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'completed',
                    'total_amount' => 0, // Şimdilik 0, sonra güncellenecek
                    'ticket_count' => count($validated['tickets']),
                    'ticket_breakdown' => $ticketBreakdown
                ]);

                // 🔧 1. ÖNCE TÜM KOLTUKLAR OCCUPIED YAPILIYOR
                foreach ($seatIds as $seatId) {
                    $seat = Seat::findOrFail($seatId);

                    // Koltuk durumunu occupied yap
                    $seat->update([
                        'status' => Seat::STATUS_OCCUPIED,
                        'reserved_at' => null,
                        'reserved_until' => null
                    ]);

                    Log::info("Seat {$seat->row}{$seat->number} (ID: {$seat->id}) marked as occupied");
                }

                // 2. SONRA TİCKET'LAR OLUŞTURULUYOR
                foreach ($validated['tickets'] as $ticketData) {
                    $discountRate = $discountRates[$ticketData['customer_type']];
                    $originalPrice = $showtime->price;
                    $discountAmount = ($originalPrice * $discountRate) / 100;
                    $finalPriceBeforeTax = $originalPrice - $discountAmount;

                    // Tax calculation'dan gelen tax'ları kullan
                    $ticketFinalPrice = $finalPriceBeforeTax; // Varsayılan (tax yok)

                    $taxCalculation = $validated['tax_calculation'] ?? null;
                    if ($taxCalculation && isset($taxCalculation['total']) && isset($taxCalculation['subtotal'])) {
                        // Proportional tax calculation (her bilet eşit pay alır)
                        $taxRatio = ($taxCalculation['total'] - $taxCalculation['subtotal']) / $taxCalculation['subtotal'];
                        $ticketTaxAmount = $finalPriceBeforeTax * $taxRatio;
                        $ticketFinalPrice = $finalPriceBeforeTax + $ticketTaxAmount;
                    }

                    $ticket = Ticket::create([
                        'showtime_id' => $validated['showtime_id'],
                        'seat_id' => $ticketData['seat_id'],
                        'user_id' => Auth::id(),
                        'sale_id' => $sale->id,
                        'price' => $ticketFinalPrice,
                        'customer_type' => $ticketData['customer_type'],
                        'discount_rate' => $discountRate,
                        'status' => 'sold'
                    ]);

                    $ticket->load(['seat', 'showtime.movie', 'showtime.hall.cinema']);
                    $tickets[] = $ticket;
                    $totalAmount += $ticketFinalPrice;
                }

                // Tax calculation varsa kullan, yoksa bilet toplamı
                $taxCalculation = $validated['tax_calculation'] ?? null;

                if ($taxCalculation && isset($taxCalculation['total'])) {
                    $finalAmount = $taxCalculation['total'];
                } else {
                    $finalAmount = $totalAmount;
                }

                $sale->update(['total_amount' => $finalAmount]);

                Log::info('Sale completed successfully:', [
                    'sale_id' => $sale->id,
                    'tickets_total' => $totalAmount,
                    'final_amount' => $finalAmount,
                    'seats_updated' => $seatIds,
                    'seats_status' => 'occupied'
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'tickets' => $tickets,
                        'sale' => $sale,
                        'total_amount' => $totalAmount,
                        'ticket_count' => count($tickets)
                    ],
                    'message' => count($tickets) . ' adet bilet başarıyla satıldı'
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error('Ticket purchase failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bilet satışı sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    // Diğer metodlar aynı kalır...
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Ticket::with(['showtime.movie', 'showtime.hall.cinema', 'seat', 'user']);

            if ($request->has('showtime_id')) {
                $query->where('showtime_id', $request->showtime_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('date')) {
                $query->whereHas('showtime', function ($q) use ($request) {
                    $q->where('date', $request->date);
                });
            }

            $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

            // Her bilet için tarih kontrolü yapıp durumu güncelle
            $tickets->getCollection()->transform(function ($ticket) {
                $now = now();
                $showtimeDateTime = \Carbon\Carbon::parse($ticket->showtime->date)
                    ->setTimeFrom($ticket->showtime->start_time);
                
                // Eğer gösterim zamanı geçmişse 'deactive', değilse 'active'
                if ($showtimeDateTime->isPast()) {
                    $ticket->status = 'deactive';
                } else {
                    $ticket->status = 'active';
                }
                
                return $ticket;
            });

            return response()->json([
                'success' => true,
                'data' => $tickets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Biletler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $ticket = Ticket::with([
                'showtime.movie',
                'showtime.hall.cinema',
                'seat',
                'user',
                'sale'
            ])->findOrFail($id);

            // Bilet durumunu tarih kontrolü ile güncelle
            $now = now();
            $showtimeDateTime = \Carbon\Carbon::parse($ticket->showtime->date)
                ->setTimeFrom($ticket->showtime->start_time);
            
            if ($showtimeDateTime->isPast()) {
                $ticket->status = 'deactive';
            } else {
                $ticket->status = 'active';
            }

            return response()->json([
                'success' => true,
                'data' => $ticket
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bilet bulunamadı'
            ], 404);
        }
    }

    public function myTickets(Request $request): JsonResponse
    {
        try {
            $tickets = Ticket::with([
                'showtime.movie',
                'showtime.hall.cinema',
                'seat',
                'sale',
            ])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Her bilet için tarih kontrolü yapıp durumu güncelle
            $tickets->getCollection()->transform(function ($ticket) {
                $now = now();
                $showtimeDateTime = \Carbon\Carbon::parse($ticket->showtime->date)
                    ->setTimeFrom($ticket->showtime->start_time);
                
                // Eğer gösterim zamanı geçmişse 'deactive', değilse 'active'
                if ($showtimeDateTime->isPast()) {
                    $ticket->status = 'deactive';
                } else {
                    $ticket->status = 'active';
                }
                
                return $ticket;
            });

            return response()->json([
                'success' => true,
                'data' => $tickets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Biletleriniz yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTicketPrices($showtimeId): JsonResponse
    {
        try {
            $showtime = Showtime::findOrFail($showtimeId);
            $basePrice = $showtime->price;

            $customerTypes = CustomerType::where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $prices = [];
            $discountRates = [];

            foreach ($customerTypes as $type) {
                $discount = ($basePrice * $type->discount_rate) / 100;
                $prices[$type->code] = $basePrice - $discount;
                $discountRates[$type->code] = $type->discount_rate;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'base_price' => $basePrice,
                    'types' => $customerTypes,
                    'prices' => $prices,
                    'discount_rates' => $discountRates
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fiyat bilgileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
}