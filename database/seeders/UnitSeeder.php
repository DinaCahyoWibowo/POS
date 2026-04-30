<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the units table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('units')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('units')->insert([
            [
                'name' => 'Pieces',
                'code' => 'pcs',
                'description' => 'single item',
                'created_at' => '2025-12-05 06:29:15',
                'updated_at' => '2025-12-05 06:29:15'
            ],
            [
                'name' => 'Packages',
                'code' => 'pkgs',
                'description' => 'a packages of a unit',
                'created_at' => '2025-12-05 07:01:05',
                'updated_at' => '2025-12-06 03:50:15'
            ],
            [
                'name' => 'Carton',
                'code' => 'crtn',
                'description' => 'a carton',
                'created_at' => '2025-12-05 07:23:11',
                'updated_at' => '2025-12-05 07:23:11'
            ]
        ]);
    }
}