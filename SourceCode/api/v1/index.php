<?php
declare(strict_types=1);

function respond(int $statusCode, array $payload): void {
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function ok($data, int $status = 200): void {
    respond($status, ["success" => true, "data" => $data]);
}

function errorResponse(string $code, string $message, array $details = [], int $status = 400): void {
    respond($status, [
        "success" => false,
        "error" => [
            "code" => $code,
            "message" => $message,
            "details" => $details
        ]
    ]);
}