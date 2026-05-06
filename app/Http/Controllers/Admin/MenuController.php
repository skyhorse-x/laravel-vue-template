<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::where('parent_id', 0)->with('children')->orderBy('sort', 'asc')->get();

        return response()->json([
            'code' => 200,
            'data' => $menus,
        ]);
    }

    public function all()
    {
        $menus = Menu::orderBy('sort', 'asc')->get();

        return response()->json([
            'code' => 200,
            'data' => $menus,
        ]);
    }

    public function show($id)
    {
        $menu = Menu::find($id);
        
        if (!$menu) {
            return response()->json(['code' => 404, 'message' => '菜单不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $menu,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'route' => $request->route,
            'icon' => $request->icon,
            'parent_id' => $request->parent_id ?? 0,
            'sort' => $request->sort ?? 0,
            'status' => $request->status ?? 1,
            'module' => $request->module,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $menu,
        ]);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);
        
        if (!$menu) {
            return response()->json(['code' => 404, 'message' => '菜单不存在'], 404);
        }

        $menu->update($request->only(['name', 'route', 'icon', 'parent_id', 'sort', 'status', 'module']));

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $menu,
        ]);
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);
        
        if (!$menu) {
            return response()->json(['code' => 404, 'message' => '菜单不存在'], 404);
        }

        Menu::where('parent_id', $id)->delete();
        $menu->delete();

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }
}
