<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    /* 
        percentage : 
        for admin to determine bonus referral 

        is_admin :
        0 == normal user
        1 == admin user

        status : 
        0 == banned
        1 == active
        2 == membership (cancelled)
        3 == membership that eligible for using own whatsapp, but the membership is end
    */
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'currency',
        'lang',
        'membership',
        'end_membership',
        'myreferral',
        'referral_code',
        'email_wamate',
        'wamate_id',
        'token',
        'refresh_token',
        'counter',
        'percentage',
        'is_valid_email'
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
    ];
}
