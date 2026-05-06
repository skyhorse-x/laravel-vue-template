<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function categories()
    {
        $categories = \App\Models\ProductCategory::where('status', 1)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'code' => 200,
            'data' => $categories,
        ]);
    }

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->keyword) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status !== null) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 1);
        }

        if ($request->is_hot) {
            $query->where('is_hot', 1);
        }

        if ($request->is_new) {
            $query->where('is_new', 1);
        }

        if ($request->is_featured) {
            $query->where('is_featured', 1);
        }

        $products = $query->orderBy('sort', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 12);

        return response()->json([
            'code' => 200,
            'data' => $products,
        ]);
    }

    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product || $product->status != 1) {
            return response()->json(['code' => 404, 'message' => '商品不存在或已下架'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $product,
        ]);
    }
}
