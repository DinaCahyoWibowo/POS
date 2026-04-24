<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        // primary check: role === 'admin'
        if ($user->role === 'admin') {
            return $next($request);
        }

        // nothing matched — forbidden
        abort(403);
    }
}
