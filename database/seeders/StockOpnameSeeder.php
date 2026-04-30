<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockOpnameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the stock_opnames table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('stock_opnames')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('stock_opnames')->insert([
            [
                'id' => 1,
                'code' => 'OPN20260129N5MA',
                'opname_date' => '2026-01-29',
                'reason' => null,
                'created_at' => '2026-01-29 14:23:29',
                'updated_at' => '2026-01-29 14:23:29'
            ],
            [
                'id' => 2,
                'code' => 'OPN202601290002',
                'opname_date' => '2026-01-29',
                'reason' => null,
                'created_at' => '2026-01-29 14:30:49',
                'updated_at' => '2026-01-29 14:30:49'
            ]
        ]);
    }
}