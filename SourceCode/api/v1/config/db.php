<?php
function getDB() {
    $host = 'db5019942979.hosting-data.io';
    $dbname = 'dbs15399296';
    $username = 'dbu4083026';
    $password = 'Saltypadel123!';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $username,
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'DB_CONNECTION_FAILED',
                'message' => 'Database connection failed',
                'details' => []
            ]
        ]);
        exit;
    }
}