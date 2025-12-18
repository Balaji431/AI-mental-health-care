<?php
header('Content-Type: application/json');
require_once 'db.php';

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'user_id required'
    ]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, title, content, mood, entry_date, created_at
    FROM journal_entries
    WHERE user_id = ?
    ORDER BY entry_date DESC
");

$stmt->execute([$user_id]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $entries
]);
