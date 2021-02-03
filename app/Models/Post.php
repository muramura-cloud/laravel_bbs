<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'img',
        'category',
    ];

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }

    public function reads()
    {
        return $this->hasMany('App\Models\Read');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getPostsByKeyword($keyword)
    {
        $posts = $this::with(['comments'])->withCount('likes')->where('title', 'LIKE', "%{$keyword}%")
            ->orWhere('body', 'LIKE', "%{$keyword}%")->orderBy('created_at', 'desc')->paginate(10);

        return $posts;
    }

    public function getPostsByCategoryAndKeyword($category, $keyword)
    {
        $posts = $this::with(['comments'])->withCount('likes')->where('category', $category)
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")->orWhere('body', 'LIKE', "%{$keyword}%");
            })->orderBy('created_at', 'desc')->paginate(10);

        return $posts;
    }

    public function getPostsByUser($users)
    {
        $posts = $this::with(['comments'])->withCount('likes')->where(function ($query) use ($users) {
            foreach ($users as $user) {
                $query->orWhere('user_id', $user->id);
            }
        })->orderBy('created_at', 'desc')->paginate(10);

        return $posts;
    }

    public function getPostsByCategoryAndUser($category, $users)
    {
        $posts = $this::with(['comments'])->withCount('likes')->where('category', $category)
            ->where(function ($query) use ($users) {
                foreach ($users as $user) {
                    $query->orWhere('user_id', $user->id);
                }
            })->orderBy('created_at', 'desc')->paginate(10);

        return $posts;
    }
}
