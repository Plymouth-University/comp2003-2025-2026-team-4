<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

// ── GET ── Return all past events (past dates only)
if ($method === 'GET') {
    $pdo  = getDB();
    $stmt = $pdo->query(
        'SELECT 
            id AS eventId,
            event_name AS eventName,
            event_date AS eventDate,
            image_path AS imagePath
        FROM events
        WHERE event_date < CURDATE()
        ORDER BY event_date DESC'
    );
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $events]);
    exit;
}

// ── Protect everything below ──
require_auth();

// ── DELETE ── Delete an event
if ($method === 'DELETE') {
    $eventId = $_GET['id'] ?? null;

    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['code' => 'VALIDATION_ERROR', 'message' => 'Event ID is required', 'details' => []]]);
        exit;
    }

    $pdo   = getDB();
    $check = $pdo->prepare('SELECT id FROM events WHERE id = ?');
    $check->execute([$eventId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Event not found', 'details' => []]]);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
    $stmt->execute([$eventId]);

    echo json_encode(['success' => true, 'data' => ['message' => 'Event deleted']]);
    exit;
}

// ── Method not allowed ── ALWAYS LAST
http_response_code(405);
echo json_encode(['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'Method not allowed', 'details' => []]]);