<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// ── GET ── Return all site settings
if ($method === 'GET') {
    $pdo = getDB();
    $stmt = $pdo->query('SELECT setting_key, setting_value FROM site_settings');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert to key-value object
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    echo json_encode([
        'success' => true,
        'data' => $settings
    ]);
    exit;
}

// ── Protect everything  ──
require_auth();
// ── PUT ── Update site settings
if ($method === 'PUT') {
    $allowed = [
        'whatsappUrl',
        'instagramUrl', 
        'merchandiseUrl',
        'heroHeading',
        'heroSubtext'
    ];

    $errors = [];

    // Validate URLs
    foreach (['whatsappUrl', 'instagramUrl', 'merchandiseUrl'] as $key) {
        if (isset($input[$key]) && !filter_var($input[$key], FILTER_VALIDATE_URL)) {
            $errors[] = $key . ' must be a valid URL';
        }
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'One or more fields are invalid',
                'details' => $errors
            ]
        ]);
        exit;
    }

    $pdo = getDB();

    // Only update fields that were sent
    foreach ($allowed as $key) {
        if (isset($input[$key])) {
            $stmt = $pdo->prepare(
                'UPDATE site_settings SET setting_value = ? 
                WHERE setting_key = ?'
            );
            $stmt->execute([$input[$key], $key]);
        }
    }

    echo json_encode([
        'success' => true,
        'data' => ['message' => 'Settings updated']
    ]);
    exit;
}

// ── Method not allowed ← ALWAYS LAST
http_response_code(405);
echo json_encode([
    'success' => false,
    'error' => [
        'code' => 'METHOD_NOT_ALLOWED',
        'message' => 'Method not allowed',
        'details' => []
    ]
]);