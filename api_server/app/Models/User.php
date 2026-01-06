<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // eklenecek 
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birth_date',
        'gender',
        'cinema_id',
        'role_id',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function isAdmin()
    {
        return $this->role?->name === 'admin';
    }

    public function isCustomer()
    {
        return $this->role?->name === 'customer';
    }

    public function isSuperAdmin()
    {
        return $this->role?->name === 'super_admin' || $this->role?->name === 'admin';
    }

    // İzin kontrolü için
    public function hasPermission($permissionName)
    {
        return $this->role->permissions->contains('name', $permissionName);
    }

}
