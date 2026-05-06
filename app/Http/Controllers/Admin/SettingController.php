<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->input('group');

        $query = Setting::query();
        if ($group) {
            $query->where('group', $group);
        }

        $settings = $query->orderBy('sort', 'desc')->get();

        return response()->json([
            'code' => 200,
            'data' => $settings,
        ]);
    }

    public function show($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json(['code' => 404, 'message' => '设置不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $setting,
        ]);
    }

    public function update(Request $request, $key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json(['code' => 404, 'message' => '设置不存在'], 404);
        }

        $value = $request->input('value');
        Setting::setValue($key, $value, $setting->type);

        return response()->json(['code' => 200, 'message' => '设置更新成功']);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        // 过滤掉非设置字段的参数（如 _token 等）
        $reservedKeys = ['_token', '_method'];
        foreach ($data as $key => $value) {
            if (in_array($key, $reservedKeys)) continue;
            Setting::setValue($key, $value);
        }

        return response()->json(['code' => 200, 'message' => '设置保存成功']);
    }

    public function getByGroup($group)
    {
        $settings = Setting::where('group', $group)->orderBy('sort', 'desc')->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->typed_value;
        }

        return response()->json([
            'code' => 200,
            'data' => $result,
        ]);
    }

    public function updateByGroup(Request $request, $group)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $type = 'string';
            if (is_bool($value)) {
                $type = 'boolean';
            } elseif (is_numeric($value)) {
                $type = 'number';
            } elseif (is_array($value)) {
                $type = 'array';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['type' => $type, 'group' => $group]
            );
            Setting::setValue($key, $value, $type);
        }

        return response()->json(['code' => 200, 'message' => '设置保存成功']);
    }
}
