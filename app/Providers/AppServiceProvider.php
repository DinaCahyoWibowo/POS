<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


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
        // Share an explicitly-loaded current user on every view so layout uses correct DB connection.
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $mode = request()->query('app_mode') ?: request()->cookie('app_mode') ?: session('app_mode', 'live');
            $conn = $mode === 'demo' ? 'demo' : 'mysql';
            $current = null;
            try {
                if (auth()->check()) {
                    $current = \App\Models\User::on($conn)->find(auth()->id());
                }
            } catch (\Exception $e) {
                $current = null;
            }
            $view->with('currentUser', $current);
        });

    }
}