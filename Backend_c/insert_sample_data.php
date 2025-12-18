<?php
require_once 'db.php';

// Helper function to generate random date within a range
function randomDate($start_date, $end_date) {
    $min = strtotime($start_date);
    $max = strtotime($end_date);
    $random_time = mt_rand($min, $max);
    return date('Y-m-d', $random_time);
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // 1. Insert Sample Users
    $users = [
        [
            'name' => 'John Doe',
            'mail' => 'john@example.com',
            'phno' => '1234567890',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'age' => 30,
            'gender' => 'Male',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Jane Smith',
            'mail' => 'jane@example.com',
            'phno' => '0987654321',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'age' => 28,
            'gender' => 'Female',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];

    $userIds = [];
    foreach ($users as $user) {
        $stmt = $pdo->prepare("INSERT INTO users (name, mail, phno, password, age, gender, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array_values($user));
        $userIds[] = $pdo->lastInsertId();
    }
    
    // 2. Insert Mood Analytics
    $moodEntries = [];
    foreach ($userIds as $userId) {
        for ($i = 0; $i < 5; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $moodScore = rand(1, 10);
            $moodEntries[] = [
                'user_id' => $userId,
                'date' => $date,
                'avg_mood' => $moodScore,
                'mood_notes' => "Felt " . ($moodScore > 5 ? 'good' : 'not so good') . " today.",
                'created_at' => $date . ' ' . date('H:i:s')
            ];
        }
    }

    $moodIds = [];
    foreach ($moodEntries as $entry) {
        $stmt = $pdo->prepare("INSERT INTO mood_analytics (user_id, date, avg_mood, mood_notes, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(array_values($entry));
        $moodIds[] = $pdo->lastInsertId();
    }

    // 3. Insert Journal Entries
    $journalEntries = [];
    $titles = ["My Day", "Reflections", "Daily Thoughts", "Gratitude Journal", "Mindful Moments"];
    
    foreach ($userIds as $userId) {
        for ($i = 0; $i < 3; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $journalEntries[] = [
                'user_id' => $userId,
                'title' => $titles[array_rand($titles)],
                'content' => "This is a sample journal entry for " . date('F j, Y', strtotime($date)) . ". I'm feeling " . (rand(0, 1) ? 'happy' : 'thoughtful') . " today.",
                'created_at' => $date . ' ' . date('H:i:s')
            ];
        }
    }

    $journalIds = [];
    foreach ($journalEntries as $entry) {
        $stmt = $pdo->prepare("INSERT INTO journal_entries (user_id, title, content, created_at) VALUES (?, ?, ?, ?)");
        $stmt->execute(array_values($entry));
        $journalIds[] = $pdo->lastInsertId();
    }

    // 4. Insert User Settings
    foreach ($userIds as $userId) {
        $settings = [
            'user_id' => $userId,
            'dark_mode' => rand(0, 1),
            'notifications_enabled' => 1,
            'offline_mode' => 0,
            'privacy_consent' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, dark_mode, notifications_enabled, offline_mode, privacy_consent, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(array_values($settings));
    }

    // 5. Insert AI Insights
    $insights = [
        "Your mood has been improving over the last week. Keep it up!",
        "You tend to feel better on weekdays than weekends. Consider planning relaxing activities for the weekend.",
        "Your journal entries show a positive trend. You might want to reflect on what's working well.",
        "You've been consistent with your mood tracking. Try adding more details to your journal entries for better insights."
    ];

    foreach ($userIds as $userId) {
        for ($i = 0; $i < 2; $i++) {
            $insight = [
                'user_id' => $userId,
                'insight_text' => $insights[array_rand($insights)],
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime("-$i days"))
            ];
            $stmt = $pdo->prepare("INSERT INTO ai_insights (user_id, insight_text, is_read, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute(array_values($insight));
        }
    }

    // Commit the transaction
    $pdo->commit();
    
    echo "Sample data inserted successfully!\n";
    echo "User IDs created: " . implode(', ', $userIds) . "\n";
    
} catch (Exception $e) {
    // Rollback the transaction if something went wrong
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}

// Function to get a random element from an array
function getRandomElement($array) {
    return $array[array_rand($array)];
}
?>
