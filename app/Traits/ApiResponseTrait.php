<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function apiResponse($success, $data = null, $message = '', $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }
}
