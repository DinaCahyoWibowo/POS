<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetDemoDatabase extends Command
{
    protected $signature = 'demo:reset';
    protected $description = 'Reset demo database';

    public function handle()
    {
    Log::info('demo:reset START at ' . now());
    $this->info('Resetting demo database...');

    // Ensure application uses the demo connection for the migrate+seed steps
    Config::set('database.default', 'demo');
    DB::purge('demo');
    DB::reconnect('demo');

    // Run migration fresh + seed on demo DB
    try {
        Artisan::call('migrate:fresh', [
            '--database' => 'demo',
            '--seed' => true,
            '--force' => true,
        ]);
    } catch (\Exception $e) {
        $this->error('Demo reset failed: '.$e->getMessage());
        $this->error(Artisan::output());
        return 1;
    }

    $this->info('Migrations & seeders executed successfully.');
    Log::info('demo:reset SUCCESS at ' . now());
    $this->info('Demo database reset successfully!');
    }
}
