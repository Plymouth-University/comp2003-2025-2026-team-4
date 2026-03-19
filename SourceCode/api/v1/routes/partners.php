<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// ── GET ── Return all partners
if ($method === 'GET') {
    $pdo = getDB();
    $stmt = $pdo->query(
        'SELECT
            id AS partnerId,
            partner_name AS partnerName,
            logo_path AS logoPath,
            website_url AS websiteUrl
        FROM partners
        ORDER BY id ASC'
    );
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $partners
    ]);
    exit;
}
// ── Protect everything  ──
require_auth();

// ── POST ── Add new partner
if ($method === 'POST') {
    $partnerName = $input['partnerName'] ?? '';
    $logoPath    = $input['logoPath'] ?? null;
    $websiteUrl  = $input['websiteUrl'] ?? null;

    if (empty($partnerName)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code'    => 'VALIDATION_ERROR',
                'message' => 'partnerName is required',
                'details' => []
            ]
        ]);
        exit;
    }

    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO partners (partner_name, logo_path, website_url)
        VALUES (?, ?, ?)'
    );
    $stmt->execute([$partnerName, $logoPath, $websiteUrl]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data'    => ['partnerId' => $pdo->lastInsertId(), 'message' => 'Partner added']
    ]);
    exit;
}

// ── PUT ── Update partner logo path
if ($method === 'PUT') {
    $partnerId = $_GET['id'] ?? null;

    if (!$partnerId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Partner ID is required',
                'details' => []
            ]
        ]);
        exit;
    }

    $logoPath = $input['logoPath'] ?? null;

    if (empty($logoPath)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'logoPath is required',
                'details' => []
            ]
        ]);
        exit;
    }

    $pdo = getDB();

    // Check partner exists
    $check = $pdo->prepare('SELECT id FROM partners WHERE id = ?');
    $check->execute([$partnerId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'NOT_FOUND',
                'message' => 'Partner not found',
                'details' => []
            ]
        ]);
        exit;
    }

    $stmt = $pdo->prepare(
        'UPDATE partners SET logo_path = ? WHERE id = ?'
    );
    $stmt->execute([$logoPath, $partnerId]);

    echo json_encode([
        'success' => true,
        'data' => ['logoPath' => $logoPath]
    ]);
    exit;
}

// Method not allowed 
http_response_code(405);
echo json_encode([
    'success' => false,
    'error' => [
        'code' => 'METHOD_NOT_ALLOWED',
        'message' => 'Method not allowed',
        'details' => []
    ]
]);