<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['id' => 1],
            [
                'name' => '超级管理员',
                'description' => '拥有所有权限',
                'status' => 1,
            ]
        );

        Role::firstOrCreate(
            ['id' => 2],
            [
                'name' => '管理员',
                'description' => '普通管理员',
                'status' => 1,
            ]
        );
    }
}
