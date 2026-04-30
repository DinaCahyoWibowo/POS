<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        // Priority: query param -> cookie -> session -> default
        $queryMode = request()->query('app_mode');
        $cookieMode = request()->cookie('app_mode');
        if ($queryMode) {
            $mode = $queryMode;
        } else {
            if ($cookieMode) {
                $mode = $cookieMode;
            } else {
                $mode = session('app_mode', 'live');
            }
        }

        if ($mode === 'demo') {
            Config::set('database.default', 'demo');
            DB::purge('mysql');
            DB::purge('demo');
            DB::reconnect('demo');
        } else {
            Config::set('database.default', 'mysql');
            DB::purge('mysql');
            DB::reconnect('mysql');
        }

        return $next($request);
    }
}