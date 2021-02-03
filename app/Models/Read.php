<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Read extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'comment_id',
        'is_read',
    ];

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }

    public function getUnreadPostIds($post_ids)
    {
        $unread_post_ids = [];
        if (!empty($post_ids)) {
            $post_ids = array_column($post_ids, 'id');
            $unread_post_ids = $this::where(function ($read_query) use ($post_ids) {
                foreach ($post_ids as $id) {
                    $read_query->orWhere('post_id', $id)->where('is_read', false);
                }
            })->get('post_id')->toArray();
        }

        return $unread_post_ids;
    }
}
