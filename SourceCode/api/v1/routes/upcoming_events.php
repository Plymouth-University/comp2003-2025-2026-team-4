<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// ── GET ── Return all upcoming events (future dates only)
if ($method === 'GET') {
    $pdo = getDB();
    $stmt = $pdo->query(
        'SELECT 
            id AS eventId,
            event_name AS eventName,
            event_location AS eventLocation,
            event_date AS eventDate,
            event_start_time AS eventStartTime,
            event_end_time AS eventEndTime,
            image_path AS imagePath
        FROM events
        WHERE CONCAT(event_date, " ", event_end_time)  >= CONVERT_TZ(NOW(), "+01:00", "+00:00")
        ORDER BY event_date ASC'
    );
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $events]);
    exit;
}

// ── Protect everything below ──
require_auth();

// ── POST ── Create new upcoming event
if ($method === 'POST') {
    $eventName     = $input['eventName'] ?? '';
    $eventLocation = $input['eventLocation'] ?? '';
    $eventDate     = $input['eventDate'] ?? '';
    $eventStartTime = $input['startTime'] ?? '';
    $eventEndTime   = $input['endTime'] ?? '';

    $errors = [];
    if (empty($eventName))     $errors[] = 'eventName is required';
    if (empty($eventLocation)) $errors[] = 'eventLocation is required';
    if (empty($eventDate))     $errors[] = 'eventDate is required';
    if (empty($eventStartTime)) $errors[] = 'eventStartTime is required';
    if (empty($eventEndTime))   $errors[] = 'eventEndTime is required'; 


    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code'    => 'VALIDATION_ERROR',
                'message' => 'One or more fields are invalid',
                'details' => $errors
            ]
        ]);
        exit;
    }

    $pdo  = getDB();
    $imagePath = $input['imagePath'] ?? null;

    $stmt = $pdo->prepare(
        'INSERT INTO events (event_name, event_location, event_date, event_start_time, event_end_time, image_path)
        VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$eventName, $eventLocation, $eventDate, $eventStartTime, $eventEndTime, $imagePath]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data'    => ['eventId' => $pdo->lastInsertId(), 'message' => 'Event created']
    ]);
    exit;
}

// ── PUT ── Update existing event
if ($method === 'PUT') {
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

    $fields = [];
    $values = [];

    if (isset($input['eventName']))     { $fields[] = 'event_name = ?';     $values[] = $input['eventName']; }
    if (isset($input['eventLocation'])) { $fields[] = 'event_location = ?'; $values[] = $input['eventLocation']; }
    if (isset($input['eventDate']))     { $fields[] = 'event_date = ?';     $values[] = $input['eventDate']; }
    if (isset($input['eventTime']))     { $fields[] = 'event_time = ?';     $values[] = $input['eventTime']; }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['code' => 'VALIDATION_ERROR', 'message' => 'No fields provided to update', 'details' => []]]);
        exit;
    }

    $values[] = $eventId;
    $stmt = $pdo->prepare('UPDATE events SET ' . implode(', ', $fields) . ' WHERE id = ?');
    $stmt->execute($values);

    echo json_encode(['success' => true, 'data' => ['message' => 'Event updated']]);
    exit;
}

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