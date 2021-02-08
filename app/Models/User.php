<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
//    const CREATED_AT = 'registration_date';

    protected $fillable = [
        'email', 'login', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];
}
