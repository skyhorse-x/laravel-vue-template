<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');

        $query = Article::where('user_id', $user->id);

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        if ($request->keyword) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        $articles = $query->orderBy('id', 'desc')->paginate($request->per_page ?? 10);

        return response()->json([
            'code' => 200,
            'data' => $articles
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $article = Article::where('user_id', $user->id)->find($id);

        if (!$article) {
            return response()->json(['code' => 404, 'message' => '文章不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $article
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('user');

        $request->validate([
            'title' => 'required|max:200',
            'content' => 'required',
            'category' => 'nullable|max:50',
            'cover' => 'nullable|url',
            'status' => 'in:0,1'
        ], [
            'title.required' => '请输入文章标题',
            'title.max' => '标题最多200个字符',
            'content.required' => '请输入文章内容',
        ]);

        $article = Article::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'cover' => $request->cover,
            'status' => $request->status ?? 1,
            'view_count' => 0
        ]);

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $article
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $article = Article::where('user_id', $user->id)->find($id);

        if (!$article) {
            return response()->json(['code' => 404, 'message' => '文章不存在'], 404);
        }

        $request->validate([
            'title' => 'required|max:200',
            'content' => 'required',
            'category' => 'nullable|max:50',
            'cover' => 'nullable|url',
            'status' => 'in:0,1'
        ]);

        $article->update($request->only(['title', 'content', 'category', 'cover', 'status']));

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $article
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $article = Article::where('user_id', $user->id)->find($id);

        if (!$article) {
            return response()->json(['code' => 404, 'message' => '文章不存在'], 404);
        }

        $article->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }
}
