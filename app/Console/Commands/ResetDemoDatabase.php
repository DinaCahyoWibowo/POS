<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetDemoDatabase extends Command
{
    protected $signature = 'demo:reset';
    protected $description = 'Reset demo database';

    public function handle()
    {
    $this->info('Resetting demo database...');

    // Run migration fresh + seed on demo DB
    Artisan::call('migrate:fresh', [
        '--database' => 'demo',
        '--seed' => true,
        '--force' => true,
    ]);

    $this->info(Artisan::output());

    $this->info('Demo database reset successfully!');
    }
}
