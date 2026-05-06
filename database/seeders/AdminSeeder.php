<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['id' => 1],
            [
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('123456'),
                'name' => '超级管理员',
                'role_id' => 1,
                'status' => 1,
            ]
        );

        Admin::firstOrCreate(
            ['id' => 2],
            [
                'username' => 'manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('manager123'),
                'name' => '管理员',
                'role_id' => 2,
                'status' => 1,
            ]
        );
    }
}
