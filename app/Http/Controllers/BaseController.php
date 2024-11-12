<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{

    protected function successResponse($data, $message = 'Request was successful', $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }


    protected function errorResponse($message, $errors = [], $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
