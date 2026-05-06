<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->attributes->get('user')?->id;

        return [
            'name' => 'sometimes|max:50',
            'phone' => 'sometimes|nullable|unique:luma_users,phone,' . $userId,
            'avatar' => 'sometimes|nullable|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => '姓名最多50个字符',
            'phone.unique' => '该手机号已被使用',
            'avatar.max' => '头像地址过长',
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
