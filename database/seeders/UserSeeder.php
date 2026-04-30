<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the users table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('users')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('users')->insert([
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@serenovr.my.id',
                'role' => 'admin',
                'email_verified_at' => null,
                'password' => '$2y$12$5eFNSan9qmbP.iD8KycU0enA.K1yGovOkO7e6FjIKp/N5/EZrbaSW',
                'remember_token' => null,
                'created_at' => '2025-12-05 05:25:05',
                'updated_at' => '2026-04-29 11:19:59'
            ],
            [
                'name' => 'Inventory',
                'username' => 'inventory',
                'email' => 'inventory@serenovr.my.id',
                'role' => 'inventory',
                'email_verified_at' => null,
                'password' => '$2y$12$c3T00eLs0GthtA91TQg2gucrKpRL9KxkhudeqesOKcA5a/9jeXUYu',
                'remember_token' => null,
                'created_at' => '2025-12-05 05:27:33',
                'updated_at' => '2026-04-29 11:21:05'
            ],
            [
                'name' => 'Sales',
                'username' => 'sales',
                'email' => 'sales@serenovr.my.id',
                'role' => 'sales',
                'email_verified_at' => null,
                'password' => '$2y$12$CQMLF8aEhaY3Hy1FvTDuzOFFIhZhleJAoVDIRBNYSerkxnUUHQ94y',
                'remember_token' => null,
                'created_at' => '2025-12-12 10:45:37',
                'updated_at' => '2026-04-29 11:21:14'
            ]
        ]);
    }
}