<?php
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Add CORS headers for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $userId = AuthMiddleware::authenticate();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Authentication failed',
        'debug' => [
            'message' => $e->getMessage()
        ]
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get recent mood data for analysis (last 7 days)
        try {
            $query = "
                SELECT date, avg_mood, mood_notes 
                FROM mood_analytics 
                WHERE user_id = ? AND date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
                ORDER BY date ASC
            ";
            
            $stmt = $pdo->prepare($query);
            if (!$stmt) {
                throw new Exception('Failed to prepare mood data query: ' . implode(' ', $pdo->errorInfo()));
            }
            
            if (!$stmt->execute([$userId])) {
                throw new Exception('Failed to execute mood data query: ' . implode(' ', $stmt->errorInfo()));
            }
            
            $recentMoods = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log the number of mood entries found
            error_log('Found ' . count($recentMoods) . ' mood entries for user ' . $userId);
            
        } catch (PDOException $e) {
            throw new Exception('Database error while fetching mood data: ' . $e->getMessage());
        }

        // Get recent tool usage
        try {
            $query = "
                SELECT tool_name, tool_type, COUNT(*) as usage_count, AVG(duration_seconds) as avg_duration
                FROM tool_usage 
                WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY tool_name, tool_type
            ";
            
            $stmt = $pdo->prepare($query);
            if (!$stmt) {
                throw new Exception('Failed to prepare tool usage query: ' . implode(' ', $pdo->errorInfo()));
            }
            
            if (!$stmt->execute([$userId])) {
                throw new Exception('Failed to execute tool usage query: ' . implode(' ', $stmt->errorInfo()));
            }
            
            $toolUsage = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log the number of tool usage entries found
            error_log('Found ' . count($toolUsage) . ' tool usage entries for user ' . $userId);
            
        } catch (PDOException $e) {
            throw new Exception('Database error while fetching tool usage: ' . $e->getMessage());
        }

        // Generate insights based on the data
        $insights = [];

        // Mood trend insight
        if (count($recentMoods) >= 3) {
            $firstMood = $recentMoods[0]['avg_mood'];
            $lastMood = end($recentMoods)['avg_mood'];
            $moodDiff = $lastMood - $firstMood;

            if (abs($moodDiff) > 1.5) {
                $trend = $moodDiff > 0 ? 'improved' : 'declined';
                $insights[] = [
                    'type' => 'mood_pattern',
                    'title' => 'Mood Trend',
                    'description' => "Your mood has {$trend} significantly over the past week.",
                    'data' => [
                        'start_mood' => $firstMood,
                        'end_mood' => $lastMood,
                        'days_analyzed' => count($recentMoods)
                    ]
                ];
            }
        }

        // Tool usage insight
        if (!empty($toolUsage)) {
            $mostUsedTool = array_reduce($toolUsage, function($carry, $item) {
                return ($carry === null || $item['usage_count'] > $carry['usage_count']) ? $item : $carry;
            });

            $insights[] = [
                'type' => 'activity_correlation',
                'title' => 'Most Used Tool',
                'description' => "Your most used coping tool is '{$mostUsedTool['tool_name']}' with an average duration of " . 
                               round($mostUsedTool['avg_duration'] / 60, 1) . " minutes per session.",
                'data' => $mostUsedTool
            ];
        }

        // Save insights to database
        $savedInsights = [];
        foreach ($insights as $insight) {
            $stmt = $pdo->prepare("
                INSERT INTO ai_insights 
                (user_id, insight_type, title, description, data) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $insight['type'],
                $insight['title'],
                $insight['description'],
                json_encode($insight['data'] ?? null)
            ]);
            
            $insight['id'] = $pdo->lastInsertId();
            $savedInsights[] = $insight;
        }

        echo json_encode([
            'success' => true,
            'message' => count($savedInsights) . ' new insights generated',
            'data' => $savedInsights
        ]);

    } catch (Exception $e) {
        error_log('Insight Generation Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to generate insights',
            'debug' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
