<?php

// auth.php
// Handles POST /auth (login) and POST /auth/logout

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

// ─── LOGIN ────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $body     = json_decode(file_get_contents('php://input'), true);
    $username = trim($body['username'] ?? '');
    $password = trim($body['password'] ?? '');

    // Validation — both fields required
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'errors'  => [
                [
                    'code'    => 'VALIDATION_ERROR',
                    'message' => 'Username and password are required',
                    'details' => []
                ]
            ]
        ]);
        exit;
    }

    $pdo  = getDB();

    // Fetch admin record
    $stmt = $pdo->prepare(
        'SELECT id, username, password, failed_attempts, locked_until
        FROM admins
        WHERE username = :username
        LIMIT 1'
    );
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Account does not exist
    if (!$admin) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'errors'  => [
                [
                    'code'    => 'INVALID_CREDENTIALS',
                    'message' => 'Invalid username or password',
                    'details' => []
                ]
            ]
        ]);
        exit;
    }

    // Check if account is locked
    if ($admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'errors'  => [
                [
                    'code'    => 'ACCOUNT_LOCKED',
                    'message' => 'Account locked. Try again in 15 minutes.',
                    'details' => []
                ]
            ]
        ]);
        exit;
    }

    // Verify password
    if (!password_verify($password, $admin['password'])) {
        $new_attempts = $admin['failed_attempts'] + 1;

        if ($new_attempts >= 5) {
            // Lock for 15 minutes
            $locked_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $pdo->prepare(
                'UPDATE admins
                SET failed_attempts = :attempts, locked_until = :locked
                WHERE id = :id'
            )->execute([
                ':attempts' => $new_attempts,
                ':locked'   => $locked_until,
                ':id'       => $admin['id']
            ]);

            http_response_code(429);
            echo json_encode([
                'success' => false,
                'errors'  => [
                    [
                        'code'    => 'ACCOUNT_LOCKED',
                        'message' => 'Account locked. Try again in 15 minutes.',
                        'details' => []
                    ]
                ]
            ]);
        } else {
            $pdo->prepare(
                'UPDATE admins SET failed_attempts = :attempts WHERE id = :id'
            )->execute([
                ':attempts' => $new_attempts,
                ':id'       => $admin['id']
            ]);

            http_response_code(401);
            echo json_encode([
                'success' => false,
                'errors'  => [
                    [
                        'code'    => 'INVALID_CREDENTIALS',
                        'message' => 'Invalid username or password',
                        'details' => []
                    ]
                ]
            ]);
        }
        exit;
    }

    // Password correct — generate token and store in admins table
    $token      = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+2 hours'));

    $pdo->prepare(
        'UPDATE admins
        SET token = :token,
            token_expires_at = :expires_at,
            failed_attempts = 0,
            locked_until = NULL
        WHERE id = :id'
    )->execute([
        ':token'      => $token,
        ':expires_at' => $expires_at,
        ':id'         => $admin['id']
    ]);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data'    => [
            'token'     => $token,
            'expiresAt' => $expires_at
        ]
    ]);
    exit;
}

// ─── METHOD NOT ALLOWED ───────────────────────────────────────────────────────
http_response_code(405);
echo json_encode([
    'success' => false,
    'errors'  => [
        [
            'code'    => 'METHOD_NOT_ALLOWED',
            'message' => 'Method not allowed',
            'details' => []
        ]
    ]
]);