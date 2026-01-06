<?php
// app/Models/FutureMovie.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FutureMovie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description', 
        'duration',
        'language',
        'release_date',
        'genre',
        'poster_url',
        'imdb_raiting',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    // ✅ TARİH FORMATINI DÜZELTTİK
    protected $casts = [
        'release_date' => 'date', // Carbon olarak otomatik cast
    ];

    // Status constants
    const STATUS_UPCOMING = 'upcoming';
    const STATUS_PRE_PRODUCTION = 'pre_production';
    const STATUS_IN_PRODUCTION = 'in_production'; 
    const STATUS_POST_PRODUCTION = 'post_production';

    /**
     * Scope: Sadece gelecek filmler
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_UPCOMING);
    }

    /**
     * Scope: Yakında çıkacak filmler (30 gün içinde, bugünden SONRA)
     */
    public function scopeComingSoon($query)
    {
        $today = Carbon::now()->startOfDay();
        $thirtyDaysLater = Carbon::now()->addDays(30)->endOfDay();
        return $query->whereDate('release_date', '>', $today)
                     ->whereDate('release_date', '<=', $thirtyDaysLater);
    }

    /**
     * Scope: Pre-order için uygun filmler (1 hafta içinde)
     */
    public function scopePreOrder($query)
    {
        $oneWeekLater = Carbon::now()->addDays(7);
        $now = Carbon::now();
        return $query->where('release_date', '>', $now)
                    ->where('release_date', '<=', $oneWeekLater);
    }

    /**
     * Scope: Türe göre filtrele
     */
    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    /**
     * Scope: Arama
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('genre', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Status'u Türkçe olarak döndür
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_UPCOMING => 'Yakında',
            self::STATUS_PRE_PRODUCTION => 'Ön Prodüksiyon',
            self::STATUS_IN_PRODUCTION => 'Çekim Aşamasında', 
            self::STATUS_POST_PRODUCTION => 'Post Prodüksiyon'
        ];

        return $labels[$this->status] ?? 'Bilinmiyor';
    }

    /**
     * Kaç gün sonra çıkacağını hesapla
     */
    public function getDaysUntilReleaseAttribute()
    {
        if (!$this->release_date) return null;
        
        $now = Carbon::now();
        
        if ($this->release_date->isPast()) {
            return 0;
        }
        
        return $now->diffInDays($this->release_date);
    }

    /**
     * Release date'i d-m-Y formatında döndür (API için)
     */
    public function getFormattedReleaseDateAttribute()
    {
        return $this->release_date ? $this->release_date->format('d-m-Y') : null;
    }

    /**
     * Poster URL'i kontrol et
     */
    public function getPosterUrlAttribute($value)
    {
        if (!$value || empty(trim($value))) {
            return null;
        }
        
        return $value;
    }
}