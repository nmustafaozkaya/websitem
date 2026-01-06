<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $fillable = [
        'name', 'code', 'icon', 'discount_rate', 
        'description', 'is_active', 'sort_order'
    ];
}
