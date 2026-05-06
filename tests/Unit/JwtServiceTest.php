<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\JwtService;
use RuntimeException;

class JwtServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 设置 JWT 配置
        config(['jwt.secret' => 'test_secret_key_for_jwt_unit_testing_only']);
        config(['jwt.expire' => 3600]);
        config(['jwt.issuer' => 'http://localhost']);
    }

    public function test_generate_token_success(): void
    {
        $userId = 1;
        $token = JwtService::generateToken($userId);

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }

    public function test_verify_token_success(): void
    {
        $userId = 123;
        $token = JwtService::generateToken($userId);

        $decoded = JwtService::verifyToken($token);

        $this->assertNotNull($decoded);
        $this->assertEquals($userId, $decoded->sub);
    }

    public function test_verify_invalid_token_returns_null(): void
    {
        $invalidToken = 'invalid.token.here';

        $decoded = JwtService::verifyToken($invalidToken);

        $this->assertNull($decoded);
    }

    public function test_generate_token_with_custom_expiration(): void
    {
        $userId = 1;
        $customExpire = 7200;
        $token = JwtService::generateToken($userId, $customExpire);

        $decoded = JwtService::verifyToken($token);

        $this->assertNotNull($decoded);
        $this->assertEquals($userId, $decoded->sub);
        $this->assertEquals($customExpire, $decoded->exp - $decoded->iat);
    }

    public function test_generate_token_without_config_throws_exception(): void
    {
        config(['jwt.secret' => null]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('JWT_SECRET 未配置');

        JwtService::generateToken(1);
    }

    public function test_verify_token_without_config_throws_exception(): void
    {
        config(['jwt.secret' => null]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('JWT_SECRET 未配置');

        JwtService::verifyToken('any.token.here');
    }
}
