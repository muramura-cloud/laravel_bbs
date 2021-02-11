<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;


class SetupSidebar
{
    public function handle(Request $request, Closure $next)
    {
        // $request->merge([
        //     'user' => $user,
        //     'user' => $user,
        // ]);

        return $next($request);
    }
}
