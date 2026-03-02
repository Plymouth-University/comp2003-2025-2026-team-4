<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// ── GET ── Return all upcoming events
if ($method === 'GET') {
    $pdo = getDB();
    $stmt = $pdo->query(
        'SELECT 
            id AS eventId,
            event_name AS eventName,
            event_location AS eventLocation,
            event_date AS eventDate,
            event_time AS eventTime,
            booking_status AS bookingStatus,
            booking_url AS bookingUrl,
            image_path AS imagePath
        FROM upcoming_events
        ORDER BY event_date ASC'
    );
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $events]);
    exit;
}

// ── POST ── Create new upcoming event
if ($method === 'POST') {
    $eventName     = $input['eventName'] ?? '';
    $eventLocation = $input['eventLocation'] ?? '';
    $eventDate     = $input['eventDate'] ?? '';
    $eventTime     = $input['eventTime'] ?? '';
    $bookingStatus = $input['bookingStatus'] ?? 'OPEN';
    $bookingUrl    = $input['bookingUrl'] ?? null;

    $errors = [];
    if (empty($eventName))     $errors[] = 'eventName is required';
    if (empty($eventLocation)) $errors[] = 'eventLocation is required';
    if (empty($eventDate))     $errors[] = 'eventDate is required';
    if (empty($eventTime))     $errors[] = 'eventTime is required';
    if ($eventDate && $eventDate < date('Y-m-d')) {
        $errors[] = 'eventDate must be a future date';
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
        'INSERT INTO upcoming_events 
        (event_name, event_location, event_date, event_time, booking_status, booking_url)
        VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $eventName, $eventLocation, $eventDate,
        $eventTime, $bookingStatus, $bookingUrl
    ]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => [
            'eventId' => $pdo->lastInsertId(),
            'message' => 'Event created'
        ]
    ]);
    exit;
}

// ── PUT ── Update existing event
if ($method === 'PUT') {
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
    $check = $pdo->prepare('SELECT id FROM upcoming_events WHERE id = ?');
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

    $fields = [];
    $values = [];

    if (isset($input['eventName']))     { $fields[] = 'event_name = ?';     $values[] = $input['eventName']; }
    if (isset($input['eventLocation'])) { $fields[] = 'event_location = ?'; $values[] = $input['eventLocation']; }
    if (isset($input['eventDate']))     { $fields[] = 'event_date = ?';     $values[] = $input['eventDate']; }
    if (isset($input['eventTime']))     { $fields[] = 'event_time = ?';     $values[] = $input['eventTime']; }
    if (isset($input['bookingStatus'])) { $fields[] = 'booking_status = ?'; $values[] = $input['bookingStatus']; }
    if (isset($input['bookingUrl']))    { $fields[] = 'booking_url = ?';    $values[] = $input['bookingUrl']; }

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

    $values[] = $eventId;
    $sql = 'UPDATE upcoming_events SET ' . implode(', ', $fields) . ' WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    echo json_encode(['success' => true, 'data' => ['message' => 'Event updated']]);
    exit;
}

// ── DELETE ── Delete an event
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
    $check = $pdo->prepare('SELECT id FROM upcoming_events WHERE id = ?');
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

    $stmt = $pdo->prepare('DELETE FROM upcoming_events WHERE id = ?');
    $stmt->execute([$eventId]);

    echo json_encode(['success' => true, 'data' => ['message' => 'Event deleted']]);
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