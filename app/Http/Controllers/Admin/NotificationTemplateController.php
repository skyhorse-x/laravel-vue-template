<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationTemplate;

class NotificationTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationTemplate::query();

        if ($request->keyword) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $templates = $query->orderBy('id', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'code' => 200,
            'data' => $templates,
        ]);
    }

    public function show($id)
    {
        $template = NotificationTemplate::find($id);

        if (!$template) {
            return response()->json(['code' => 404, 'message' => '模板不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $template,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:luma_notification_templates',
            'name' => 'required',
            'type' => 'required',
            'content' => 'required',
        ], [
            'code.required' => '请输入模板代码',
            'code.unique' => '模板代码已存在',
            'name.required' => '请输入模板名称',
            'type.required' => '请选择通知类型',
            'content.required' => '请输入通知内容',
        ]);

        $template = NotificationTemplate::create([
            'code' => $request->code,
            'name' => $request->name,
            'type' => $request->type,
            'subject' => $request->subject,
            'content' => $request->content,
            'variables' => $request->variables ?? [],
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $template,
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = NotificationTemplate::find($id);

        if (!$template) {
            return response()->json(['code' => 404, 'message' => '模板不存在'], 404);
        }

        $data = [
            'name' => $request->name ?? $template->name,
            'type' => $request->type ?? $template->type,
            'subject' => $request->subject ?? $template->subject,
            'content' => $request->content ?? $template->content,
            'variables' => $request->variables ?? $template->variables,
            'status' => $request->status ?? $template->status,
        ];

        if ($request->code && $request->code !== $template->code) {
            $exists = NotificationTemplate::where('code', $request->code)->where('id', '!=', $id)->exists();
            if ($exists) {
                return response()->json(['code' => 400, 'message' => '模板代码已存在'], 400);
            }
            $data['code'] = $request->code;
        }

        $template->update($data);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $template,
        ]);
    }

    public function destroy($id)
    {
        $template = NotificationTemplate::find($id);

        if (!$template) {
            return response()->json(['code' => 404, 'message' => '模板不存在'], 404);
        }

        $template->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功',
        ]);
    }
}
