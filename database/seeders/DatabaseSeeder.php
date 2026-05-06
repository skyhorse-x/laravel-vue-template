<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            ArticleSeeder::class,
            ProductSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
