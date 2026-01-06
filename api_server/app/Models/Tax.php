<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',          // 'percentage', 'fixed', 'fixed_total'
        'rate',          // Vergi oranı veya tutar
        'status',        // 'active', 'inactive'
        'priority',      // Hesaplama sırası
        'description'    // Açıklama
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'priority' => 'integer'
    ];

    // Vergi türleri
    const TYPE_PERCENTAGE = 'percentage';  // Yüzde bazlı (KDV %20)
    const TYPE_FIXED = 'fixed';           // Bilet başına sabit (₺2/bilet)
    const TYPE_FIXED_TOTAL = 'fixed_total'; // Toplam sabit tutar (₺5)

    // Durum değerleri
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Aktif vergileri getir
     */
    public static function getActive()
    {
        return self::where('status', self::STATUS_ACTIVE)
                   ->orderBy('priority')
                   ->get();
    }

    /**
     * Vergi tutarını hesapla
     */
    public function calculateAmount(float $subtotal, int $ticketCount = 1): float
    {
        switch ($this->type) {
            case self::TYPE_PERCENTAGE:
                return ($subtotal * $this->rate) / 100;
                
            case self::TYPE_FIXED:
                return $this->rate * $ticketCount;
                
            case self::TYPE_FIXED_TOTAL:
                return $this->rate;
                
            default:
                return 0;
        }
    }

    /**
     * Vergi adını formatla
     */
    public function getFormattedNameAttribute(): string
    {
        switch ($this->type) {
            case self::TYPE_PERCENTAGE:
                return "{$this->name} (%{$this->rate})";
                
            case self::TYPE_FIXED:
                return "{$this->name} (₺{$this->rate}/bilet)";
                
            case self::TYPE_FIXED_TOTAL:
                return "{$this->name} (₺{$this->rate})";
                
            default:
                return $this->name;
        }
    }

    /**
     * Scope: Aktif vergileri filtrele
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Öncelik sırasına göre sırala
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority');
    }
}