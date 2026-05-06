<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserExtend;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_extend(): void
    {
        $user = User::factory()->create();
        UserExtend::create([
            'user_id' => $user->id,
            'referral_code' => 'test123',
            'balance' => 100.00,
            'invite_count' => 5,
        ]);

        $this->assertNotNull($user->userExtend);
        $this->assertEquals('test123', $user->userExtend->referral_code);
        $this->assertEquals(100.00, $user->userExtend->balance);
        $this->assertEquals(5, $user->userExtend->invite_count);
    }

    public function test_user_status_is_integer_casted(): void
    {
        $user = User::factory()->create(['status' => 1]);

        $this->assertIsInt($user->status);
    }

    public function test_user_password_is_hidden(): void
    {
        $user = User::factory()->create();

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_user_email_is_unique(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create(['email' => 'test@example.com']);
    }
}
