<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Hall extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'cinema_id',
        'capacity',
        'status'
    ];
    protected $casts = [
        'status' => 'string'
    ];

    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class)->withTrashed();
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }
}
