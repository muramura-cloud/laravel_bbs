<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'body',
    ];

    public function post()
    {
        // 関連づけられている主テーブルの理コードを取り出す
        return $this->belongsTo('App\Models\Post');
    }

    // 紐づけられている主テーブルを取得
    public function getPost()
    {
        return $this->post;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
