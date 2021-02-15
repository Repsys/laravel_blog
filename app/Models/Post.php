<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 *
 * @property int        $id
 * @property string     $title
 * @property string     $text
 * @property int        $user_id
 * @property User       $user
 * @property Comment[]  $comments
 *
 * @package App\Models
 */
class Post extends Model
{
//    const CREATED_AT = 'creation_date';

    protected $fillable = [
        'title', 'text',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
