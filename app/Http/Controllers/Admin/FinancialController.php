<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialRecord::with('user');
        
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status !== null) {
            $query->where('status', $request->status);
        }
        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $records = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'code' => 200,
            'data' => $records,
        ]);
    }

    public function show($id)
    {
        $record = FinancialRecord::with('user')->find($id);
        
        if (!$record) {
            return response()->json(['code' => 404, 'message' => '记录不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $record,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:luma_users,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'title' => 'required',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['code' => 404, 'message' => '用户不存在'], 404);
        }

        DB::beginTransaction();
        try {
            $record = FinancialRecord::create([
                'user_id' => $request->user_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'title' => $request->title,
                'description' => $request->description,
                'order_no' => uniqid(),
                'status' => $request->status ?? 1,
            ]);

            if ($user->userExtend) {
                if ($request->type == 'income') {
                    $user->userExtend->increment('balance', $request->amount);
                } else {
                    if ($user->userExtend->balance < $request->amount) {
                        DB::rollBack();
                        return response()->json(['code' => 400, 'message' => '用户余额不足'], 400);
                    }
                    $user->userExtend->decrement('balance', $request->amount);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code' => 500, 'message' => '创建失败'], 500);
        }

        return response()->json([
            'code' => 200,
            'message' => '创建成功',
            'data' => $record,
        ]);
    }

    public function statistics(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $income = FinancialRecord::where('type', 'income')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $expense = FinancialRecord::where('type', 'expense')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        return response()->json([
            'code' => 200,
            'data' => [
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
            ],
        ]);
    }
}
