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
        return $this->belongsTo('App\Models\Post');
    }

    public function getPost()
    {
        return $this->post;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getCommentsByKeyword($keywords, $page)
    {
        $comments = $this::where(function ($query) use ($keywords) {
            foreach ($keywords as $col_name => $value) {
                $query->where($col_name, 'LIKE', "%{$value}%");
            }
        })->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page', (int) $page);

        return $comments;
    }
}
