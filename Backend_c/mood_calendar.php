<?php
header('Content-Type: application/json');
require_once 'db.php';

$user_id = $_GET['user_id'] ?? null;

$stmt = $pdo->prepare("
  SELECT entry_date, mood
  FROM journal_entries
  WHERE user_id = ?
");

$stmt->execute([$user_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  'success' => true,
  'calendar' => $data
]);
