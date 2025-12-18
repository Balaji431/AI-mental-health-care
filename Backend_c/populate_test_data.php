<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

try {
    // Start transaction
    $pdo->beginTransaction();

    echo "Starting to populate test data...\n\n";

    // 1. Create test user if not exists
    $email = 'test@example.com';
    $password = password_hash('test123', PASSWORD_BCRYPT);
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE mail = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Create test user
        $stmt = $pdo->prepare("
            INSERT INTO users (name, mail, password, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute(['Test User', $email, $password]);
        $userId = $pdo->lastInsertId();
        echo "✅ Created test user with ID: $userId\n";
    } else {
        $userId = $user['id'];
        echo "ℹ️ Using existing test user ID: $userId\n";
    }

    // 2. Clear existing test data
    $tables = ['mood_analytics', 'tool_usage', 'ai_insights'];
    foreach ($tables as $table) {
        $pdo->exec("DELETE FROM $table WHERE user_id = $userId");
        echo "🧹 Cleared existing data from $table\n";
    }

    // 3. Add mood analytics (last 7 days)
    $moodNotes = [
        "Feeling great today!",
        "Had a rough day at work",
        "Good day overall",
        "Not feeling my best",
        "Really happy with my progress",
        "Stressed about upcoming deadlines",
        "Peaceful and relaxed"
    ];

    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $mood = rand(3, 10); // Random mood between 3-10
        $note = $moodNotes[array_rand($moodNotes)];
        
        $stmt = $pdo->prepare("
            INSERT INTO mood_analytics 
            (user_id, date, avg_mood, mood_notes, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $date, $mood, $note]);
    }
    echo "✅ Added 7 days of mood data\n";

    // 4. Add tool usage data
    $tools = [
        ['Breathing Exercise', 'relaxation', 300],
        ['Meditation', 'mindfulness', 600],
        ['Gratitude Journal', 'journaling', 900],
        ['Mood Tracker', 'tracking', 180],
        ['Sleep Sounds', 'relaxation', 1200],
        ['Guided Meditation', 'mindfulness', 1500],
        ['Daily Reflection', 'journaling', 480]
    ];

    foreach ($tools as $tool) {
        $usageCount = rand(1, 5);
        for ($i = 0; $i < $usageCount; $i++) {
            $date = date('Y-m-d H:i:s', strtotime("-$i days"));
            $duration = $tool[2] * (0.8 + (mt_rand(0, 40) / 100)); // Randomize duration ±20%
            
            $stmt = $pdo->prepare("
                INSERT INTO tool_usage 
                (user_id, tool_name, tool_type, duration_seconds, created_at)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $tool[0], $tool[1], $duration, $date]);
        }
    }
    echo "✅ Added tool usage data\n";

    // 5. Clear any existing insights
    $pdo->exec("DELETE FROM ai_insights WHERE user_id = $userId");
    echo "🧹 Cleared existing insights\n";

    // Commit the transaction
    $pdo->commit();

    echo "\n✅ Test data population complete!\n";
    echo "You can now test the insights generation at: ";
    echo "http://localhost/mindcare/api/insights/generate\n";
    echo "Use the following credentials to log in:\n";
    echo "Email: test@example.com\n";
    echo "Password: test123\n";

} catch (Exception $e) {
    // Rollback the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
