<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, string $message = '操作成功', int $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function created($data = null, string $message = '创建成功'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    public static function updated($data = null, string $message = '更新成功'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    public static function deleted(string $message = '删除成功'): JsonResponse
    {
        return self::success(null, $message, 200);
    }

    public static function error(string $message = '操作失败', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'code' => $code,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    public static function unauthorized(string $message = '未授权'): JsonResponse
    {
        return self::error($message, 401);
    }

    public static function forbidden(string $message = '禁止访问'): JsonResponse
    {
        return self::error($message, 403);
    }

    public static function notFound(string $message = '资源不存在'): JsonResponse
    {
        return self::error($message, 404);
    }

    public static function validationError($errors, string $message = '参数验证失败'): JsonResponse
    {
        return self::error($message, 422, $errors);
    }

    public static function serverError(string $message = '服务器内部错误'): JsonResponse
    {
        return self::error($message, 500);
    }

    public static function paginate($paginator, string $message = '获取成功'): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ], 200);
    }
}
