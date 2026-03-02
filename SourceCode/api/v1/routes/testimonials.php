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
$query = $_GET ?? [];

function rowToTestimonial(array $r): array {
    return [
        "testimonialId" => (int)$r["id"],
        "quoteText"     => (string)$r["quote_text"],
        "authorName"    => (string)$r["author_name"],
        "authorRole"    => $r["author_role"],
        "isVisible"     => (int)$r["is_visible"] === 1,
        "createdAt"     => (string)$r["created_at"],
    ];
}

$body = json_decode(file_get_contents("php://input"), true) ?? [];

if ($method === "GET" && $id === null) {
    $all = isset($query["all"]) && (string)$query["all"] === "1";

    if ($all) {
        requireAuth();
        $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
    } else {
        $stmt = $pdo->query("SELECT * FROM testimonials WHERE is_visible = 1 ORDER BY created_at DESC");
    }

    $rows = $stmt->fetchAll();
    ok(array_map("rowToTestimonial", $rows));
}

if ($method === "POST" && $id === null) {
    requireAuth();

    $errors = [];

    $quoteText  = trim((string)($body["quoteText"] ?? ""));
    $authorName = trim((string)($body["authorName"] ?? ""));
    $authorRole = isset($body["authorRole"]) ? trim((string)$body["authorRole"]) : null;
    $isVisible  = array_key_exists("isVisible", $body) ? (bool)$body["isVisible"] : true;

    if ($quoteText === "")  $errors[] = "quoteText is required";
    if ($authorName === "") $errors[] = "authorName is required";

    if (!empty($errors)) {
        errorResponse("VALIDATION_ERROR", "Invalid fields", $errors, 400);
    }

    $stmt = $pdo->prepare("
        INSERT INTO testimonials (quote_text, author_name, author_role, is_visible)
        VALUES (:q, :a, :r, :v)
    ");
    $stmt->execute([
        ":q" => $quoteText,
        ":a" => $authorName,
        ":r" => ($authorRole === "" ? null : $authorRole),
        ":v" => $isVisible ? 1 : 0,
    ]);

    ok(["testimonialId" => (int)$pdo->lastInsertId(), "message" => "Testimonial created"], 201);
}

if ($method === "PUT" && $id !== null) {
    requireAuth();
    $testimonialId = (int)$id;

    // Check exists
    $chk = $pdo->prepare("SELECT id FROM testimonials WHERE id = :id");
    $chk->execute([":id" => $testimonialId]);
    if (!$chk->fetch()) {
        errorResponse("NOT_FOUND", "Testimonial not found", [], 404);
    }

    $fields = [];
    $params = [":id" => $testimonialId];

    $map = [
        "quoteText"  => "quote_text",
        "authorName" => "author_name",
        "authorRole" => "author_role",
        "isVisible"  => "is_visible",
    ];

    foreach ($map as $jsonKey => $dbCol) {
        if (array_key_exists($jsonKey, $body)) {
            $fields[] = "{$dbCol} = :{$jsonKey}";

            if ($jsonKey === "isVisible") {
                $params[":{$jsonKey}"] = ((bool)$body[$jsonKey]) ? 1 : 0;
            } else {
                $val = $body[$jsonKey];
                $params[":{$jsonKey}"] = ($val === "" ? null : $val);
            }
        }
    }

    if (empty($fields)) {
        errorResponse("VALIDATION_ERROR", "No fields provided", ["Provide at least one field to update"], 400);
    }

    $sql = "UPDATE testimonials SET " . implode(", ", $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    ok(["message" => "Testimonial updated"]);
}

if ($method === "DELETE" && $id !== null) {
    requireAuth();
    $testimonialId = (int)$id;

    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = :id");
    $stmt->execute([":id" => $testimonialId]);

    if ($stmt->rowCount() === 0) {
        errorResponse("NOT_FOUND", "Testimonial not found", [], 404);
    }

    ok(["message" => "Testimonial deleted"]);
}

errorResponse("METHOD_NOT_ALLOWED", "Endpoint not supported", [], 405);