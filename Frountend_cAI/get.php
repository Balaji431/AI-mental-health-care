<?php
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../db.php';

header('Content-Type: application/json');

$userId = AuthMiddleware::authenticate();

try {
    $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
    $stmt->execute([$userId]);
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no settings exist, return defaults
    if (!$settings) {
        $settings = [
            'dark_mode' => false,
            'notifications_enabled' => true,
            'offline_mode' => false,
            'privacy_consent' => false
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $settings
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch settings',
        'debug' => $e->getMessage()
    ]);
}
