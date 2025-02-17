<?php

namespace Laravel\Sanctum\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFrontendRequestsAreStateful
{
    public function handle(Request $request, Closure $next)
    {
        if (config('sanctum.stateful') === null) {
            config(['sanctum.stateful' => ['localhost']]);
        }

        return $next($request);
    }
}
