<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();
        
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $roles = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'code' => 200,
            'data' => $roles,
        ]);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        
        if (!$role) {
            return response()->json(['code' => 404, 'message' => '角色不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $role,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:luma_roles',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? 1,
        ]);

        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        
        if (!$role) {
            return response()->json(['code' => 404, 'message' => '角色不存在'], 404);
        }

        if ($role->id == 1) {
            return response()->json(['code' => 400, 'message' => '不能修改超级管理员角色'], 400);
        }

        $request->validate([
            'name' => 'unique:luma_roles,name,' . $id,
        ]);

        $role->update($request->only(['name', 'description', 'status']));

        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $role,
        ]);
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        
        if (!$role) {
            return response()->json(['code' => 404, 'message' => '角色不存在'], 404);
        }

        if ($role->id == 1) {
            return response()->json(['code' => 400, 'message' => '不能删除超级管理员角色'], 400);
        }

        $role->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }

    public function permissions()
    {
        $permissions = Permission::all();
        
        return response()->json([
            'code' => 200,
            'data' => $permissions,
        ]);
    }
}
