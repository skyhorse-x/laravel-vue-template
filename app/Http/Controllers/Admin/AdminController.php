<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::query();
        
        if ($request->username) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }
        if ($request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $admins = $query->with('role')->paginate($request->per_page ?? 10);

        return response()->json([
            'code' => 200,
            'data' => $admins,
        ]);
    }

    public function show($id)
    {
        $admin = Admin::with('role')->find($id);
        
        if (!$admin) {
            return response()->json(['code' => 404, 'message' => '管理员不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $admin,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:luma_admins',
            'email' => 'required|email|unique:luma_admins',
            'password' => 'required|min:6',
            'name' => 'required|max:50',
        ]);

        $admin = Admin::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'role_id' => $request->role_id ?? 2,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $admin,
        ]);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        
        if (!$admin) {
            return response()->json(['code' => 404, 'message' => '管理员不存在'], 404);
        }

        $request->validate([
            'username' => 'unique:luma_admins,username,' . $id,
            'email' => 'email|unique:luma_admins,email,' . $id,
            'name' => 'max:50',
        ]);

        $data = $request->only(['name', 'email', 'username', 'role_id', 'status', 'avatar']);
        
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $admin,
        ]);
    }

    public function destroy($id)
    {
        $admin = Admin::find($id);
        
        if (!$admin) {
            return response()->json(['code' => 404, 'message' => '管理员不存在'], 404);
        }

        if ($admin->id == 1) {
            return response()->json(['code' => 400, 'message' => '不能删除超级管理员'], 400);
        }

        $admin->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }
}
