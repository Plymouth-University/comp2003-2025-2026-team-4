<?php

declare(strict_types=1);

// Handle OPTIONS preflight request (browsers send before POST/PUT/DELETE)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/helpers/response.php';

// ─── Router ───────────────────────────────────────────────────────────────────

// Get clean URI and strip base path /saltypadel/api/v1/
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = preg_replace('#^/saltypadel/api/v1/#', '', $uri);
$uri = rtrim($uri, '/');

// Route to correct file
match($uri) {
    'health'       => require __DIR__ . '/test.php',
    'auth'         => require __DIR__ . '/routes/auth.php',
    'events'       => require __DIR__ . '/routes/upcoming_events.php',
    'events/past'  => require __DIR__ . '/routes/past_events.php',
    'partners'     => require __DIR__ . '/routes/partners.php',
    'testimonials' => require __DIR__ . '/routes/testimonials.php',
    'settings'     => require __DIR__ . '/routes/settings.php',
    default        => errorResponse('NOT_FOUND', 'Endpoint not found', [], 404)
};