<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Username and password are required',
                'details' => []
            ]
        ]);
        exit;
    }

    $pdo = getDB();

    // Check if account exists
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if locked
    if ($admin && $admin['locked_until'] && 
        new DateTime() < new DateTime($admin['locked_until'])) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'ACCOUNT_LOCKED',
                'message' => 'Account locked. Try again in 15 minutes.',
                'details' => []
            ]
        ]);
        exit;
    }

    // Check password
    if ($admin && password_verify($password, $admin['password'])) {
        // Reset failed attempts
        $reset = $pdo->prepare(
            'UPDATE admins SET failed_attempts = 0, 
            locked_until = NULL WHERE id = ?'
        );
        $reset->execute([$admin['id']]);

        // Generate simple token for Sprint 4
        $token = bin2hex(random_bytes(32));

        echo json_encode([
            'success' => true,
            'data' => [
                'token' => $token,
                'expiresInSeconds' => 86400
            ]
        ]);
    } else {
        // Wrong password - increment failed attempts
        if ($admin) {
            $attempts = $admin['failed_attempts'] + 1;
            $locked = null;

            if ($attempts >= 5) {
                $locked = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            }

            $update = $pdo->prepare(
                'UPDATE admins SET failed_attempts = ?, 
                locked_until = ? WHERE id = ?'
            );
            $update->execute([$attempts, $locked, $admin['id']]);
        }

        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'Invalid username or password',
                'details' => []
            ]
        ]);
    }
}