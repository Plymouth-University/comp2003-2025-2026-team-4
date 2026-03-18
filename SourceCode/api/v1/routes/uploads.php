<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../middleware/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// ── GET ── Return upload by path
if ($method === 'GET') {
    $imagePath = $_GET['imagePath'] ?? null;

    $errors = [];
    if (empty($imagePath)) {
        $errors[] = 'Image path is required.';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code'    => 'VALIDATION_ERROR',
                'message' => 'Missing or invalid path',
                'details' => $errors
            ]
        ]);
        exit;
    }

    $filePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

    if (!file_exists($filePath)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'File not found']]);
        exit;
    }

    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

// ── Protect everything below ──
require_auth();

// ── POST ── upload image, receive path in response
if ($method === 'POST') {
    $file = $_FILES['image'] ?? null;

    $errors = [];
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'No image uploaded.';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code'    => 'VALIDATION_ERROR',
                'message' => 'One or more fields are invalid.',
                'details' => $errors
            ]
        ]);
        exit;
    }
    $mimeType = mime_content_type($file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['message' => 'Invalid file type.']]);
        exit;
    }
    $category = $_POST['category'] ?? 'general';
    $category = preg_replace('/[^a-z0-9_]/', '', $category); // sanitise

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('img_', true) . '.' . $ext;
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/' . $category . '/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // create folder if it doesn't exist
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => ['message' => 'Failed to save file']]);
        exit;
    }
    $imagePath = '/assets/uploads/' . $category . '/' . $filename;

    http_response_code(201);
    echo json_encode([
    'success' => true,
    'data' => [
        'imagePath' => $imagePath
    ]
    ]);
    exit;
}

// ── DELETE ── Delete an image by path
if ($method === 'DELETE') {
    $imagePath = $input['imagePath'] ?? null;

    $errors = [];
    if (empty($imagePath)) {
        $errors[] = 'Image path is required.';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code'    => 'VALIDATION_ERROR',
                'message' => 'Missing or invalid path',
                'details' => $errors
            ]
        ]);
        exit;
    }

    $filePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

    if (!file_exists($filePath)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'File not found']]);
        exit;
    }

    if (!unlink($filePath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => ['message' => 'Failed to delete file']]);
        exit;
    }

    http_response_code(200);
    echo json_encode(['success' => true]);
    exit;
}

// ── Method not allowed ── 
http_response_code(405);
echo json_encode([
    'success' => false,
    'error' => [
        'code'    => 'METHOD_NOT_ALLOWED',
        'message' => 'Method not allowed',
        'details' => []
    ]
]);