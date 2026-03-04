<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// ── GET ── Return all visible testimonials
if ($method === 'GET') {
    $pdo = getDB();
    $stmt = $pdo->query(
        'SELECT
            id AS testimonialId,
            quote_text AS quoteText,
            author_name AS authorName,
            author_role AS authorRole,
            is_visible AS isVisible
        FROM testimonials
        WHERE is_visible = 1
        ORDER BY id ASC'
    );
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $testimonials
    ]);
    exit;
}

// ── Protect everything ──
require_auth();

// ── POST ── Create new testimonial
if ($method === 'POST') {
    $quoteText  = $input['quoteText'] ?? '';
    $authorName = $input['authorName'] ?? '';
    $authorRole = $input['authorRole'] ?? null;
    $isVisible  = $input['isVisible'] ?? 1;

    $errors = [];
    if (empty($quoteText))  $errors[] = 'quoteText is required';
    if (empty($authorName)) $errors[] = 'authorName is required';

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
    $stmt = $pdo->prepare(
        'INSERT INTO testimonials 
        (quote_text, author_name, author_role, is_visible)
        VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$quoteText, $authorName, $authorRole, $isVisible]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => [
            'testimonialId' => $pdo->lastInsertId(),
            'message' => 'Testimonial created'
        ]
    ]);
    exit;
}

// ── PUT ── Update testimonial
if ($method === 'PUT') {
    $testimonialId = $_GET['id'] ?? null;

    if (!$testimonialId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Testimonial ID is required',
                'details' => []
            ]
        ]);
        exit;
    }

    $pdo = getDB();
    $check = $pdo->prepare('SELECT id FROM testimonials WHERE id = ?');
    $check->execute([$testimonialId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'NOT_FOUND',
                'message' => 'Testimonial not found',
                'details' => []
            ]
        ]);
        exit;
    }

    $fields = [];
    $values = [];

    if (isset($input['quoteText']))  { $fields[] = 'quote_text = ?';  $values[] = $input['quoteText']; }
    if (isset($input['authorName'])) { $fields[] = 'author_name = ?'; $values[] = $input['authorName']; }
    if (isset($input['authorRole'])) { $fields[] = 'author_role = ?'; $values[] = $input['authorRole']; }
    if (isset($input['isVisible']))  { $fields[] = 'is_visible = ?';  $values[] = $input['isVisible']; }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'No fields provided to update',
                'details' => []
            ]
        ]);
        exit;
    }

    $values[] = $testimonialId;
    $sql = 'UPDATE testimonials SET ' . implode(', ', $fields) . ' WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    echo json_encode([
        'success' => true,
        'data' => ['message' => 'Testimonial updated']
    ]);
    exit;
}

// ── DELETE ── Delete a testimonial
if ($method === 'DELETE') {
    $testimonialId = $_GET['id'] ?? null;

    if (!$testimonialId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Testimonial ID is required',
                'details' => []
            ]
        ]);
        exit;
    }

    $pdo = getDB();
    $check = $pdo->prepare('SELECT id FROM testimonials WHERE id = ?');
    $check->execute([$testimonialId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'NOT_FOUND',
                'message' => 'Testimonial not found',
                'details' => []
            ]
        ]);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM testimonials WHERE id = ?');
    $stmt->execute([$testimonialId]);

    echo json_encode([
        'success' => true,
        'data' => ['message' => 'Testimonial deleted']
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