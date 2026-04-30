<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the roles table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('roles')->truncate(); } catch (Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('roles')->insert([
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full access',
                'created_at' => null,
                'updated_at' => null
            ],
            [
                'name' => 'Inventory',
                'slug' => 'inventory',
                'description' => 'user bagian inventory atau gudang',
                'created_at' => null,
                'updated_at' => '2025-12-12 10:44:34'
            ],
            [
                'name' => 'Sales',
                'slug' => 'sales',
                'description' => 'User bagian Sale atau kasir',
                'created_at' => '2025-12-12 10:46:23',
                'updated_at' => '2025-12-12 10:46:23'
            ]
        ]);
    }
}