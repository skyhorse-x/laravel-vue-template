<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'user.view', 'display_name' => '查看用户', 'module' => '用户管理'],
            ['name' => 'user.create', 'display_name' => '创建用户', 'module' => '用户管理'],
            ['name' => 'user.edit', 'display_name' => '编辑用户', 'module' => '用户管理'],
            ['name' => 'user.delete', 'display_name' => '删除用户', 'module' => '用户管理'],
            
            ['name' => 'admin.view', 'display_name' => '查看管理员', 'module' => '管理员管理'],
            ['name' => 'admin.create', 'display_name' => '创建管理员', 'module' => '管理员管理'],
            ['name' => 'admin.edit', 'display_name' => '编辑管理员', 'module' => '管理员管理'],
            ['name' => 'admin.delete', 'display_name' => '删除管理员', 'module' => '管理员管理'],
            
            ['name' => 'role.view', 'display_name' => '查看角色', 'module' => '角色管理'],
            ['name' => 'role.create', 'display_name' => '创建角色', 'module' => '角色管理'],
            ['name' => 'role.edit', 'display_name' => '编辑角色', 'module' => '角色管理'],
            ['name' => 'role.delete', 'display_name' => '删除角色', 'module' => '角色管理'],
            
            ['name' => 'article.view', 'display_name' => '查看文章', 'module' => '文章管理'],
            ['name' => 'article.create', 'display_name' => '创建文章', 'module' => '文章管理'],
            ['name' => 'article.edit', 'display_name' => '编辑文章', 'module' => '文章管理'],
            ['name' => 'article.delete', 'display_name' => '删除文章', 'module' => '文章管理'],
            
            ['name' => 'menu.view', 'display_name' => '查看菜单', 'module' => '菜单管理'],
            ['name' => 'menu.create', 'display_name' => '创建菜单', 'module' => '菜单管理'],
            ['name' => 'menu.edit', 'display_name' => '编辑菜单', 'module' => '菜单管理'],
            ['name' => 'menu.delete', 'display_name' => '删除菜单', 'module' => '菜单管理'],
            
            ['name' => 'financial.view', 'display_name' => '查看财务', 'module' => '财务管理'],
            ['name' => 'financial.create', 'display_name' => '创建财务记录', 'module' => '财务管理'],
            ['name' => 'financial.statistics', 'display_name' => '财务统计', 'module' => '财务管理'],

            ['name' => 'setting.view', 'display_name' => '查看系统设置', 'module' => '系统设置'],
            ['name' => 'setting.edit', 'display_name' => '编辑系统设置', 'module' => '系统设置'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
