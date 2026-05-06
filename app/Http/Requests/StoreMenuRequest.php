<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:50',
            'path' => 'required|max:255',
            'icon' => 'sometimes|max:50',
            'parent_id' => 'sometimes|nullable|exists:menus,id',
            'sort' => 'sometimes|integer|min:0',
            'status' => 'sometimes|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '菜单名称不能为空',
            'name.max' => '菜单名称最多50个字符',
            'path.required' => '菜单路径不能为空',
            'path.max' => '菜单路径最多255个字符',
            'icon.max' => '图标最多50个字符',
            'parent_id.exists' => '父级菜单不存在',
            'sort.integer' => '排序必须为整数',
            'sort.min' => '排序最小值为0',
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
