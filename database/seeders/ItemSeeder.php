<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the items table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('items')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('items')->insert([
            [
                'name' => 'Indomie Goreng',
                'code' => 'INIDM0001',
                'image' => 'items/r8l4Nnl6d0DkKNCK6KQ2y2fVeVqa5DYN6CTk5h4b.png',
                'brand_id' => 1,
                'category_id' => 1,
                'base_unit_id' => 1,
                'description' => 'indomie goreng instant noodle',
                'cost_price' => 3000,
                'sell_price' => 3500,
                'created_at' => '2025-12-05 07:03:54',
                'updated_at' => '2026-01-29 10:49:30'
            ],
            [
                'name' => 'Indomie Ayam Bawang',
                'code' => 'INIDM0002',
                'image' => 'items/W5jySweKpKXPiyoqXIG7BZCH5GecahbUb5m8RnjQ.png',
                'brand_id' => 1,
                'category_id' => 1,
                'base_unit_id' => 1,
                'description' => 'Indomie Ayam Bawang',
                'cost_price' => 3000,
                'sell_price' => 3500,
                'created_at' => '2025-12-05 07:28:52',
                'updated_at' => '2025-12-06 06:32:35'
            ],
            [
                'name' => 'Mie Sedaap Goreng',
                'code' => 'INMSDP0001',
                'image' => 'items/EkT20DN6V1iiIhCbCI7xTqRkAReBhk27wx7GZ848.jpg',
                'brand_id' => 2,
                'category_id' => 1,
                'base_unit_id' => 1,
                'description' => 'Mie Sedaap Goreng',
                'cost_price' => 3000,
                'sell_price' => 3500,
                'created_at' => '2025-12-05 07:44:52',
                'updated_at' => '2025-12-06 06:40:31'
            ],
            [
                'name' => 'Aqua 600ml',
                'code' => 'AMAQ0001',
                'image' => 'items/6GfMqNtkNuzBNGoZgPGAVQftMcF1rF8YEbP3Wvwc.jpg',
                'brand_id' => 3,
                'category_id' => 2,
                'base_unit_id' => 1,
                'description' => 'Aqua air mineral kemasan 600ml',
                'cost_price' => 2100,
                'sell_price' => 3500,
                'created_at' => '2025-12-06 04:02:37',
                'updated_at' => '2025-12-06 06:39:35'
            ],
            [
                'name' => 'Aqua 1500ml',
                'code' => 'AMAQ0002',
                'image' => 'items/J2izt9fOiXqoq9ovSTWaeZ713M8brpAauDHdbIj1.jpg',
                'brand_id' => 3,
                'category_id' => 2,
                'base_unit_id' => 1,
                'description' => 'Aqua Botol ukuran 1500ml',
                'cost_price' => 10000,
                'sell_price' => 12000,
                'created_at' => '2025-12-15 14:19:34',
                'updated_at' => '2026-01-27 02:43:54'
            ],
            [
                'name' => 'Indomie Goreng 1',
                'code' => 'INIDM0003',
                'image' => null,
                'brand_id' => 1,
                'category_id' => 1,
                'base_unit_id' => 1,
                'description' => null,
                'cost_price' => 3000,
                'sell_price' => 3500,
                'created_at' => '2026-01-27 02:28:02',
                'updated_at' => '2026-01-27 02:28:02'
            ]
        ]);
    }
}