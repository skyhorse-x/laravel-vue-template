<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();
        
        if ($request->title) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $articles = $query->orderBy('sort', 'desc')->paginate($request->per_page ?? 10);

        return response()->json([
            'code' => 200,
            'data' => $articles,
        ]);
    }

    public function show($id)
    {
        $article = Article::find($id);
        
        if (!$article) {
            return response()->json(['code' => 404, 'message' => '文章不存在'], 404);
        }

        $article->increment('view_count');

        return response()->json([
            'code' => 200,
            'data' => $article,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $article = Article::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'cover' => $request->cover,
            'category' => $request->category,
            'status' => $request->status ?? 1,
            'sort' => $request->sort ?? 0,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $article,
        ]);
    }

    public function update(Request $request, $id)
    {
        $article = Article::find($id);
        
        if (!$article) {
            return response()->json(['code' => 404, 'message' => '文章不存在'], 404);
        }

        $article->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'cover' => $request->cover,
            'category' => $request->category,
            'status' => $request->status,
            'sort' => $request->sort,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $article,
        ]);
    }

    public function destroy($id)
    {
        $article = Article::find($id);
        
        if (!$article) {
            return response()->json(['code' => 404, 'message' => '文章不存在'], 404);
        }

        $article->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }
}
