<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserExtend;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['id' => 1],
            [
                'name' => '测试会员',
                'email' => 'user@example.com',
                'password' => Hash::make('12345'),
                'status' => 1,
            ]
        );

        UserExtend::firstOrCreate(
            ['user_id' => $user->id],
            [
                'referral_code' => Str::random(10),
                'balance' => 0,
                'invite_count' => 0,
            ]
        );
    }
}
