<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('user')->orderBy('id', 'desc');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->is_read !== null) {
            $query->where('is_read', $request->is_read);
        }

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('content', 'like', '%' . $request->keyword . '%');
            });
        }

        $data = $query->paginate($request->per_page ?? 20);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:200',
            'content' => 'nullable|string',
            'link' => 'nullable|string|max:500',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
        ], [
            'type.required' => '请选择通知类型',
            'title.required' => '请输入通知标题',
        ]);

        if (!empty($request->user_ids)) {
            $users = User::whereIn('id', $request->user_ids)->get();
            foreach ($users as $user) {
                Notification::send($user->id, $request->type, $request->title, $request->content, $request->link);
            }
        } else {
            Notification::sendToAll($request->type, $request->title, $request->content, $request->link);
        }

        return response()->json(['code' => 200, 'message' => '发送成功']);
    }

    public function destroy($id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json(['code' => 404, 'message' => '通知不存在'], 404);
        }

        $notification->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }

    public function batchDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        Notification::whereIn('id', $request->ids)->delete();

        return response()->json(['code' => 200, 'message' => '批量删除成功']);
    }
}
