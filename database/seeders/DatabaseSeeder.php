<?php

namespace Database\Seeders;


use Database\Seeders\CategorySeeder;
use Database\Seeders\ItemUnitSeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\PaymentSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SaleItemSeeder;
use Database\Seeders\SaleSeeder;
use Database\Seeders\StockMovementLineSeeder;
use Database\Seeders\StockMovementSeeder;
use Database\Seeders\StockOpnameLineSeeder;
use Database\Seeders\StockOpnameSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            UnitSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ItemSeeder::class,
            ItemUnitSeeder::class,
            UserSeeder::class,
            SaleSeeder::class,
            SaleItemSeeder::class,
            PaymentSeeder::class,
            StockMovementSeeder::class,
            StockMovementLineSeeder::class,
            StockOpnameSeeder::class,
            StockOpnameLineSeeder::class,
        ]);
    }

}