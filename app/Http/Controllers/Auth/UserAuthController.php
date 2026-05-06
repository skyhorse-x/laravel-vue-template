<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserExtend;
use App\Models\Referral;
use App\Services\JwtService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:luma_users',
            'email' => 'required|email|unique:luma_users',
            'password' => 'required|min:8|confirmed',
            'name' => 'required|max:50',
            'referral_code' => 'nullable|exists:luma_user_extends,referral_code',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'status' => 1,
        ]);

        $referralCode = Str::random(10);
        UserExtend::create([
            'user_id' => $user->id,
            'referral_code' => $referralCode,
            'balance' => 0,
            'invite_count' => 0,
        ]);

        if ($request->referral_code) {
            $referrerExtend = UserExtend::where('referral_code', $request->referral_code)->first();
            if ($referrerExtend && $referrerExtend->user_id != $user->id) {
                Referral::create([
                    'referrer_id' => $referrerExtend->user_id,
                    'referee_id' => $user->id,
                    'bonus' => 10.00,
                    'status' => 1,
                ]);
                $referrerExtend->increment('invite_count');
                $referrerExtend->increment('balance', 10.00);
            }
        }

        $token = JwtService::generateToken($user->id);

        return response()->json([
            'code' => 200,
            'message' => '注册成功',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function login(Request $request)
    {
        $loginType = $request->input('login_type', 'username');

        if ($loginType === 'email') {
            $request->validate([
                'account' => 'required|email',
                'password' => 'required',
            ]);
            $user = User::where('email', $request->account)->first();
        } else {
            $request->validate([
                'account' => 'required|string',
                'password' => 'required',
            ]);
            $user = User::where('username', $request->account)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['code' => 401, 'message' => '账号或密码错误'], 401);
        }

        if ($user->status != 1) {
            return response()->json(['code' => 401, 'message' => '用户已禁用'], 401);
        }

        $token = JwtService::generateToken($user->id);

        return response()->json([
            'code' => 200,
            'message' => '登录成功',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function logout()
    {
        return response()->json(['code' => 200, 'message' => '退出成功']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:luma_users',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        EmailService::sendResetPasswordEmail($user->email, $token);

        return response()->json(['code' => 200, 'message' => '重置链接已发送到邮箱']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return response()->json(['code' => 400, 'message' => '无效的重置链接'], 400);
        }

        // 检查令牌是否过期（默认60分钟）
        $expireMinutes = config('auth.passwords.users.expire', 60);
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes($expireMinutes)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['code' => 400, 'message' => '重置链接已过期，请重新获取'], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['code' => 200, 'message' => '密码重置成功']);
    }

    public function me(Request $request)
    {
        $user = $request->attributes->get('user');
        $user->userExtend = $user->userExtend;
        
        return response()->json([
            'code' => 200,
            'data' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->attributes->get('user');
        
        $request->validate([
            'name' => 'max:50',
            'phone' => 'unique:luma_users,phone,' . $user->id,
        ]);

        $user->update($request->only(['name', 'phone', 'avatar']));

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $user,
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->attributes->get('user');

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['code' => 400, 'message' => '旧密码错误'], 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['code' => 200, 'message' => '密码修改成功']);
    }
}
