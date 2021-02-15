<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Comment
 *
 * @property int        $id
 * @property string     $text
 * @property int        $post_id
 * @property Post       $post
 * @property int        $user_id
 * @property User       $user
 *
 * @package App\Models
 */
class Comment extends Model
{
//    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'text',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
