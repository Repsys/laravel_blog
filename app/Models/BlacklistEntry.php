<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistEntry extends Model
{
    protected $fillable = [
        'user_id', 'target_user_id',
    ];

    public $timestamps = false;
}
