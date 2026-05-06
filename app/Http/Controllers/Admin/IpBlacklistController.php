<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IpBlacklist;
use Carbon\Carbon;

class IpBlacklistController extends Controller
{
    public function index(Request $request)
    {
        $query = IpBlacklist::orderBy('id', 'desc');

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->ip) {
            $query->where('ip', 'like', '%' . $request->ip . '%');
        }

        $data = $query->paginate($request->per_page ?? 20);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip' => 'required|string|max:50',
            'type' => 'required|in:block,login',
            'reason' => 'nullable|string|max:500',
            'expired_days' => 'nullable|integer|min:1',
        ], [
            'ip.required' => '请输入IP地址',
            'type.required' => '请选择类型',
        ]);

        $expiredAt = null;
        if ($request->expired_days) {
            $expiredAt = Carbon::now()->addDays($request->expired_days);
        }

        $operator = $request->attributes->get('admin');

        IpBlacklist::block(
            $request->ip,
            $request->type,
            $request->reason,
            $expiredAt,
            $operator->id ?? null,
            $operator->username ?? null
        );

        return response()->json(['code' => 200, 'message' => '添加成功']);
    }

    public function destroy($id)
    {
        $record = IpBlacklist::find($id);
        if (!$record) {
            return response()->json(['code' => 404, 'message' => '记录不存在'], 404);
        }

        $record->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }

    public function batchDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        IpBlacklist::whereIn('id', $request->ids)->delete();

        return response()->json(['code' => 200, 'message' => '批量删除成功']);
    }
}
