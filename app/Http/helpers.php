<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

if (! function_exists('errorResponse')) {
    function errorResponse(string $error_message, string $message = null, array $data = []): JsonResponse
    {
        Log::error($message ?? $error_message, ['error' => $error_message]);

        return response()->json([
            'success' => false,
            'message' => $message ?? $error_message,
            'data'    => array_merge(['error' => $error_message], $data),
        ])->setStatusCode(500);
    }
}

if (! function_exists('successResponse')) {
    function successResponse(array|object $data = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
        ])->setStatusCode(200);
    }
}
