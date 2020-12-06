<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'body'
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
}
