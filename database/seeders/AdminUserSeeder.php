<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;


class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           AdminUser::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin#0987'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        AdminUser::create([
            'name' => 'Admin User',
            'email' => 'manager@example.com',
            'password' => Hash::make('Admin#0987'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
