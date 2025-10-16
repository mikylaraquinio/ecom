<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();

            if ($user->is_admin || $user->role === 'admin') {
                return redirect()->intended('/admin');
            }

            return redirect('/home');
        }

        return $next($request);
    }

}
