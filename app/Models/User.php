<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';
    
    protected $fillable = [
        'username', 'password', 'firstname', 'lastname', 'email', 'phone', 'profile', 'status', 'remember_token', 'logged_in'
    ];

}
