<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ItemUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the item_units table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('item_units')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('item_units')->insert([
            [
                'item_id' => 1,
                'unit_id' => 1,
                'factor' => 1,
                'price' => 3500,
                'is_base' => 1,
                'created_at' => '2025-12-05 07:27:42',
                'updated_at' => '2026-01-29 10:49:30'
            ],
            [
                'item_id' => 2,
                'unit_id' => 1,
                'factor' => 1,
                'price' => 3500,
                'is_base' => 1,
                'created_at' => '2025-12-05 07:28:52',
                'updated_at' => '2025-12-06 06:32:35'
            ],
            [
                'item_id' => 2,
                'unit_id' => 2,
                'factor' => 5,
                'price' => 16500,
                'is_base' => 0,
                'created_at' => '2025-12-05 07:28:52',
                'updated_at' => '2025-12-06 06:32:35'
            ],
            [
                'item_id' => 2,
                'unit_id' => 3,
                'factor' => 40,
                'price' => 125000,
                'is_base' => 0,
                'created_at' => '2025-12-05 07:28:52',
                'updated_at' => '2025-12-06 06:32:35'
            ],
            [
                'item_id' => 1,
                'unit_id' => 3,
                'factor' => 40,
                'price' => 125000,
                'is_base' => 0,
                'created_at' => '2025-12-05 07:35:07',
                'updated_at' => '2026-01-29 10:49:30'
            ],
            [
                'item_id' => 1,
                'unit_id' => 2,
                'factor' => 5,
                'price' => 16500,
                'is_base' => 0,
                'created_at' => '2025-12-05 07:36:50',
                'updated_at' => '2026-01-29 10:49:30'
            ],
            [
                'item_id' => 3,
                'unit_id' => 1,
                'factor' => 1,
                'price' => 3500,
                'is_base' => 1,
                'created_at' => '2025-12-05 07:44:52',
                'updated_at' => '2025-12-06 06:40:32'
            ],
            [
                'item_id' => 3,
                'unit_id' => 2,
                'factor' => 5,
                'price' => 16500,
                'is_base' => 0,
                'created_at' => '2025-12-05 07:44:52',
                'updated_at' => '2025-12-06 06:40:32'
            ],
            [
                'item_id' => 3,
                'unit_id' => 3,
                'factor' => 40,
                'price' => 125000,
                'is_base' => 0,
                'created_at' => '2025-12-05 07:45:07',
                'updated_at' => '2025-12-06 06:40:32'
            ],
            [
                'item_id' => 4,
                'unit_id' => 1,
                'factor' => 1,
                'price' => 3500,
                'is_base' => 1,
                'created_at' => '2025-12-06 04:02:37',
                'updated_at' => '2025-12-06 06:39:35'
            ],
            [
                'item_id' => 4,
                'unit_id' => 3,
                'factor' => 24,
                'price' => 60000,
                'is_base' => 0,
                'created_at' => '2025-12-06 04:02:37',
                'updated_at' => '2025-12-06 06:39:35'
            ],
            [
                'item_id' => 5,
                'unit_id' => 1,
                'factor' => 1,
                'price' => 12000,
                'is_base' => 1,
                'created_at' => '2025-12-15 14:19:34',
                'updated_at' => '2026-01-27 02:43:54'
            ],
            [
                'item_id' => 5,
                'unit_id' => 3,
                'factor' => 12,
                'price' => 60000,
                'is_base' => 0,
                'created_at' => '2025-12-15 14:19:34',
                'updated_at' => '2026-01-27 02:43:54'
            ],
            [
                'item_id' => 6,
                'unit_id' => 1,
                'factor' => 1,
                'price' => 3500,
                'is_base' => 1,
                'created_at' => '2026-01-27 02:28:02',
                'updated_at' => '2026-01-27 02:28:02'
            ],
            [
                'item_id' => 6,
                'unit_id' => 3,
                'factor' => 24,
                'price' => null,
                'is_base' => 0,
                'created_at' => '2026-01-27 02:28:02',
                'updated_at' => '2026-01-27 02:28:02'
            ]
        ]);
    }
}