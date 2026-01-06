<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_reference',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method',
        'payment_status',
        'total_amount',
        'ticket_count',
        'ticket_breakdown'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'ticket_breakdown' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (!$sale->sale_reference) {
                $sale->sale_reference = 'SAL-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Bilet tiplerinin özetini al
    public function getTicketTypeSummary(): string
    {
        if (!$this->ticket_breakdown) {
            return $this->ticket_count . ' bilet';
        }

        $summary = [];
        $typeNames = [
            'adult' => 'Adult',
            'student' => 'Student',
            'senior' => 'Retired',
            'child' => 'Child'
        ];

        foreach ($this->ticket_breakdown as $type => $count) {
            if ($count > 0) {
                $summary[] = $count . ' ' . ($typeNames[$type] ?? $type);
            }
        }

        return implode(', ', $summary);
    }

    // Payment status'e göre badge class'ı döndür
    public function getPaymentStatusBadgeClass(): string
    {
        return match($this->payment_status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Payment status'ün Türkçe karşılığı
    public function getPaymentStatusLabel(): string
    {
        return match($this->payment_status) {
            'completed' => 'Tamamlandı',
            'pending' => 'Bekliyor',
            'failed' => 'Başarısız',
            'refunded' => 'İade Edildi',
            default => 'Bilinmiyor'
        };
    }

    // Payment method'un Türkçe karşılığı
    public function getPaymentMethodLabel(): string
    {
        return match($this->payment_method) {
            'cash' => 'Nakit',
            'card' => 'Kredi Kartı',
            'online' => 'Online Ödeme',
            default => 'Bilinmiyor'
        };
    }
}