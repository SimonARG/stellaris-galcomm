<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait ApiResponserTrait
{
    protected function successResponse(mixed $data = null, string $message = 'Operation Successful', int $code = 200): JsonResponse
    {
        return response()->json([
            'status'    => 'Success',
            'message'   => $message,
            'data'      => $data
        ], $code);
    }

    protected function createdResponse(mixed $data = null, string $message = 'Resource Created Successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    protected function errorResponse(string $message = 'An error occurred', int $code = 400): JsonResponse
    {
        Log::error($message);
        return response()->json([
            'status'    => 'Error',
            'message'   => $message,
            'data'      => null
        ], $code);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    protected function notFoundResponse(string $message = 'Resource Not Found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    protected function validationErrorResponse(mixed $errors, string $message = 'Validation Error'): JsonResponse
    {
        return response()->json([
            'status'    => 'Validation Error',
            'message'   => $message,
            'errors'    => $errors
        ], 422);
    }
}