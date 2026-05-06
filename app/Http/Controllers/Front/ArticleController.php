<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::where('status', 1);
        
        if ($request->category) {
            $query->where('category', $request->category);
        }

        $articles = $query->orderBy('sort', 'desc')->paginate($request->per_page ?? 10);

        return response()->json([
            'code' => 200,
            'data' => $articles,
        ]);
    }

    public function show($id)
    {
        $article = Article::where('status', 1)->find($id);
        
        if (!$article) {
            return response()->json(['code' => 404, 'message' => '文章不存在'], 404);
        }

        $article->increment('view_count');

        return response()->json([
            'code' => 200,
            'data' => $article,
        ]);
    }

    public function categories()
    {
        $categories = Article::where('status', 1)
            ->select('category')
            ->distinct()
            ->get()
            ->pluck('category');

        return response()->json([
            'code' => 200,
            'data' => $categories,
        ]);
    }
}
