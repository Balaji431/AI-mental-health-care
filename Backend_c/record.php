<?php
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../db.php';

header('Content-Type: application/json');

$userId = AuthMiddleware::authenticate();
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        if (!isset($data['mood_score']) || !is_numeric($data['mood_score'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid mood_score is required']);
            exit();
        }

        $date = $data['date'] ?? date('Y-m-d');
        $moodScore = (float) $data['mood_score'];
        $notes = $data['notes'] ?? '';

        // Check if mood entry already exists for this date
        $stmt = $pdo->prepare("SELECT id FROM mood_analytics WHERE user_id = ? AND date = ?");
        $stmt->execute([$userId, $date]);
        $existingEntry = $stmt->fetch();

        if ($existingEntry) {
            // Update existing entry
            $stmt = $pdo->prepare("
                UPDATE mood_analytics 
                SET avg_mood = ?, mood_notes = ? 
                WHERE id = ? AND user_id = ?
            ")->execute([
                $moodScore,
                $notes,
                $existingEntry['id'],
                $userId
            ]);
            $message = 'Mood updated successfully';
        } else {
            // Insert new entry
            $stmt = $pdo->prepare("
                INSERT INTO mood_analytics 
                (user_id, date, avg_mood, mood_notes) 
                VALUES (?, ?, ?, ?)
            ")->execute([
                $userId,
                $date,
                $moodScore,
                $notes
            ]);
            $message = 'Mood recorded successfully';
        }

        // Return the created/updated mood entry
        $stmt = $pdo->prepare("SELECT * FROM mood_analytics WHERE user_id = ? AND date = ?");
        $stmt->execute([$userId, $date]);
        $moodEntry = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $moodEntry
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to record mood',
            'debug' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
