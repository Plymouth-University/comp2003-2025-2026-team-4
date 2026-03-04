<?php

// auth_check.php
// Middleware: validates Bearer token on protected routes (POST / PUT / DELETE)
// Include at the top of any endpoint that requires admin authentication.

require_once __DIR__ . '/../config/db.php';

function require_auth() {
    $headers = getallheaders();

    // Check Authorization header exists
    if (!isset($headers['Authorization']) && !isset($headers['authorization'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'errors' => [
                [
                    'code'    => 'UNAUTHORIZED',
                    'message' => 'Authorization header missing',
                    'details' => []
                ]
            ]
        ]);
        exit;
    }

    $auth_header = $headers['Authorization'] ?? $headers['authorization'];

    // Must be a Bearer token
    if (!str_starts_with($auth_header, 'Bearer ')) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'errors' => [
                [
                    'code'    => 'UNAUTHORIZED',
                    'message' => 'Invalid token format. Expected: Bearer <token>',
                    'details' => []
                ]
            ]
        ]);
        exit;
    }

    $token = trim(substr($auth_header, 7));

    if (empty($token)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'errors' => [
                [
                    'code'    => 'UNAUTHORIZED',
                    'message' => 'Token is empty',
                    'details' => []
                ]
            ]
        ]);
        exit;
    }

    // Validate token directly against the admins table
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'SELECT id, username, token_expires_at
        FROM admins
        WHERE token = :token
        LIMIT 1'
    );
    $stmt->execute([':token' => $token]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'errors'  => [
                [
                    'code'    => 'UNAUTHORIZED',
                    'message' => 'Invalid or expired token',
                    'details' => []
                ]
            ]
        ]);
        exit;
    }

    // Check token has not expired
    if (strtotime($admin['token_expires_at']) < time()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'errors'  => [
                [
                    'code'    => 'TOKEN_EXPIRED',
                    'message' => 'Session has expired. Please log in again.',
                    'details' => []
                ]
            ]
        ]);
        exit;
    }

    // Token is valid — return username so the endpoint can use it if needed
    return $admin['username'];
}