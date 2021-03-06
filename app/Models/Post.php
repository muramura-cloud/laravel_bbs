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

    public function getPosts()
    {
        return $this::with(['comments'])->withCount('likes')->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getPostsByKeyword($keyword, $page = null)
    {
        $posts = $this::with(['comments'])->withCount('likes')
            ->where('title', 'LIKE', "%{$keyword}%")
            ->orWhere('body', 'LIKE', "%{$keyword}%")->orderBy('created_at', 'desc');

        if (!empty($page)) {
            return $posts->paginate(10, ['*'], 'page', $page);
        } else {
            return $posts->paginate(10);
        }
    }

    public function getPostsByCategory($category)
    {
        return  Post::with(['comments'])->withCount('likes')->where('category', $category)->orderBy('created_at', 'desc')->paginate(10);
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

    public function getLikePosts($like_post_ids)
    {
        $like_posts = [];
        if (!empty($like_post_ids)) {
            $like_posts = Post::where(function ($post_query) use ($like_post_ids) {
                foreach ($like_post_ids as $id) {
                    $post_query->orWhere('id', $id);
                }
            })->with(['comments'])->withCount('likes')->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page', 1);
        }

        return $like_posts;
    }
}
