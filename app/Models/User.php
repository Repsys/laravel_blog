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
        'password', 'remember_token', 'api_token', 'pivot'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(User::class, 'subscriptions',
            'user_id', 'target_user_id');
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'subscriptions',
            'target_user_id', 'user_id');
    }

    public function blacklist()
    {
        return $this->belongsToMany(User::class, 'blacklist_entries',
            'user_id', 'target_user_id');
    }
}
