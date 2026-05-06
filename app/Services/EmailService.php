<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    public static function sendResetPasswordEmail($email, $token)
    {
        $url = config('app.url') . '/reset-password?token=' . $token . '&email=' . $email;
        
        Mail::raw("点击以下链接重置密码：\n" . $url, function ($message) use ($email) {
            $message->to($email)
                    ->subject('密码重置');
        });
    }

    public static function sendVerifyEmail($email, $token)
    {
        $url = config('app.url') . '/verify-email?token=' . $token . '&email=' . $email;
        
        Mail::raw("点击以下链接验证邮箱：\n" . $url, function ($message) use ($email) {
            $message->to($email)
                    ->subject('邮箱验证');
        });
    }

    public static function sendReferralNotification($email, $referrerName)
    {
        Mail::raw("恭喜！您被 {$referrerName} 推荐注册成功", function ($message) use ($email) {
            $message->to($email)
                    ->subject('推荐注册成功');
        });
    }
}
