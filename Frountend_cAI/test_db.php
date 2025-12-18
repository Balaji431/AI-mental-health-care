<?php
// test_db.php — checks config.php and PDO

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/config.php';

    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception('PDO instance not available from config.php');
    }

    // Simple test query
    $stmt = $pdo->query('SELECT 1 AS ok');
    $row = $stmt->fetch();

    if ($row && isset($row['ok'])) {
        echo json_encode([
            'success' => true,
            'message' => 'DB connected successfully',
            'result'  => $row
        ]);
    } else {
        throw new Exception('DB responded but query failed');
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'DB connection failed',
        'error'   => $e->getMessage()
    ]);
}
