<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:200',
            'content' => 'required',
            'category' => 'sometimes|max:50',
            'status' => 'sometimes|integer|in:0,1',
            'author' => 'sometimes|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '文章标题不能为空',
            'title.max' => '文章标题最多200个字符',
            'content.required' => '文章内容不能为空',
            'category.max' => '分类最多50个字符',
            'author.max' => '作者最多50个字符',
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
