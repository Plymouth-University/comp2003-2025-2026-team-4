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
$body = json_decode(file_get_contents("php://input"), true) ?? [];

function rowToPastEvent(array $r): array {
    return [
        "eventId" => (int)$r["id"],
        "eventName" => (string)$r["event_name"],
        "eventDate" => (string)$r["event_date"],
        "imagePath" => $r["image_path"],
    ];
}

if ($method === "GET" && $id === null) {
    $stmt = $pdo->query("SELECT * FROM past_events ORDER BY event_date DESC");
    $rows = $stmt->fetchAll();
    ok(array_map("rowToPastEvent", $rows));
}

if ($method === "POST" && $id === null) {
    requireAuth();

    $errors = [];
    $eventName = trim((string)($body["eventName"] ?? ""));
    $eventDate = (string)($body["eventDate"] ?? "");

    if ($eventName === "") $errors[] = "eventName is required";
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventDate)) $errors[] = "eventDate must be YYYY-MM-DD";

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventDate)) {
        if (strtotime($eventDate) > strtotime(date("Y-m-d"))) $errors[] = "eventDate must be in the past";
    }

    if (!empty($errors)) {
        errorResponse("VALIDATION_ERROR", "Invalid fields", $errors, 400);
    }

    $stmt = $pdo->prepare("INSERT INTO past_events (event_name, event_date) VALUES (:n, :d)");
    $stmt->execute([
        ":n" => $eventName,
        ":d" => $eventDate
    ]);

    ok(["eventId" => (int)$pdo->lastInsertId(), "message" => "Past event created"], 201);
}

if ($method === "DELETE" && $id !== null) {
    requireAuth();

    $eventId = (int)$id;

    $stmt = $pdo->prepare("DELETE FROM past_events WHERE id = :id");
    $stmt->execute([":id" => $eventId]);

    if ($stmt->rowCount() === 0) {
        errorResponse("NOT_FOUND", "Event not found", [], 404);
    }

    ok(["message" => "Past event deleted"]);
}

errorResponse("METHOD_NOT_ALLOWED", "Endpoint not supported", [], 405);