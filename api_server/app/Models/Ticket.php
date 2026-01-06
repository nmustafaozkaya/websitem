<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'showtime_id',      
        'seat_id',          
        'user_id',
        'sale_id',          
        'price',
        'customer_type',        
        'discount_rate',    
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_rate' => 'decimal:2', 
        'status' => 'string'
    ];

    const CUSTOMER_TYPE_ADULT = 'adult';
    const CUSTOMER_TYPE_STUDENT = 'student';
    const CUSTOMER_TYPE_SENIOR = 'senior';
    const CUSTOMER_TYPE_CHILD = 'child';

    public function getCustomerTypeLabel()
    {
        $labels = [
            self::CUSTOMER_TYPE_ADULT => 'Adult',
            self::CUSTOMER_TYPE_STUDENT => 'Student',
            self::CUSTOMER_TYPE_SENIOR => 'Retired',
            self::CUSTOMER_TYPE_CHILD => 'Child'
        ];

        return $labels[$this->customer_type] ?? 'Bilinmiyor';
    }

    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class, 'showtime_id'); 
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class, 'seat_id'); 
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    // Bilet için QR kod verisini oluştur
    public function getQrCodeData(): string
    {
        return json_encode([
            'ticket_id' => $this->id,
            'movie' => $this->showtime->movie->title,
            'cinema' => $this->showtime->hall->cinema->name,
            'hall' => $this->showtime->hall->name,
            'seat' => $this->seat->row . $this->seat->number,
            'showtime' => $this->showtime->start_time->format('d.m.Y H:i'),
            'customer_type' => $this->getCustomerTypeLabel(),
            'price' => $this->price
        ]);
    }

    // Bilet durumuna göre badge class'ı döndür
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'deactive' => 'bg-red-100 text-red-800',
            'sold' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Bilet durumunun Türkçe karşılığı
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'deactive' => 'Deactive',
            'sold' => 'Satıldı',
            'cancelled' => 'İptal Edildi',
            'refunded' => 'İade Edildi',
            default => 'Bilinmiyor'
        };
    }
}