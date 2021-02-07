<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class CheckAdminUser
{
    public function handle(Request $request, Closure $next)
    {
        $admin = new Admin;

        if (!Auth::check() || Auth::check() && !$admin->isAdminUser(Auth::user()->email)) {
            return redirect('/');
        }

        return $next($request);
    }
}
