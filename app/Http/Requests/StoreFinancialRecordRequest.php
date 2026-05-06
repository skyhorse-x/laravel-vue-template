<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFinancialRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:luma_users,id',
            'type' => 'required|integer|in:1,2',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'sometimes|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => '用户ID不能为空',
            'user_id.exists' => '用户不存在',
            'type.required' => '类型不能为空',
            'type.integer' => '类型必须为整数',
            'type.in' => '类型值无效',
            'amount.required' => '金额不能为空',
            'amount.numeric' => '金额必须为数字',
            'amount.min' => '金额最小值为0.01',
            'description.max' => '描述最多255个字符',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'code' => 422,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422));
    }
}
