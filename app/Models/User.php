<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\SystemSetup\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pageination',
        'name',
        'department_code',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'user_code',
        'user_type',
        'role',
        'permission',
        'phone',
        'session_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'session_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
     protected $casts = [
        'email_verified_at' => 'datetime',
        'phone' => 'decimal:0',
        'pageination' => 'decimal:0',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }
}
