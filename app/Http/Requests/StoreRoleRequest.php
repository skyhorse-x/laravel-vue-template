<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:50|unique:roles,name',
            'slug' => 'required|max:50|unique:roles,slug',
            'description' => 'sometimes|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '角色名称不能为空',
            'name.max' => '角色名称最多50个字符',
            'name.unique' => '该角色名称已存在',
            'slug.required' => '角色标识不能为空',
            'slug.max' => '角色标识最多50个字符',
            'slug.unique' => '该角色标识已存在',
            'description.max' => '描述最多255个字符',
            'permissions.array' => '权限格式不正确',
            'permissions.*.exists' => '选择的权限不存在',
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
