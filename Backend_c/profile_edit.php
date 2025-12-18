<?php
// profile_edit.php
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

if ($mail === '' || $password === '') {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Email and password required']);
    exit;
}

// fields allowed to change
$name = isset($input['name']) ? trim($input['name']) : null;
$phno = isset($input['phno']) ? trim($input['phno']) : null;
$description = array_key_exists('description', $input) ? trim($input['description']) : null;

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
    $userId = $u['id'];

    // build dynamic update
    $fields = [];
    $params = [':id' => $userId];
    if ($name !== null) { $fields[] = 'name = :name'; $params[':name'] = $name; }
    if ($phno !== null) { $fields[] = 'phno = :phno'; $params[':phno'] = $phno; }
    if ($description !== null) { $fields[] = 'description = :description'; $params[':description'] = $description; }

    if (empty($fields)) {
        echo json_encode(['success'=>false,'message'=>'No fields to update']);
        exit;
    }

    $sql = 'UPDATE users SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = :id';
    $up = $pdo->prepare($sql);
    $up->execute($params);

    echo json_encode(['success'=>true,'message'=>'Profile updated']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    error_log('profile_edit error: '.$e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Server error']);
    exit;
}
