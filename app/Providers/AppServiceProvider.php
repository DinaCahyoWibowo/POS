<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            $mode = session('app_mode', 'live');

    if ($mode === 'demo') {
        Config::set('database.default', 'demo');
        DB::purge('demo');   // 🔥 force reconnect
    } else {
        Config::set('database.default', 'mysql');
        DB::purge('mysql'); // 🔥 force reconnect
    }

    }
}
