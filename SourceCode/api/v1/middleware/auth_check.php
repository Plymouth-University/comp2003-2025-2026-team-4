<?php
declare(strict_types=1);

require_once __DIR__ . "/../helpers/response.php";

function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function jwtEncode(array $payload, string $secret): string {
    $header = ["alg" => "HS256", "typ" => "JWT"];

    $segments = [
        base64UrlEncode(json_encode($header)),
        base64UrlEncode(json_encode($payload)),
    ];

    $signingInput = implode(".", $segments);
    $signature = hash_hmac("sha256", $signingInput, $secret, true);

    $segments[] = base64UrlEncode($signature);
    return implode(".", $segments);
}

function jwtDecode(string $jwt, string $secret): ?array {
    $parts = explode(".", $jwt);
    if (count($parts) !== 3) return null;

    [$h, $p, $s] = $parts;

    $signingInput = $h . "." . $p;
    $expected = base64UrlEncode(hash_hmac("sha256", $signingInput, $secret, true));
    if (!hash_equals($expected, $s)) return null;

    $payload = json_decode(base64UrlDecode($p), true);
    if (!is_array($payload)) return null;

    if (isset($payload["exp"]) && time() >= (int)$payload["exp"]) return null;

    return $payload;
}

function requireAuth(): array {
    $cfg = require __DIR__ . "/../config/config.php";

    $auth = $_SERVER["HTTP_AUTHORIZATION"] ?? "";
    if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
        errorResponse("UNAUTHORISED", "Missing token", [], 401);
    }

    $payload = jwtDecode($matches[1], $cfg["jwt_secret"]);
    if ($payload === null) {
        errorResponse("UNAUTHORISED", "Invalid token", [], 401);
    }

    return $payload;
}