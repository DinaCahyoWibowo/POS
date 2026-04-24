<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::where('username', 'admin')->exists()) {
            User::create([
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('12345'),
                'role' => 'admin',
            ]);
        } else {
            // ensure admin role is set
            $admin = User::where('username', 'admin')->first();
            if ($admin) {
                if ($admin->role !== 'admin') {
                    $admin->role = 'admin';
                    $admin->save();
                }
            }
        }
    }
}
