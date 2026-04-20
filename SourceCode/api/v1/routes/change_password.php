<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'errors' => [['code' => 'METHOD_NOT_ALLOWED', 'message' => 'Method not allowed']]]);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$current  = trim($body['currentPassword'] ?? '');
$new      = trim($body['newPassword'] ?? '');
$confirm  = trim($body['confirmPassword'] ?? '');

if (empty($current) || empty($new) || empty($confirm)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => [['code' => 'VALIDATION_ERROR', 'message' => 'All fields are required']]]);
    exit;
}

if ($new !== $confirm) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => [['code' => 'VALIDATION_ERROR', 'message' => 'New passwords do not match']]]);
    exit;
}

if (strlen($new) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => [['code' => 'VALIDATION_ERROR', 'message' => 'Password must be at least 8 characters']]]);
    exit;
}

$pdo = getDB();
$stmt = $pdo->query('SELECT id, password FROM admins LIMIT 1');
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || !password_verify($current, $admin['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'errors' => [['code' => 'INVALID_CREDENTIALS', 'message' => 'Current password is incorrect']]]);
    exit;
}

$new_hash = password_hash($new, PASSWORD_BCRYPT);
$pdo->prepare('UPDATE admins SET password = ? WHERE id = ?')->execute([$new_hash, $admin['id']]);

echo json_encode(['success' => true, 'data' => ['message' => 'Password updated successfully']]);