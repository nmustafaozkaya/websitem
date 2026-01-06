<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cinema extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'city_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function halls()
    {
        return $this->hasMany(Hall::class);
    }
    public function movies()
    {
        return $this->belongsToMany(Movie::class);

    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    

}
