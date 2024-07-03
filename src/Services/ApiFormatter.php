<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiFormatter
{
    public function formatResponse(array $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'status' => $statusCode,
            'data' => $data,
        ], $statusCode);
    }

    public function formatError(string $message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse([
            'status' => $statusCode,
            'error' => $message,
        ], $statusCode);
    }
}