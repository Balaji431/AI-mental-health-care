<?php
require_once 'db.php';
header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "User ID required"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        u.name,
        ups.joined_date,
        ups.current_streak,
        ups.total_checkins,
        ups.tools_used,
        ups.goals_achieved
    FROM users u
    JOIN user_profile_stats ups ON u.id = ups.user_id
    WHERE u.id = ?
");

$stmt->execute([$user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    echo json_encode(["success" => true, "profile" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "Profile not found"]);
}
