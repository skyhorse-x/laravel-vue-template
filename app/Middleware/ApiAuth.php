<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class ApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['code' => 401, 'message' => '未登录'], 401);
        }

        $token = str_replace('Bearer ', '', $token);

        try {
            $secret = config('jwt.secret');

            if (!$secret) {
                return response()->json(['code' => 500, 'message' => 'JWT 未正确配置，请联系管理员'], 500);
            }

            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $user = User::find($decoded->sub);

            if (!$user || $user->status != 1) {
                return response()->json(['code' => 401, 'message' => '用户不存在或已禁用'], 401);
            }

            $request->attributes->set('user', $user);
            return $next($request);
        } catch (\Exception) {
            return response()->json(['code' => 401, 'message' => 'token无效或已过期'], 401);
        }
    }
}
