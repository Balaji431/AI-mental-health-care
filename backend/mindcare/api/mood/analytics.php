<?php
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../db.php';

header('Content-Type: application/json');

$userId = AuthMiddleware::authenticate();

// Get date range from query parameters (default to last 30 days)
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

try {
    // Get mood data for the date range
    $stmt = $pdo->prepare("
        SELECT date, avg_mood, mood_notes 
        FROM mood_analytics 
        WHERE user_id = ? AND date BETWEEN ? AND ?
        ORDER BY date ASC
    ");
    $stmt->execute([$userId, $startDate, $endDate]);
    $moodData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate statistics
    $stats = [
        'total_entries' => count($moodData),
        'average_mood' => 0,
        'highest_mood' => null,
        'lowest_mood' => null,
        'trend' => 'stable' // stable, improving, declining
    ];

    if (!empty($moodData)) {
        $moodValues = array_column($moodData, 'avg_mood');
        $stats['average_mood'] = array_sum($moodValues) / count($moodValues);
        $stats['highest_mood'] = max($moodValues);
        $stats['lowest_mood'] = min($moodValues);

        // Simple trend analysis (comparing first and last 3 days if available)
        if (count($moodData) >= 6) {
            $firstPeriod = array_slice($moodValues, 0, 3);
            $lastPeriod = array_slice($moodValues, -3);
            $firstAvg = array_sum($firstPeriod) / count($firstPeriod);
            $lastAvg = array_sum($lastPeriod) / count($lastPeriod);
            
            if ($lastAvg > $firstAvg + 0.5) {
                $stats['trend'] = 'improving';
            } elseif ($lastAvg < $firstAvg - 0.5) {
                $stats['trend'] = 'declining';
            }
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'mood_data' => $moodData,
            'statistics' => $stats,
            'date_range' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch mood analytics',
        'debug' => $e->getMessage()
    ]);
}
