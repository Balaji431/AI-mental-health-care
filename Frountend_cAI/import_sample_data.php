<?php
require_once 'config.php';

try {
    // Disable foreign key checks temporarily
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // Clear existing data
    $tables = [
        'user_achievements', 'achievements', 'support_contacts', 'tool_usage', 
        'ai_insights', 'mood_analytics', 'journal_media', 'journal_entries', 
        'user_settings', 'user_goals', 'notification_settings', 'user_activities',
        'crisis_resources', 'users'
    ];
    
    foreach ($tables as $table) {
        try {
            $pdo->exec("TRUNCATE TABLE `$table`");
            echo "Cleared table: $table\n";
        } catch (PDOException $e) {
            echo "Note: Could not clear $table - " . $e->getMessage() . "\n";
        }
    }
    
    // 1. Insert Users
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
    
    $stmt = $pdo->prepare("INSERT INTO users (name, mail, phno, password, age, gender, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute(array_values($user));
    }
    echo "Inserted users\n";
    
    // 2. Insert User Settings
    $settings = [
        [1, 'light', 1, '20:00:00', 'en', date('Y-m-d H:i:s')],
        [2, 'dark', 1, '21:00:00', 'en', date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, theme, notifications_enabled, reminder_time, language, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($settings as $setting) {
        $stmt->execute($setting);
    }
    echo "Inserted user settings\n";
    
    // 3. Insert Journal Entries
    $entries = [
        [1, 'Feeling Good Today', 'Had a great day at work and went for a nice walk in the park.', 'happy', '2023-06-15', date('Y-m-d H:i:s')],
        [1, 'Stressed Out', 'Work is getting really overwhelming. Need to take a break.', 'stressed', '2023-06-16', date('Y-m-d H:i:s')],
        [2, 'New Beginnings', 'Started my new job today. Feeling nervous but excited!', 'excited', '2023-06-15', date('Y-m-d H:i:s')],
        [2, 'Rainy Day', 'Stayed in and read a book. Perfect weather for some self-care.', 'calm', '2023-06-17', date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO journal_entries (user_id, title, content, mood, entry_date, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($entries as $entry) {
        $stmt->execute($entry);
    }
    echo "Inserted journal entries\n";
    
    // 4. Insert Journal Media
    $media = [
        [1, 'image', 'https://example.com/images/park.jpg', 'Beautiful park view', NULL, date('Y-m-d H:i:s')],
        [1, 'audio', 'https://example.com/audio/birds.mp3', 'Birds chirping', NULL, date('Y-m-d H:i:s')],
        [3, 'image', 'https://example.com/images/office.jpg', 'First day at new job', NULL, date('Y-m-d H:i:s')],
        [4, 'image', 'https://example.com/images/rainy_day.jpg', 'View from my window', NULL, date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO journal_media (journal_id, media_type, media_url, caption, transcript, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($media as $item) {
        $stmt->execute($item);
    }
    echo "Inserted journal media\n";
    
    // 5. Insert Mood Analytics
    $moodAnalytics = [
        [1, 'happy', 7.5, 3, '2023-06-15', date('Y-m-d H:i:s')],
        [1, 'anxious', 6.0, 2, '2023-06-16', date('Y-m-d H:i:s')],
        [2, 'excited', 8.5, 2, '2023-06-15', date('Y-m-d H:i:s')],
        [2, 'calm', 7.0, 2, '2023-06-17', date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO mood_analytics (user_id, mood, average_intensity, entry_count, date, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($moodAnalytics as $data) {
        $stmt->execute($data);
    }
    echo "Inserted mood analytics\n";
    
    // 6. Insert AI Insights
    $insights = [
        [1, 'mood_pattern', 'You tend to feel happier on weekends', 0, date('Y-m-d H:i:s')],
        [1, 'activity_impact', 'Exercise seems to improve your mood by 20%', 1, '2023-06-14 10:00:00'],
        [2, 'sleep_quality', 'Better sleep correlates with higher daily mood', 0, date('Y-m-d H:i:s')],
        [2, 'stress_pattern', 'Work-related stress peaks mid-week', 1, '2023-06-13 09:30:00']
    ];
    $stmt = $pdo->prepare("INSERT INTO ai_insights (user_id, insight_type, content, is_read, created_at) VALUES (?, ?, ?, ?, ?)");
    foreach ($insights as $insight) {
        $stmt->execute($insight);
    }
    echo "Inserted AI insights\n";
    
    // 7. Insert Tool Usage
    $toolUsage = [
        [1, 'breathing_exercise', 5, date('Y-m-d H:i:s')],
        [1, 'meditation', 10, '2023-06-16 20:00:00'],
        [2, 'gratitude_journal', 15, '2023-06-15 21:00:00'],
        [2, 'sleep_sounds', 30, '2023-06-17 22:00:00']
    ];
    $stmt = $pdo->prepare("INSERT INTO tool_usage (user_id, tool_name, duration_minutes, created_at) VALUES (?, ?, ?, ?)");
    foreach ($toolUsage as $usage) {
        $stmt->execute($usage);
    }
    echo "Inserted tool usage\n";
    
    // 8. Insert Support Contacts
    $contacts = [
        [1, 'Sarah Johnson', 'Friend', '1112223333', 'sarah@example.com', 1, date('Y-m-d H:i:s')],
        [1, 'Dr. Michael Brown', 'Therapist', '4445556666', 'drbrown@clinic.com', 0, date('Y-m-d H:i:s')],
        [2, 'Alex Taylor', 'Partner', '7778889999', 'alex@example.com', 1, date('Y-m-d H:i:s')],
        [2, 'Dr. Emily Wilson', 'Counselor', '2223334444', 'emily@wellness.com', 0, date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO support_contacts (user_id, name, relationship, phone, email, is_primary, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($contacts as $contact) {
        $stmt->execute($contact);
    }
    echo "Inserted support contacts\n";
    
    // 9. Insert Achievements
    $achievements = [
        ['Early Bird', 'Complete 5 morning check-ins before 8 AM', 'early_bird.png', 50, date('Y-m-d H:i:s')],
        ['Journal Master', 'Write 10 journal entries', 'journal.png', 100, date('Y-m-d H:i:s')],
        ['Mood Tracker', 'Log your mood for 7 consecutive days', 'mood.png', 75, date('Y-m-d H:i:s')],
        ['Self-Care Champion', 'Use self-care tools for 5 days in a row', 'self_care.png', 125, date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO achievements (name, description, icon, points, created_at) VALUES (?, ?, ?, ?, ?)");
    foreach ($achievements as $achievement) {
        $stmt->execute($achievement);
    }
    echo "Inserted achievements\n";
    
    // 10. Insert User Achievements
    $userAchievements = [
        [1, 2, '2023-06-10 15:30:00', date('Y-m-d H:i:s')],
        [1, 3, '2023-06-12 16:45:00', date('Y-m-d H:i:s')],
        [2, 1, '2023-06-11 07:15:00', date('Y-m-d H:i:s')],
        [2, 4, '2023-06-14 20:30:00', date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO user_achievements (user_id, achievement_id, unlocked_at, created_at) VALUES (?, ?, ?, ?)");
    foreach ($userAchievements as $ua) {
        $stmt->execute($ua);
    }
    echo "Inserted user achievements\n";
    
    // 11. Insert Crisis Resources
    $resources = [
        ['National Suicide Prevention Lifeline', 'Free and confidential support for people in distress', '988', 'https://988lifeline.org', '24/7', date('Y-m-d H:i:s')],
        ['Crisis Text Line', 'Text HOME to 741741 for free, 24/7 crisis counseling', '741741', 'https://www.crisistextline.org', '24/7', date('Y-m-d H:i:s')],
        ['Veterans Crisis Line', 'Support for veterans and their loved ones', '988', 'https://www.veteranscrisisline.net', '24/7', date('Y-m-d H:i:s')],
        ['Disaster Distress Helpline', 'Crisis counseling for emotional distress related to disasters', '1-800-985-5990', 'https://www.samhsa.gov/find-help/disaster-distress-helpline', '24/7', date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO crisis_resources (title, description, phone, website, available_hours, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($resources as $resource) {
        $stmt->execute($resource);
    }
    echo "Inserted crisis resources\n";
    
    // 12. Insert User Goals
    $goals = [
        [1, 'journal_entries', 10, 5, '2023-07-15', 0, date('Y-m-d H:i:s')],
        [1, 'mood_checkins', 14, 7, '2023-07-01', 0, date('Y-m-d H:i:s')],
        [2, 'meditation_minutes', 300, 150, '2023-07-10', 0, date('Y-m-d H:i:s')],
        [2, 'gratitude_entries', 21, 10, '2023-07-20', 0, date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO user_goals (user_id, goal_type, target_value, current_value, target_date, is_completed, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($goals as $goal) {
        $stmt->execute($goal);
    }
    echo "Inserted user goals\n";
    
    // 13. Insert Notification Settings
    $notifications = [
        [1, 1, 1, 0, 1, 1, date('Y-m-d H:i:s')],
        [2, 1, 1, 1, 1, 0, date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO notification_settings (user_id, email_notifications, push_notifications, sms_notifications, daily_reminder, weekly_summary, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($notifications as $notification) {
        $stmt->execute($notification);
    }
    echo "Inserted notification settings\n";
    
    // 14. Insert User Activities
    $activities = [
        [1, 'exercise', 30, 'Morning jog', '2023-06-15 07:00:00', date('Y-m-d H:i:s')],
        [1, 'meditation', 15, 'Evening session', '2023-06-16 20:30:00', date('Y-m-d H:i:s')],
        [2, 'yoga', 45, 'Online class', '2023-06-15 18:00:00', date('Y-m-d H:i:s')],
        [2, 'reading', 60, 'Self-help book', '2023-06-17 21:00:00', date('Y-m-d H:i:s')]
    ];
    $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, activity_type, duration_minutes, notes, activity_date, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($activities as $activity) {
        $stmt->execute($activity);
    }
    echo "Inserted user activities\n";
    
    // Re-enable foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    echo "\n✅ Sample data import completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "Transaction rolled back\n";
    }
}
