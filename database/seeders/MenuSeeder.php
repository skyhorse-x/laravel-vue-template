<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            // 一级菜单
            ['id' => 1, 'name' => '仪表盘', 'route' => '/admin', 'icon' => 'Dashboard', 'parent_id' => 0, 'sort' => 1, 'status' => 1, 'module' => 'dashboard'],
            ['id' => 6, 'name' => '控制台', 'route' => '/admin/dashboard', 'icon' => 'Console', 'parent_id' => 0, 'sort' => 0, 'status' => 1, 'module' => 'console'],
            ['id' => 2, 'name' => '用户管理', 'route' => '', 'icon' => 'Users', 'parent_id' => 0, 'sort' => 2, 'status' => 1, 'module' => 'user'],
            ['id' => 3, 'name' => '内容管理', 'route' => '', 'icon' => 'Article', 'parent_id' => 0, 'sort' => 3, 'status' => 1, 'module' => 'content'],
            ['id' => 7, 'name' => '商品管理', 'route' => '', 'icon' => 'Product', 'parent_id' => 0, 'sort' => 3.5, 'status' => 1, 'module' => 'product'],
            ['id' => 4, 'name' => '系统设置', 'route' => '', 'icon' => 'Setting', 'parent_id' => 0, 'sort' => 4, 'status' => 1, 'module' => 'system'],
            ['id' => 5, 'name' => '财务管理', 'route' => '/admin/financial', 'icon' => 'Financial', 'parent_id' => 0, 'sort' => 5, 'status' => 1, 'module' => 'financial'],

            // 用户管理 - 二级菜单
            ['id' => 21, 'name' => '用户列表', 'route' => '/admin/users', 'icon' => '', 'parent_id' => 2, 'sort' => 1, 'status' => 1, 'module' => 'user'],
            ['id' => 22, 'name' => '管理员列表', 'route' => '/admin/admins', 'icon' => '', 'parent_id' => 2, 'sort' => 2, 'status' => 1, 'module' => 'admin'],

            // 内容管理 - 二级菜单
            ['id' => 31, 'name' => '文章列表', 'route' => '/admin/articles', 'icon' => '', 'parent_id' => 3, 'sort' => 1, 'status' => 1, 'module' => 'article'],
            ['id' => 32, 'name' => '文章分类', 'route' => '/admin/articles?tab=categories', 'icon' => '', 'parent_id' => 3, 'sort' => 2, 'status' => 1, 'module' => 'article'],

            // 商品管理 - 二级菜单
            ['id' => 71, 'name' => '商品列表', 'route' => '/admin/products', 'icon' => '', 'parent_id' => 7, 'sort' => 1, 'status' => 1, 'module' => 'product'],
            ['id' => 72, 'name' => '商品分类', 'route' => '/admin/products?tab=categories', 'icon' => '', 'parent_id' => 7, 'sort' => 2, 'status' => 1, 'module' => 'product'],

            // 系统设置 - 二级菜单
            ['id' => 41, 'name' => '角色管理', 'route' => '/admin/roles', 'icon' => '', 'parent_id' => 4, 'sort' => 1, 'status' => 1, 'module' => 'role'],
            ['id' => 42, 'name' => '菜单管理', 'route' => '/admin/menus', 'icon' => '', 'parent_id' => 4, 'sort' => 2, 'status' => 1, 'module' => 'menu'],
            ['id' => 43, 'name' => '基础设置', 'route' => '/admin/settings', 'icon' => '', 'parent_id' => 4, 'sort' => 3, 'status' => 1, 'module' => 'setting'],
            ['id' => 44, 'name' => '网站设置', 'route' => '/admin/settings?group=site', 'icon' => '', 'parent_id' => 4, 'sort' => 4, 'status' => 1, 'module' => 'setting'],
            ['id' => 45, 'name' => '通知管理', 'route' => '/admin/notifications', 'icon' => '', 'parent_id' => 4, 'sort' => 5, 'status' => 1, 'module' => 'notification'],
            ['id' => 46, 'name' => '操作日志', 'route' => '/admin/operation-logs', 'icon' => '', 'parent_id' => 4, 'sort' => 6, 'status' => 1, 'module' => 'log'],
            ['id' => 47, 'name' => 'IP黑名单', 'route' => '/admin/ip-blacklist', 'icon' => '', 'parent_id' => 4, 'sort' => 7, 'status' => 1, 'module' => 'security'],
            ['id' => 48, 'name' => '通知模板', 'route' => '/admin/notification-templates', 'icon' => '', 'parent_id' => 4, 'sort' => 8, 'status' => 1, 'module' => 'notification'],
        ];

        foreach ($menus as $menu) {
            Menu::firstOrCreate(
                ['id' => $menu['id']],
                $menu
            );
        }
    }
}
