<?php
// login.php
// Place this file in: C:\xampp\htdocs\mindcare\login.php
// db.php must be in the same folder

require_once __DIR__ . '/db.php';

/* ===============================
   HEADERS & CORS
   =============================== */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

/* ===============================
   HANDLE PREFLIGHT
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/* ===============================
   ALLOW ONLY POST
   =============================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

/* ===============================
   READ JSON INPUT
   =============================== */
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON'
    ]);
    exit;
}

$mail = trim($data['mail'] ?? '');
$password = $data['password'] ?? '';

if ($mail === '' || $password === '') {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required.'
    ]);
    exit;
}

/* ===============================
   LOGIN LOGIC (PLAIN PASSWORD)
   =============================== */
try {
    $stmt = $pdo->prepare(
        "SELECT id, name, mail, phno, password, age, gender
         FROM users
         WHERE mail = :mail
         LIMIT 1"
    );
    $stmt->execute([':mail' => $mail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid credentials.'
        ]);
        exit;
    }

    // ⚠️ PLAIN TEXT PASSWORD COMPARISON
    if ($password !== $user['password']) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid credentials.'
        ]);
        exit;
    }

    // Remove password before response
    unset($user['password']);
    $user['id'] = (int)$user['id'];

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => $user
    ]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    error_log('login.php error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
    exit;
}
