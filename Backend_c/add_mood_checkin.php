<?php
// add_mood_checkin.php
require_once 'db.php';

// CORS (for testing)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON body'
    ]);
    exit;
}

// Get inputs
$user_id    = isset($input['user_id']) ? (int)$input['user_id'] : 0;
$mood_score = isset($input['mood_score']) ? (int)$input['mood_score'] : null;
$intensity  = isset($input['intensity']) ? (int)$input['intensity'] : null;
$emotions   = $input['emotions'] ?? null; // array
$note_text  = trim($input['note_text'] ?? '');

// Validation
$errors = [];

if ($user_id <= 0) $errors[] = 'Invalid user_id';
if ($mood_score === null || $mood_score < 1 || $mood_score > 5) {
    $errors[] = 'mood_score must be between 1 and 5';
}
if ($intensity !== null && ($intensity < 1 || $intensity > 10)) {
    $errors[] = 'intensity must be between 1 and 10';
}
if ($emotions !== null && !is_array($emotions)) {
    $errors[] = 'emotions must be an array';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'errors' => $errors
    ]);
    exit;
}

// Convert emotions array to JSON
$emotions_json = $emotions ? json_encode(array_values($emotions)) : null;

try {
    // Insert check-in
    $stmt = $pdo->prepare("
        INSERT INTO mood_checkins
        (user_id, mood_score, intensity, emotions, note_text)
        VALUES
        (:user_id, :mood_score, :intensity, :emotions, :note_text)
    ");

    $stmt->execute([
        ':user_id'    => $user_id,
        ':mood_score' => $mood_score,
        ':intensity'  => $intensity,
        ':emotions'   => $emotions_json,
        ':note_text'  => $note_text ?: null
    ]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Mood check-in saved successfully'
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
    exit;
}
