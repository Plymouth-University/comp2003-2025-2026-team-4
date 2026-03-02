<?php
declare(strict_types=1);

require_once __DIR__ . "/../helpers/response.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../middleware/auth_check.php";

$pdo = getPdo();
$method = $_SERVER["REQUEST_METHOD"];

$path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
$route = substr($path, strpos($path, "api/v1/") + strlen("api/v1/"));
$parts = explode("/", $route);

$id = $parts[1] ?? null;

function rowToUpcomingEvent(array $r): array {
    return [
        "eventId" => (int)$r["id"],
        "eventName" => (string)$r["event_name"],
        "eventLocation" => (string)$r["event_location"],
        "eventDate" => (string)$r["event_date"],
        "eventTime" => (string)$r["event_time"],
        "bookingStatus" => (string)$r["booking_status"],
        "bookingUrl" => $r["booking_url"],
        "imagePath" => $r["image_path"],
    ];
}

$body = json_decode(file_get_contents("php://input"), true) ?? [];

if ($method === "GET" && $id === null) {
    $stmt = $pdo->query("SELECT * FROM upcoming_events ORDER BY event_date ASC, event_time ASC");
    $rows = $stmt->fetchAll();
    ok(array_map("rowToUpcomingEvent", $rows));
}

if ($method === "POST" && $id === null) {
    requireAuth();

    $errors = [];
    $eventName = trim((string)($body["eventName"] ?? ""));
    $eventLocation = trim((string)($body["eventLocation"] ?? ""));
    $eventDate = (string)($body["eventDate"] ?? "");
    $eventTime = (string)($body["eventTime"] ?? "");
    $bookingStatus = (string)($body["bookingStatus"] ?? "OPEN");
    $bookingUrl = trim((string)($body["bookingUrl"] ?? ""));

    if ($eventName === "") $errors[] = "eventName is required";
    if ($eventLocation === "") $errors[] = "eventLocation is required";
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventDate)) $errors[] = "eventDate must be YYYY-MM-DD";
    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $eventTime)) $errors[] = "eventTime must be HH:MM or HH:MM:SS";

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventDate)) {
        if (strtotime($eventDate) < strtotime(date("Y-m-d"))) $errors[] = "eventDate must be a future date";
    }

    $allowedStatus = ["OPEN","FULL","CANCELLED"];
    if (!in_array($bookingStatus, $allowedStatus, true)) {
        $errors[] = "bookingStatus must be OPEN, FULL, or CANCELLED";
    }

    if (!empty($errors)) {
        errorResponse("VALIDATION_ERROR", "Invalid fields", $errors, 400);
    }

    $stmt = $pdo->prepare("
        INSERT INTO upcoming_events (event_name, event_location, event_date, event_time, booking_status, booking_url)
        VALUES (:n, :loc, :d, :t, :s, :u)
    ");

    $stmt->execute([
        ":n" => $eventName,
        ":loc" => $eventLocation,
        ":d" => $eventDate,
        ":t" => strlen($eventTime) === 5 ? $eventTime . ":00" : $eventTime,
        ":s" => $bookingStatus,
        ":u" => $bookingUrl !== "" ? $bookingUrl : null,
    ]);

    ok(["eventId" => (int)$pdo->lastInsertId(), "message" => "Event created"], 201);
}

if ($method === "PUT" && $id !== null) {
    requireAuth();
    $eventId = (int)$id;

    $fields = [];
    $params = [":id" => $eventId];

    $map = [
        "eventName" => "event_name",
        "eventLocation" => "event_location",
        "eventDate" => "event_date",
        "eventTime" => "event_time",
        "bookingStatus" => "booking_status",
        "bookingUrl" => "booking_url",
    ];

    foreach ($map as $jsonKey => $dbCol) {
        if (array_key_exists($jsonKey, $body)) {
            $fields[] = "{$dbCol} = :{$jsonKey}";
            $val = $body[$jsonKey];

            if ($jsonKey === "eventTime" && is_string($val) && strlen($val) === 5) {
                $val .= ":00";
            }
            $params[":{$jsonKey}"] = ($val === "" ? null : $val);
        }
    }

    if (empty($fields)) {
        errorResponse("VALIDATION_ERROR", "No fields provided", ["Provide at least one field to update"], 400);
    }

    $chk = $pdo->prepare("SELECT id FROM upcoming_events WHERE id = :id");
    $chk->execute([":id" => $eventId]);
    if (!$chk->fetch()) {
        errorResponse("NOT_FOUND", "Event not found", [], 404);
    }

    $sql = "UPDATE upcoming_events SET " . implode(", ", $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    ok(["message" => "Event updated"]);
}

if ($method === "DELETE" && $id !== null) {
    requireAuth();
    $eventId = (int)$id;

    $stmt = $pdo->prepare("DELETE FROM upcoming_events WHERE id = :id");
    $stmt->execute([":id" => $eventId]);

    if ($stmt->rowCount() === 0) {
        errorResponse("NOT_FOUND", "Event not found", [], 404);
    }

    ok(["message" => "Event deleted"]);
}

errorResponse("METHOD_NOT_ALLOWED", "Endpoint not supported", [], 405);