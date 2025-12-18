<?php
header('Content-Type: application/json');
require_once 'db.php';

/* Allow only POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'POST method required'
    ]);
    exit;
}

/* Read JSON input */
$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON'
    ]);
    exit;
}

/* Extract fields */
$journal_id = isset($data['journal_id']) ? (int)$data['journal_id'] : null;
$media_type = trim($data['media_type'] ?? '');
$media_url  = trim($data['media_url'] ?? '');
$caption    = $data['caption'] ?? null;
$transcript = $data['transcript'] ?? null;

/* Validate required fields */
if (!$journal_id || $media_type === '' || $media_url === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing fields'
    ]);
    exit;
}

/* Optional safety check: journal entry must exist */
$check = $pdo->prepare("SELECT id FROM journal_entries WHERE id = ?");
$check->execute([$journal_id]);

if (!$check->fetch()) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Journal entry not found'
    ]);
    exit;
}

/* Insert media */
$stmt = $pdo->prepare("
    INSERT INTO journal_media 
    (journal_id, media_type, media_url, caption, transcript)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $journal_id,
    $media_type,
    $media_url,
    $caption,
    $transcript
]);

echo json_encode([
    'success' => true,
    'message' => 'Media added successfully'
]);
