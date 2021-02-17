<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Like;


class SetupSidebar
{
    public function handle(Request $request, Closure $next)
    {
        $request->merge([
            'categories' => Category::all(),
            'ranking_loved_posts' => Post::withCount('likes')->orderBy('likes_count', 'desc')->take(5)->get(),
            'like' => new Like,
        ]);

        return $next($request);
    }
}
