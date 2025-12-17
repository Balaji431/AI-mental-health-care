<?php
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../db.php';

header('Content-Type: application/json');

$userId = AuthMiddleware::authenticate();

// Optional query parameters
$type = $_GET['type'] ?? null; // mood_pattern, activity_correlation, recommendation
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] === 'true';

try {
    $query = "SELECT * FROM ai_insights WHERE user_id = ?";
    $params = [$userId];

    if ($type) {
        $query .= " AND insight_type = ?";
        $params[] = $type;
    }

    if ($unreadOnly) {
        $query .= " AND is_read = 0";
    }

    $query .= " ORDER BY created_at DESC LIMIT ?";
    $params[] = $limit;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $insights = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark insights as read if they were fetched as unread
    if ($unreadOnly && !empty($insights)) {
        $insightIds = array_column($insights, 'id');
        $placeholders = rtrim(str_repeat('?,', count($insightIds)), ',');
        
        $stmt = $pdo->prepare("UPDATE ai_insights SET is_read = 1 WHERE id IN ($placeholders)");
        $stmt->execute($insightIds);
    }

    echo json_encode([
        'success' => true,
        'data' => $insights
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch AI insights',
        'debug' => $e->getMessage()
    ]);
}
