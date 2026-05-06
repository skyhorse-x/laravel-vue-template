<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        $admin = $request->attributes->get('admin');
        
        if (!$admin) {
            return response()->json(['code' => 401, 'message' => '未登录'], 401);
        }

        if ($admin->role_id == 1) {
            return $next($request);
        }

        if (!$permission) {
            return response()->json(['code' => 403, 'message' => '权限不足'], 403);
        }

        $permissions = $admin->role->permissions->pluck('name')->toArray();
        
        if (!in_array($permission, $permissions)) {
            return response()->json(['code' => 403, 'message' => '权限不足'], 403);
        }

        return $next($request);
    }
}
