<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }

    public function like_exist($user_id, $post_id)
    {
        $exist = Like::where('user_id', '=',  $user_id)->where('post_id', '=', $post_id)->first();

        if ($exist) {
            return true;
        } else {
            return false;
        }
    }
}
