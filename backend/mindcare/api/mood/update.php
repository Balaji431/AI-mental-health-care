<?php
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../db.php';

header('Content-Type: application/json');

$userId = AuthMiddleware::authenticate();
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        // Check if user settings exist
        $stmt = $pdo->prepare("SELECT id FROM user_settings WHERE user_id = ?");
        $stmt->execute([$userId]);
        $settings = $stmt->fetch();

        if ($settings) {
            // Update existing settings
            $stmt = $pdo->prepare("
                UPDATE user_settings 
                SET dark_mode = ?, 
                    notifications_enabled = ?, 
                    offline_mode = ?,
                    privacy_consent = ?,
                    updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ")->execute([
                $data['dark_mode'] ?? false,
                $data['notifications_enabled'] ?? true,
                $data['offline_mode'] ?? false,
                $data['privacy_consent'] ?? false,
                $userId
            ]);
        } else {
            // Insert new settings
            $stmt = $pdo->prepare("
                INSERT INTO user_settings 
                (user_id, dark_mode, notifications_enabled, offline_mode, privacy_consent) 
                VALUES (?, ?, ?, ?, ?)
            ")->execute([
                $userId,
                $data['dark_mode'] ?? false,
                $data['notifications_enabled'] ?? true,
                $data['offline_mode'] ?? false,
                $data['privacy_consent'] ?? false
            ]);
        }

        // Return updated settings
        $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
        $stmt->execute([$userId]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $settings
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update settings',
            'debug' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
