<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|max:50|unique:luma_admins,username',
            'email' => 'required|email|unique:luma_admins,email',
            'password' => 'required|min:8|confirmed',
            'name' => 'required|max:50',
            'role_id' => 'required|exists:roles,id',
            'status' => 'sometimes|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '用户名不能为空',
            'username.max' => '用户名最多50个字符',
            'username.unique' => '该用户名已被使用',
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '该邮箱已被使用',
            'password.required' => '密码不能为空',
            'password.min' => '密码至少6个字符',
            'name.required' => '姓名不能为空',
            'name.max' => '姓名最多50个字符',
            'role_id.required' => '请选择角色',
            'role_id.exists' => '选择的角色不存在',
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
