<?php
// update_email.php
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
$password = $input['password'] ?? '';
$new_mail = trim($input['new_mail'] ?? '');

if ($mail === '' || $password === '' || $new_mail === '') {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'mail, password and new_mail required']);
    exit;
}
if (!filter_var($new_mail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'new_mail must be a valid email']);
    exit;
}

try {
    // authenticate
    $stmt = $pdo->prepare('SELECT id, password FROM users WHERE mail = :mail LIMIT 1');
    $stmt->execute([':mail' => $mail]);
    $u = $stmt->fetch();
    if (!$u || !password_verify($password, $u['password'])) {
        http_response_code(401);
        echo json_encode(['success'=>false,'message'=>'Invalid credentials']);
        exit;
    }

    // check new_mail uniqueness
    $chk = $pdo->prepare('SELECT id FROM users WHERE mail = :new_mail LIMIT 1');
    $chk->execute([':new_mail' => $new_mail]);
    if ($chk->fetch()) {
        http_response_code(409);
        echo json_encode(['success'=>false,'message'=>'Email already in use']);
        exit;
    }

    $upd = $pdo->prepare('UPDATE users SET mail = :new_mail, updated_at = NOW() WHERE id = :id');
    $upd->execute([':new_mail' => $new_mail, ':id' => $u['id']]);

    echo json_encode(['success'=>true,'message'=>'Email updated']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    error_log('update_email error: '.$e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Server error']);
    exit;
}
