<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class Authcheck
{
    public function handle(Request $request, Closure $next)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
