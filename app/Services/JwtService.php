<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;

class JwtService
{
    public static function generateToken($userId, $expire = null)
    {
        $secret = config('jwt.secret');

        if (!$secret) {
            throw new RuntimeException('JWT_SECRET 未配置，请运行 php artisan jwt:secret 生成密钥');
        }

        $expire = $expire ?: config('jwt.expire');
        $payload = [
            'iss' => config('jwt.issuer'),
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $expire,
        ];
        return JWT::encode($payload, $secret, 'HS256');
    }

    public static function verifyToken($token)
    {
        $secret = config('jwt.secret');

        if (!$secret) {
            throw new RuntimeException('JWT_SECRET 未配置，请运行 php artisan jwt:secret 生成密钥');
        }

        try {
            return JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Exception) {
            return null;
        }
    }
}
