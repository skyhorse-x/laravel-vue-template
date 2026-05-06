<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationLog;

class OperationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = OperationLog::orderBy('id', 'desc');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->username) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        if ($request->module) {
            $query->where('module', $request->module);
        }

        if ($request->ip) {
            $query->where('ip', $request->ip);
        }

        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $data = $query->paginate($request->per_page ?? 20);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function modules()
    {
        $modules = OperationLog::distinct()->pluck('module')->filter()->values();

        return response()->json(['code' => 200, 'data' => $modules]);
    }

    public function show($id)
    {
        $log = OperationLog::find($id);
        if (!$log) {
            return response()->json(['code' => 404, 'message' => '日志不存在'], 404);
        }

        return response()->json(['code' => 200, 'data' => $log]);
    }

    public function destroy($id)
    {
        $log = OperationLog::find($id);
        if (!$log) {
            return response()->json(['code' => 404, 'message' => '日志不存在'], 404);
        }

        $log->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }

    public function clear(Request $request)
    {
        $days = $request->days ?? 30;
        $beforeDate = now()->subDays($days)->format('Y-m-d H:i:s');

        $count = OperationLog::where('created_at', '<', $beforeDate)->delete();

        return response()->json(['code' => 200, 'message' => "已清理 {$count} 条日志"]);
    }
}
