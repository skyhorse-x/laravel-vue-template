<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class ProductController extends Controller
{
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
        }
        if ($request->is_featured) {
            $query->where('is_featured', 1);
        }
        if ($request->is_hot) {
            $query->where('is_hot', 1);
        }
        if ($request->is_new) {
            $query->where('is_new', 1);
        }

        $products = $query->orderBy('sort', 'desc')->orderBy('id', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'code' => 200,
            'data' => $products,
        ]);
    }

    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json(['code' => 404, 'message' => '商品不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $product,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => '请输入商品名称',
            'price.required' => '请输入商品价格',
            'price.numeric' => '价格必须是数字',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
            'content' => $request->content,
            'price' => $request->price,
            'original_price' => $request->original_price ?? 0,
            'stock' => $request->stock ?? 0,
            'cover' => $request->cover,
            'images' => $request->images ?? [],
            'category_id' => $request->category_id ?? 0,
            'category_name' => $request->category_name,
            'status' => $request->status ?? 1,
            'is_featured' => $request->is_featured ?? 0,
            'is_hot' => $request->is_hot ?? 0,
            'is_new' => $request->is_new ?? 0,
            'sort' => $request->sort ?? 0,
            'specs' => $request->specs ?? [],
            'attributes' => $request->attributes ?? [],
        ];

        $product = Product::create($data);

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $product,
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['code' => 404, 'message' => '商品不存在'], 404);
        }

        $data = [
            'name' => $request->name ?? $product->name,
            'description' => $request->description ?? $product->description,
            'content' => $request->content ?? $product->content,
            'price' => $request->price ?? $product->price,
            'original_price' => $request->original_price ?? $product->original_price,
            'stock' => $request->stock ?? $product->stock,
            'cover' => $request->cover ?? $product->cover,
            'images' => $request->images ?? $product->images,
            'category_id' => $request->category_id ?? $product->category_id,
            'category_name' => $request->category_name ?? $product->category_name,
            'status' => $request->status ?? $product->status,
            'is_featured' => $request->is_featured ?? $product->is_featured,
            'is_hot' => $request->is_hot ?? $product->is_hot,
            'is_new' => $request->is_new ?? $product->is_new,
            'sort' => $request->sort ?? $product->sort,
            'specs' => $request->specs ?? $product->specs,
            'attributes' => $request->attributes ?? $product->attributes,
        ];

        if ($request->slug) {
            $data['slug'] = $request->slug;
        } else {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update($data);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['code' => 404, 'message' => '商品不存在'], 404);
        }

        $product->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功',
        ]);
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->ids;

        if (empty($ids)) {
            return response()->json(['code' => 400, 'message' => '请选择要删除的商品'], 400);
        }

        Product::whereIn('id', $ids)->delete();

        return response()->json([
            'code' => 200,
            'message' => '批量删除成功',
        ]);
    }

    public function categories()
    {
        $categories = ProductCategory::orderBy('sort', 'desc')->orderBy('id', 'desc')->get();

        return response()->json([
            'code' => 200,
            'data' => $categories,
        ]);
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => '请输入分类名称',
        ]);

        $category = ProductCategory::create([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
            'parent_id' => $request->parent_id ?? 0,
            'sort' => $request->sort ?? 0,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $category,
        ]);
    }

    public function categoryUpdate(Request $request, $id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json(['code' => 404, 'message' => '分类不存在'], 404);
        }

        $category->update([
            'name' => $request->name ?? $category->name,
            'slug' => $request->slug ?? $category->slug,
            'description' => $request->description ?? $category->description,
            'parent_id' => $request->parent_id ?? $category->parent_id,
            'sort' => $request->sort ?? $category->sort,
            'status' => $request->status ?? $category->status,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $category,
        ]);
    }

    public function categoryDestroy($id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json(['code' => 404, 'message' => '分类不存在'], 404);
        }

        if (Product::where('category_id', $id)->exists()) {
            return response()->json(['code' => 400, 'message' => '该分类下有商品，无法删除'], 400);
        }

        $category->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功',
        ]);
    }
}
