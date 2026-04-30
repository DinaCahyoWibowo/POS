<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the brands table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('brands')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('brands')->insert([
            [
                'name' => 'Indomie',
                'code' => 'IDM',
                'description' => 'Indomie Instant Noodle',
                'created_at' => '2025-12-05 06:31:22',
                'updated_at' => '2025-12-05 06:31:22'
            ],
            [
                'name' => 'Mie Sedaap',
                'code' => 'MSDP',
                'description' => 'Mie Sedaap instan Noodle',
                'created_at' => '2025-12-05 07:43:59',
                'updated_at' => '2025-12-05 07:43:59'
            ],
            [
                'name' => 'Aqua',
                'code' => 'AQ',
                'description' => 'brand air mineral',
                'created_at' => '2025-12-06 03:51:33',
                'updated_at' => '2025-12-06 03:54:34'
            ]
        ]);
    }
}