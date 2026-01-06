<?php

// app/Models/Seat.php - Güncellenmiş
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Seat extends Model
{
    use HasFactory;

    // Status sabitleri
    const STATUS_AVAILABLE = 'Blank';
    const STATUS_OCCUPIED = 'Filled';
    const STATUS_PENDING = 'In Another Basket';

    protected $fillable = [
        'hall_id',
        'row',
        'number',
        'status',
        'reserved_at',
        'reserved_until'
    ];

    protected $casts = [
        'status' => 'string',
        'reserved_at' => 'datetime',
        'reserved_until' => 'datetime'
    ];


    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Helper methods
    public function isAvailable(): bool
    {
        // Eğer pending ise ve süresi dolmuşsa available say
        if ($this->status === self::STATUS_PENDING && $this->isReservationExpired()) {
            return true;
        }
        
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    public function isPending(): bool
    {
        // Pending ama süresi dolmamışsa pending
        return $this->status === self::STATUS_PENDING && !$this->isReservationExpired();
    }

    /**
     * Rezervasyon süresi dolmuş mu kontrol et
     */
    public function isReservationExpired(): bool
    {
        if (!$this->reserved_until) {
            // reserved_until yoksa reserved_at + 10 dakika kontrol et
            return $this->reserved_at && $this->reserved_at->addMinutes(10)->isPast();
        }
        
        return $this->reserved_until->isPast();
    }

    /**
     * Koltuk reservasyonu için kalan süre (dakika)
     */
    public function getRemainingMinutes(): int
    {
        if (!$this->isPending()) {
            return 0;
        }

        $until = $this->reserved_until ?: $this->reserved_at?->addMinutes(10);
        
        if (!$until) {
            return 0;
        }

        $remaining = now()->diffInMinutes($until, false);
        return max(0, $remaining);
    }

    public function markAsOccupied(): void
    {
        $this->update([
            'status' => self::STATUS_OCCUPIED,
            'reserved_at' => null,
            'reserved_until' => null
        ]);
    }

    public function markAsPending(): void
    {
        $reservedAt = now();
        $reservedUntil = $reservedAt->copy()->addMinutes(10);
        
        $this->update([
            'status' => self::STATUS_PENDING,
            'reserved_at' => $reservedAt,
            'reserved_until' => $reservedUntil
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update([
            'status' => self::STATUS_AVAILABLE,
            'reserved_at' => null,
            'reserved_until' => null
        ]);
    }

    // Status label'ları
    public function getStatusLabel(): string
    {
        if ($this->status === self::STATUS_PENDING && $this->isReservationExpired()) {
            return 'Blank'; // Süresi dolmuş pending'i Blank göster
        }

        return match($this->status) {
            self::STATUS_AVAILABLE => 'Blank',
            self::STATUS_OCCUPIED => 'Filled',
            self::STATUS_PENDING => 'In Another Basket (' . $this->getRemainingMinutes() . ' dk)',
            default => 'Unknown'
        };
    }

    // Status renkleri
    public function getStatusColor(): string
    {
        if ($this->status === self::STATUS_PENDING && $this->isReservationExpired()) {
            return '#10b981'; // Süresi dolmuş pending'i yeşil göster
        }

        return match($this->status) {
            self::STATUS_AVAILABLE => '#10b981',
            self::STATUS_OCCUPIED => '#cbcbcb',
            self::STATUS_PENDING => '#ff4061',
            default => '#gray'
        };
    }

    /**
     * Scope: Süresi dolmuş pending koltukları
     */
    public function scopeExpiredPending($query)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where(function($q) {
                $q->whereNotNull('reserved_until')
                  ->where('reserved_until', '<', now())
                  ->orWhere(function($subQ) {
                      $subQ->whereNull('reserved_until')
                           ->whereNotNull('reserved_at')
                           ->where('reserved_at', '<', now()->subMinutes(10));
                  });
            });
    }

    /**
     * Süresi dolmuş pending koltukları temizle
     */
    public static function cleanupExpired(): int
    {
        $expiredSeats = self::expiredPending()->get();
        
        $count = 0;
        foreach ($expiredSeats as $seat) {
            $seat->markAsAvailable();
            $count++;
        }
        
        return $count;
    }
}