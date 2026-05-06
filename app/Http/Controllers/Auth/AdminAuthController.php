<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Services\JwtService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['code' => 401, 'message' => '用户名或密码错误'], 401);
        }

        if ($admin->status != 1) {
            return response()->json(['code' => 401, 'message' => '管理员已禁用'], 401);
        }

        $token = JwtService::generateToken($admin->id);

        return response()->json([
            'code' => 200,
            'message' => '登录成功',
            'data' => [
                'admin' => $admin,
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
            'email' => 'required|email|exists:luma_admins',
        ]);

        $admin = Admin::where('email', $request->email)->first();
        $token = Str::random(64);

        DB::table('luma_admin_password_reset_tokens')->updateOrInsert(
            ['email' => $admin->email],
            ['token' => $token, 'created_at' => now()]
        );

        EmailService::sendResetPasswordEmail($admin->email, $token);

        return response()->json(['code' => 200, 'message' => '重置链接已发送到邮箱']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = DB::table('luma_admin_password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return response()->json(['code' => 400, 'message' => '无效的重置链接'], 400);
        }

        // 检查令牌是否过期（默认60分钟）
        $expireMinutes = config('auth.passwords.admins.expire', 60);
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes($expireMinutes)->isPast()) {
            DB::table('luma_admin_password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['code' => 400, 'message' => '重置链接已过期，请重新获取'], 400);
        }

        Admin::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('luma_admin_password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['code' => 200, 'message' => '密码重置成功']);
    }

    public function me(Request $request)
    {
        $admin = $request->attributes->get('admin');
        $admin->role = $admin->role;
        
        return response()->json([
            'code' => 200,
            'data' => $admin,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = $request->attributes->get('admin');
        
        $request->validate([
            'name' => 'max:50',
            'email' => 'email|unique:luma_admins,email,' . $admin->id,
        ]);

        $admin->update($request->only(['name', 'email', 'avatar']));

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $admin,
        ]);
    }

    public function changePassword(Request $request)
    {
        $admin = $request->attributes->get('admin');

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json(['code' => 400, 'message' => '旧密码错误'], 400);
        }

        $admin->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['code' => 200, 'message' => '密码修改成功']);
    }
}
