<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BlacklistEntry
 *
 * @property int        $id
 * @property int        $user_id
 * @property User       $user
 * @property int        $target_user_id
 * @property User       $target_user
 *
 * @package App\Models
 */
class BlacklistEntry extends Model
{
    protected $fillable = [
        'user_id', 'target_user_id',
    ];

    public $timestamps = false;
}
