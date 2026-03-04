<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// ── GET ── Return all past events
if ($method === 'GET') {
    $pdo = getDB();
    $stmt = $pdo->query(
        'SELECT 
            id AS eventId,
            event_name AS eventName,
            event_date AS eventDate,
            image_path AS imagePath
        FROM past_events
        ORDER BY event_date DESC'
    );
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $events
    ]);
    exit;
}

// ── Protect everything  ──
require_auth();

// ── POST ── Create new past event
if ($method === 'POST') {
    $eventName = $input['eventName'] ?? '';
    $eventDate = $input['eventDate'] ?? '';

    $errors = [];
    if (empty($eventName)) $errors[] = 'eventName is required';
    if (empty($eventDate)) $errors[] = 'eventDate is required';
    if ($eventDate && $eventDate > date('Y-m-d')) {
        $errors[] = 'eventDate must be in the past';
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
    $stmt = $pdo->prepare(
        'INSERT INTO past_events (event_name, event_date)
        VALUES (?, ?)'
    );
    $stmt->execute([$eventName, $eventDate]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => [
            'eventId' => $pdo->lastInsertId(),
            'message' => 'Past event created'
        ]
    ]);
    exit;
}

// ── DELETE ── Delete a past event
if ($method === 'DELETE') {
    $eventId = $_GET['id'] ?? null;

    if (!$eventId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Event ID is required',
                'details' => []
            ]
        ]);
        exit;
    }

    $pdo = getDB();
    $check = $pdo->prepare('SELECT id FROM past_events WHERE id = ?');
    $check->execute([$eventId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'NOT_FOUND',
                'message' => 'Event not found',
                'details' => []
            ]
        ]);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM past_events WHERE id = ?');
    $stmt->execute([$eventId]);

    echo json_encode([
        'success' => true,
        'data' => ['message' => 'Past event deleted']
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