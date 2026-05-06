<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->get('group', 'site');

        $settings = Setting::where('group', $group)
            ->where('type', '!=', 'password')
            ->orderBy('sort', 'desc')
            ->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->typed_value;
        }

        return response()->json([
            'code' => 200,
            'data' => $result,
        ]);
    }

    public function show($key)
    {
        $setting = Setting::where('key', $key)
            ->where('type', '!=', 'password')
            ->first();

        if (!$setting) {
            return response()->json(['code' => 404, 'message' => '设置不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $setting->typed_value,
        ]);
    }
}
