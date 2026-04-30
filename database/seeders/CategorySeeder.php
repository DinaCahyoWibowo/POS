<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the categories table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('categories')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('categories')->insert([
            [
                'name' => 'Instant Noodle',
                'code' => 'IN',
                'description' => 'instant noodle',
                'created_at' => '2025-12-05 06:29:56',
                'updated_at' => '2025-12-05 06:29:56'
            ],
            [
                'name' => 'Air Mineral',
                'code' => 'AM',
                'description' => 'air mineral',
                'created_at' => '2025-12-06 03:50:50',
                'updated_at' => '2025-12-06 03:50:50'
            ]
        ]);
    }
}