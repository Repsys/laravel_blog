<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const CREATED_AT = 'registration_date';

    protected $fillable = [
        'email', 'login', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];
}
