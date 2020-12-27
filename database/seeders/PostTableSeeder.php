<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Comment;

class PostTableSeeder extends Seeder
{
    public function run()
    {
        Post::factory()->count(20)->create()->each(function ($post) {
            $comments = Comment::factory()->count(2)->make();
            $post->comments()->saveMany($comments);
        });
    }
}
