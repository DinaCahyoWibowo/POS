<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockOpnameLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the stock_opname_lines table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('stock_opname_lines')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('stock_opname_lines')->insert([
            [
                'id' => 1,
                'stock_opname_id' => 1,
                'item_id' => 5,
                'system_qty' => 47,
                'physical_qty' => 47,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:23:29',
                'updated_at' => '2026-01-29 14:23:29'
            ],
            [
                'id' => 2,
                'stock_opname_id' => 1,
                'item_id' => 4,
                'system_qty' => 214,
                'physical_qty' => 214,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:23:29',
                'updated_at' => '2026-01-29 14:23:29'
            ],
            [
                'id' => 3,
                'stock_opname_id' => 1,
                'item_id' => 2,
                'system_qty' => 70,
                'physical_qty' => 70,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:23:29',
                'updated_at' => '2026-01-29 14:23:29'
            ],
            [
                'id' => 4,
                'stock_opname_id' => 1,
                'item_id' => 1,
                'system_qty' => 159,
                'physical_qty' => 159,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:23:29',
                'updated_at' => '2026-01-29 14:23:29'
            ],
            [
                'id' => 5,
                'stock_opname_id' => 1,
                'item_id' => 6,
                'system_qty' => 48,
                'physical_qty' => 47,
                'difference' => -1,
                'reason' => 'rusak',
                'created_at' => '2026-01-29 14:23:29',
                'updated_at' => '2026-01-29 14:23:29'
            ],
            [
                'id' => 6,
                'stock_opname_id' => 1,
                'item_id' => 3,
                'system_qty' => 159,
                'physical_qty' => 159,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:23:29',
                'updated_at' => '2026-01-29 14:23:29'
            ],
            [
                'id' => 7,
                'stock_opname_id' => 2,
                'item_id' => 5,
                'system_qty' => 47,
                'physical_qty' => 47,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:30:49',
                'updated_at' => '2026-01-29 14:30:49'
            ],
            [
                'id' => 8,
                'stock_opname_id' => 2,
                'item_id' => 4,
                'system_qty' => 214,
                'physical_qty' => 214,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:30:49',
                'updated_at' => '2026-01-29 14:30:49'
            ],
            [
                'id' => 9,
                'stock_opname_id' => 2,
                'item_id' => 2,
                'system_qty' => 70,
                'physical_qty' => 70,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:30:49',
                'updated_at' => '2026-01-29 14:30:49'
            ],
            [
                'id' => 10,
                'stock_opname_id' => 2,
                'item_id' => 1,
                'system_qty' => 159,
                'physical_qty' => 159,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:30:49',
                'updated_at' => '2026-01-29 14:30:49'
            ],
            [
                'id' => 11,
                'stock_opname_id' => 2,
                'item_id' => 6,
                'system_qty' => 47,
                'physical_qty' => 47,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:30:49',
                'updated_at' => '2026-01-29 14:30:49'
            ],
            [
                'id' => 12,
                'stock_opname_id' => 2,
                'item_id' => 3,
                'system_qty' => 159,
                'physical_qty' => 159,
                'difference' => 0,
                'reason' => null,
                'created_at' => '2026-01-29 14:30:49',
                'updated_at' => '2026-01-29 14:30:49'
            ]
        ]);
    }
}