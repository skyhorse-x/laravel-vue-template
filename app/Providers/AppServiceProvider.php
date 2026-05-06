<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $key = $request->input('account') ?: $request->ip();
            return Limit::perMinutes(15, 5)->by($key)->response(function () {
                return response()->json(['code' => 429, 'message' => '登录尝试次数过多，请15分钟后再试'], 429);
            });
        });

        RateLimiter::for('auth-api', function (Request $request) {
            return Limit::perMinutes(5, 5)->by($request->ip())->response(function () {
                return response()->json(['code' => 429, 'message' => '请求过于频繁，请稍后再试'], 429);
            });
        });
    }
}
