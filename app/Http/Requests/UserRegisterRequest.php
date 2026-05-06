<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:50|unique:luma_users,username',
            'email' => 'required|email|unique:luma_users,email',
            'password' => 'required|min:6',
            'name' => 'required|max:50',
            'referral_code' => 'nullable|exists:luma_user_extends,referral_code',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '用户名不能为空',
            'username.max' => '用户名最多50个字符',
            'username.unique' => '该用户名已被注册',
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '该邮箱已被注册',
            'password.required' => '密码不能为空',
            'password.min' => '密码至少6个字符',
            'name.required' => '姓名不能为空',
            'name.max' => '姓名最多50个字符',
            'referral_code.exists' => '推荐码无效',
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
