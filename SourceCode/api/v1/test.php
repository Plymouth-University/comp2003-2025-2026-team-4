<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config/db.php';

try {
    $pdo = getDB();
    echo json_encode([
        'success' => true,
        'data' => [
            'status' => 'ok',
            'message' => 'SaltyPadel API is running',
            'database' => 'connected',
            'version' => 'v1.0'
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 'DB_CONNECTION_FAILED',
            'message' => 'Database not connected',
            'details' => []
        ]
    ]);
}
