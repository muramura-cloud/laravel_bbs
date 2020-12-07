<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // これによってPostFactoryが使えるようになる。
    use HasFactory;

    // fillableはモデルを利用してデータを作成する時に、複数代入を許可するための設定
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'img',
    ];

    public function comments()
    {
        // これによってPostクラス内でcommentsというプロパティでcommentインスタンスを取り出すことができる。
        return $this->hasMany('App\Models\Comment');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
