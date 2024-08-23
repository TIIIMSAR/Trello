<?php

namespace Modules\Todo\Entities;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    function likes()
    {
        return $this->belongsToMany(Workspace::class, 'like');
    }


    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }

   
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
