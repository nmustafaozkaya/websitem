<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Movie extends Model
{
    protected $fillable = [
        'title', 'description', 'duration', 'language', 'release_date',
        'poster_url','genre', 'status','imdb_raiting'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'release_date' => 'date',
        'duration' => 'integer',
        'status' => 'string'
    ];


    public function getReleaseDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');  // Burada istediğiniz formatı belirtiyoruz
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

}
