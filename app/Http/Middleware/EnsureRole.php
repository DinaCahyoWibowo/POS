<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('role:admin,sales')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        // no roles specified -> allow any authenticated user
        if (empty($roles)) {
            return $next($request);
        }

        // Flatten roles array: middleware parameters may be passed as separate args
        $flattened = [];
        foreach ($roles as $r) {
            if ($r === null || $r === '') continue;
            foreach (explode(',', (string)$r) as $part) {
                $part = trim($part);
                if ($part !== '') $flattened[] = strtolower($part);
            }
        }

        // Normalize user's role: allow numeric role id or slug stored
        $userRole = $user->role;
        if (is_numeric($userRole)) {
            $roleModel = \App\Models\Role::find((int)$userRole);
            $userRole = $roleModel ? $roleModel->slug : (string)$userRole;
        }
        $userRole = is_null($userRole) ? '' : strtolower(trim((string)$userRole));

        $allowed = $flattened;

        if (in_array($userRole, $allowed, true)) {
            \Log::info('EnsureRole: allowed', ['user_id' => $user->id ?? null, 'user_role' => $userRole, 'allowed' => $allowed]);
            return $next($request);
        }

        \Log::warning('EnsureRole: forbidden', ['user_id' => $user->id ?? null, 'user_role' => $userRole, 'allowed' => $allowed, 'request_path' => $request->path()]);
        abort(403);
    }
}
