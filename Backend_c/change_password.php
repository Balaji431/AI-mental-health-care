<?php
// change_password.php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid JSON']); exit; }

$mail = trim($input['mail'] ?? '');
$current = $input['current_password'] ?? '';
$new = $input['new_password'] ?? '';

if ($mail === '' || $current === '' || $new === '') {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'mail, current_password and new_password are required']);
    exit;
}
if (strlen($new) < 6) {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'New password must be at least 6 characters']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, password FROM users WHERE mail = :mail LIMIT 1');
    $stmt->execute([':mail' => $mail]);
    $u = $stmt->fetch();
    if (!$u || !password_verify($current, $u['password'])) {
        http_response_code(401);
        echo json_encode(['success'=>false,'message'=>'Invalid credentials']);
        exit;
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE users SET password = :pwd, updated_at = NOW() WHERE id = :id');
    $upd->execute([':pwd' => $hash, ':id' => $u['id']]);

    echo json_encode(['success'=>true,'message'=>'Password changed']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    error_log('change_password error: '.$e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Server error']);
    exit;
}
