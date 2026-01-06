<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Showtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'hall_id',
        'price',
        'start_time',
        'end_time',
        'date',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date' => 'date',
        'status' => 'string'
    ];

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'showtimes_id');
    }
}
