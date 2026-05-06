<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');

        $query = Notification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('user_id', 0);
        });

        if ($request->is_read !== null) {
            $query->where('is_read', $request->is_read);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $data = $query->orderBy('id', 'desc')->paginate($request->per_page ?? 20);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $notification = Notification::where('user_id', $user->id)->orWhere('user_id', 0)->find($id);

        if (!$notification) {
            return response()->json(['code' => 404, 'message' => '通知不存在'], 404);
        }

        return response()->json(['code' => 200, 'data' => $notification]);
    }

    public function markRead(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $notification = Notification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('user_id', 0);
        })->find($id);

        if (!$notification) {
            return response()->json(['code' => 404, 'message' => '通知不存在'], 404);
        }

        $notification->update([
            'is_read' => 1,
            'read_at' => now()
        ]);

        return response()->json(['code' => 200, 'message' => '已标记为已读']);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->attributes->get('user');

        Notification::where('user_id', $user->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);

        return response()->json(['code' => 200, 'message' => '全部已标记为已读']);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $notification = Notification::where('user_id', $user->id)->find($id);

        if (!$notification) {
            return response()->json(['code' => 404, 'message' => '通知不存在'], 404);
        }

        $notification->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }
}
