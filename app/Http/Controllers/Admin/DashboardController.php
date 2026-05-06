<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Product;
use App\Models\Article;
use App\Models\FinancialRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::count();
        $admins = Admin::count();
        $articles = Article::count();
        $products = Product::count();
        $income = FinancialRecord::where('type', 'deposit')->where('status', 2)->sum('amount');

        return response()->json([
            'code' => 200,
            'data' => [
                'users' => $users,
                'admins' => $admins,
                'articles' => $articles,
                'products' => $products,
                'income' => round($income, 2),
            ]
        ]);
    }
}
