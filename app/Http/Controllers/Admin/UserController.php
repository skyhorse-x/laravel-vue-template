<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $users = $query->with('userExtend')->paginate($request->per_page ?? 10);

        return response()->json([
            'code' => 200,
            'data' => $users,
        ]);
    }

    public function show($id)
    {
        $user = User::with('userExtend', 'referrals')->find($id);
        
        if (!$user) {
            return response()->json(['code' => 404, 'message' => '用户不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:luma_users',
            'password' => 'required|min:6',
            'name' => 'required|max:50',
            'username' => 'nullable|max:50|unique:luma_users',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'username' => $request->username,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['code' => 404, 'message' => '用户不存在'], 404);
        }

        $request->validate([
            'email' => 'email|unique:luma_users,email,' . $id,
            'name' => 'max:50',
            'username' => 'nullable|max:50|unique:luma_users,username,' . $id,
        ]);

        $data = $request->only(['name', 'email', 'username', 'status', 'phone', 'avatar']);
        
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['code' => 404, 'message' => '用户不存在'], 404);
        }

        $user->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->ids;
        
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['code' => 400, 'message' => '请选择要删除的用户'], 400);
        }

        User::destroy($ids);

        return response()->json(['code' => 200, 'message' => '批量删除成功']);
    }
}
