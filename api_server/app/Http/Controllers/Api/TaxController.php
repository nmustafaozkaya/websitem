<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaxController extends Controller
{
    /**
     * Get only the service fee (Hizmet Bedeli)
     */
    public function index(): JsonResponse
    {
        try {
            $serviceFee = $this->resolveServiceFee();

            return response()->json([
                'success' => true,
                'data' => [$serviceFee]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hizmet bedeli yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate total with taxes
     */
    public function calculateTotal(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'subtotal' => 'required|numeric|min:0',
                'ticket_count' => 'required|integer|min:1'
            ]);

            $subtotal = $request->subtotal;
            $ticketCount = $request->ticket_count;

            $serviceFee = $this->resolveServiceFee();
            $feeAmount = $this->calculateServiceFeeAmount($serviceFee, $ticketCount, $subtotal);

            return response()->json([
                'success' => true,
                'data' => [
                    'subtotal' => round($subtotal, 2),
                    'taxes' => [[
                        'name' => $serviceFee['name'],
                        'type' => $serviceFee['type'],
                        'rate' => $serviceFee['rate'],
                        'amount' => round($feeAmount, 2),
                        'description' => $serviceFee['description'] ?? 'Hizmet bedeli'
                    ]],
                    'total_tax_amount' => round($feeAmount, 2),
                    'total' => round($subtotal + $feeAmount, 2),
                    'ticket_count' => $ticketCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hizmet bedeli hesaplanırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function resolveServiceFee(): array
    {
        $serviceFee = Tax::where('status', 'active')
            ->whereRaw('LOWER(name) = ?', ['hizmet bedeli'])
            ->orderBy('priority')
            ->first();

        if ($serviceFee) {
            return $serviceFee->toArray();
        }

        return [
            'id' => null,
            'name' => 'Hizmet Bedeli',
            'type' => 'fixed',
            'rate' => 2.00,
            'status' => 'active',
            'priority' => 1,
            'description' => 'Bilet başına hizmet bedeli',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];
    }

    private function calculateServiceFeeAmount(array $serviceFee, int $ticketCount, float $subtotal): float
    {
        $rate = (float) ($serviceFee['rate'] ?? 0);
        $type = $serviceFee['type'] ?? 'fixed';

        return match ($type) {
            'percentage' => ($subtotal * $rate) / 100,
            'fixed_total' => $rate,
            default => $rate * $ticketCount, // fixed per ticket
        };
    }
}

// Tax Model - app/Models/Tax.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'type', // 'percentage', 'fixed', 'fixed_total'
        'rate',
        'status',
        'priority',
        'description'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'priority' => 'integer'
    ];
}