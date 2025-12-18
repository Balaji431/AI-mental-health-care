<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'POST only']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? null;
$title   = $data['title'] ?? null;
$content = $data['content'] ?? null;
$mood    = $data['mood'] ?? null;

if (!$user_id || !$content) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO journal_entries (user_id, title, content, mood, entry_date)
    VALUES (?, ?, ?, ?, CURDATE())
");

$stmt->execute([$user_id, $title, $content, $mood]);

echo json_encode([
    'success' => true,
    'journal_id' => $pdo->lastInsertId()
]);
