<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserExtend;
use App\Models\Referral;

class ReferralController extends Controller
{
    public function getReferralCode(Request $request)
    {
        $user = $request->attributes->get('user');
        $userExtend = $user->userExtend;

        if (!$userExtend) {
            return response()->json(['code' => 400, 'message' => '用户扩展信息不存在'], 400);
        }

        $referrals = Referral::where('referrer_id', $user->id)->with('referee')->get();

        return response()->json([
            'code' => 200,
            'data' => [
                'referral_code' => $userExtend->referral_code,
                'invite_count' => $userExtend->invite_count,
                'balance' => $userExtend->balance,
                'referrals' => $referrals,
            ],
        ]);
    }

    public function getReferralInfo($code)
    {
        $userExtend = UserExtend::where('referral_code', $code)->first();
        
        if (!$userExtend) {
            return response()->json(['code' => 404, 'message' => '邀请码不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => [
                'referrer_id' => $userExtend->user_id,
                'referral_code' => $code,
            ],
        ]);
    }
}
