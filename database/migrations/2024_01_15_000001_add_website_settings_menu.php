<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $menuExists = DB::table('luma_menus')->where('route', '/admin/settings?group=site')->exists();

        if (!$menuExists) {
            $maxId = DB::table('luma_menus')->max('id') ?? 0;
            DB::table('luma_menus')->insert([
                'id' => $maxId + 1,
                'name' => '网站设置',
                'route' => '/admin/settings?group=site',
                'icon' => '',
                'parent_id' => 4,
                'sort' => 4,
                'status' => 1,
                'module' => 'setting',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $basicSetting = DB::table('luma_menus')->where('id', 43)->first();
        if ($basicSetting && $basicSetting->name === '系统设置') {
            DB::table('luma_menus')->where('id', 43)->update(['name' => '基础设置', 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        DB::table('luma_menus')->where('route', '/admin/settings?group=site')->delete();
        DB::table('luma_menus')->where('id', 43)->update(['name' => '系统设置', 'updated_at' => now()]);
    }
};
