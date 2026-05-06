<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialRecord;
use App\Models\UserExtend;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');

        $query = FinancialRecord::where('user_id', $user->id);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $records = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'code' => 200,
            'data' => $records,
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        $record = FinancialRecord::where('user_id', $user->id)->find($id);

        if (!$record) {
            return response()->json(['code' => 404, 'message' => '记录不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $record,
        ]);
    }

    public function deposit(Request $request)
    {
        $user = $request->attributes->get('user');

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:alipay,wechat,bank'
        ], [
            'amount.required' => '请输入充值金额',
            'amount.min' => '充值金额最小为0.01',
            'payment_method.required' => '请选择支付方式'
        ]);

        $orderNo = 'DEP' . date('YmdHis') . Str::random(8);

        $record = FinancialRecord::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'title' => '账户充值',
            'description' => '通过' . $this->getPaymentMethodName($request->payment_method) . '充值',
            'order_no' => $orderNo,
            'status' => 0
        ]);

        return response()->json([
            'code' => 200,
            'message' => '充值申请已提交',
            'data' => [
                'order_no' => $orderNo,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method
            ]
        ]);
    }

    public function withdraw(Request $request)
    {
        $user = $request->attributes->get('user');

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'account' => 'required|string|max:100',
            'account_name' => 'nullable|string|max:50'
        ], [
            'amount.required' => '请输入提现金额',
            'amount.min' => '提现金额最小为0.01',
            'account.required' => '请输入提现账户'
        ]);

        $extend = UserExtend::where('user_id', $user->id)->first();
        $balance = $extend ? $extend->balance : 0;

        if ($request->amount > $balance) {
            return response()->json(['code' => 400, 'message' => '余额不足'], 400);
        }

        $orderNo = 'WTH' . date('YmdHis') . Str::random(8);

        DB::transaction(function () use ($user, $extend, $request, $orderNo, $balance) {
            if ($extend) {
                $extend->balance = $balance - $request->amount;
                $extend->save();
            }

            FinancialRecord::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'amount' => $request->amount,
                'title' => '申请提现',
                'description' => '提现至: ' . $request->account,
                'order_no' => $orderNo,
                'status' => 0
            ]);
        });

        return response()->json([
            'code' => 200,
            'message' => '提现申请已提交',
            'data' => [
                'order_no' => $orderNo,
                'amount' => $request->amount
            ]
        ]);
    }

    private function getPaymentMethodName($method)
    {
        $map = [
            'alipay' => '支付宝',
            'wechat' => '微信支付',
            'bank' => '银行卡'
        ];
        return $map[$method] ?? $method;
    }
}
