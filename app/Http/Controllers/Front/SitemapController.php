<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $baseUrl = config('app.url', 'http://localhost');

        $articles = Article::where('status', 1)
            ->orderBy('updated_at', 'desc')
            ->limit(1000)
            ->get(['id', 'updated_at']);

        $content = view('sitemap', compact('baseUrl', 'articles'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
