<?php
declare(strict_types=1);

function jsonResponse(int $statusCode, array $payload): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($payload);
    exit;
}

function success($data, int $statusCode = 200): void {
    jsonResponse($statusCode, [
        'success' => true,
        'data' => $data
    ]);
}

function errorResponse(string $code, string $message, array $details = [], int $statusCode = 400): void {
    jsonResponse($statusCode, [
        'success' => false,
        'error' => [
            'code' => $code,
            'message' => $message,
            'details' => $details
        ]
    ]);
}