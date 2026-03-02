<?php
declare(strict_types=1);

require_once __DIR__ . "/../helpers/response.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../middleware/auth_check.php";

$pdo = getPdo();
$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    // Public - return all settings as key/value object
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    $rows = $stmt->fetchAll();

    $settings = [];
    foreach ($rows as $r) {
        $settings[(string)$r["setting_key"]] = (string)$r["setting_value"];
    }

    ok($settings);
}

if ($method === "PUT") {
    // Protected - update one or multiple settings
    requireAuth();

    $body = json_decode(file_get_contents("php://input"), true);
    if (!is_array($body) || empty($body)) {
        errorResponse("VALIDATION_ERROR", "Invalid fields", ["Body must be a JSON object of settings"], 400);
    }

    // Validate keys/values
    $errors = [];
    foreach ($body as $k => $v) {
        if (!is_string($k) || trim($k) === "") $errors[] = "setting key must be a non-empty string";
        if (!is_string($v)) $errors[] = "setting_value for '{$k}' must be a string";
    }
    if (!empty($errors)) {
        errorResponse("VALIDATION_ERROR", "Invalid fields", $errors, 400);
    }

    // Upsert settings (insert if missing, update if exists)
    $stmt = $pdo->prepare("
        INSERT INTO site_settings (setting_key, setting_value)
        VALUES (:k, :v)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ");

    foreach ($body as $k => $v) {
        $stmt->execute([
            ":k" => $k,
            ":v" => $v
        ]);
    }

    ok(["message" => "Settings updated"]);
}

errorResponse("METHOD_NOT_ALLOWED", "Endpoint not supported", [], 405);