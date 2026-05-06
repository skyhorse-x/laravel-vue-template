<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserExtend;

class UserController extends Controller
{
    public function balance(Request $request)
    {
        $user = $request->attributes->get('user');
        $extend = UserExtend::where('user_id', $user->id)->first();

        return response()->json([
            'code' => 200,
            'data' => [
                'balance' => $extend ? $extend->balance : 0
            ]
        ]);
    }
}
