<?php
require_once 'db.php';

try {
    // Start transaction
    $pdo->beginTransaction();

    // Clear existing data (be careful with this in production!)
    $tables = [
        'user_achievements', 'achievements', 'support_contacts', 'tool_usage',
        'ai_insights', 'mood_analytics', 'journal_media', 'journal_entries',
        'user_settings', 'user_goals', 'notification_settings', 'user_activities',
        'users'
    ];

    foreach ($tables as $table) {
        try {
            $pdo->exec("TRUNCATE TABLE `$table`");
            echo "Cleared table: $table\n";
        } catch (PDOException $e) {
            echo "Error clearing $table: " . $e->getMessage() . "\n";
        }
    }

    // 1. Add Users
    $users = [
        [
            'name' => 'Alex Johnson',
            'email' => 'alex@example.com',
            'phno' => '1234567890',
            'password' => password_hash('alex123', PASSWORD_BCRYPT),
            'age' => 28,
            'gender' => 'Male'
        ],
        [
            'name' => 'Sarah Williams',
            'email' => 'sarah@example.com',
            'phno' => '2345678901',
            'password' => password_hash('sarah123', PASSWORD_BCRYPT),
            'age' => 32,
            'gender' => 'Female'
        ],
        [
            'name' => 'Michael Brown',
            'email' => 'michael@example.com',
            'phno' => '3456789012',
            'password' => password_hash('michael123', PASSWORD_BCRYPT),
            'age' => 25,
            'gender' => 'Male'
        ],
        [
            'name' => 'Emily Davis',
            'email' => 'emily@example.com',
            'phno' => '4567890123',
            'password' => password_hash('emily123', PASSWORD_BCRYPT),
            'age' => 30,
            'gender' => 'Female'
        ],
        [
            'name' => 'David Wilson',
            'email' => 'david@example.com',
            'phno' => '5678901234',
            'password' => password_hash('david123', PASSWORD_BCRYPT),
            'age' => 35,
            'gender' => 'Male'
        ]
    ];

    $userIds = [];
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phno, password, age, gender) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($users as $user) {
        $stmt->execute([$user['name'], $user['email'], $user['phno'], $user['password'], $user['age'], $user['gender']]);
        $userIds[] = $pdo->lastInsertId();
        echo "Added user: {$user['name']}\n";
    }

    // 2. Add User Settings
    $themes = ['light', 'dark', 'system'];
    $languages = ['en', 'es', 'fr', 'de', 'hi'];
    $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, theme, notifications_enabled, reminder_time, language) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($userIds as $userId) {
        $theme = $themes[array_rand($themes)];
        $language = $languages[array_rand($languages)];
        $reminderTime = rand(6, 22) . ':' . (rand(0, 1) ? '00' : '30') . ':00';
        $stmt->execute([$userId, $theme, rand(0, 1), $reminderTime, $language]);
    }
    echo "Added user settings\n";

    // 3. Add Journal Entries
    $moods = ['happy', 'sad', 'excited', 'calm', 'anxious', 'grateful', 'energetic', 'tired'];
    $titles = [
        'A Great Day', 'Feeling Down', 'New Beginnings', 'Daily Reflection', 'Weekend Vibes',
        'Work Stress', 'Family Time', 'Personal Growth', 'Health Journey', 'Random Thoughts'
    ];
    
    $contents = [
        'Today was an amazing day! I felt so productive and happy with my progress.',
        'Feeling a bit low today. Not sure why, but I know this too shall pass.',
        'Started a new project and I\'m really excited about the possibilities!',
        'Took some time to reflect on my goals and what I want to achieve this year.',
        'Spent the weekend recharging and it was exactly what I needed.',
        'Work has been really stressful lately. Need to find better ways to cope.',
        'Had a wonderful time with family today. These moments are precious.',
        'Focused on personal growth today. Read a book and learned something new.',
        'Started my health journey. Small steps towards a better me!',
        'Just some random thoughts about life and where I\'m headed.'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO journal_entries (user_id, title, content, mood, entry_date) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($userIds as $userId) {
        for ($i = 0; $i < 5; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $title = $titles[array_rand($titles)];
            $content = $contents[array_rand($contents)];
            $mood = $moods[array_rand($moods)];
            $stmt->execute([$userId, $title, $content, $mood, $date]);
        }
    }
    echo "Added journal entries\n";

    // 4. Add Journal Media
    $mediaTypes = ['image', 'audio'];
    $imageUrls = [
        'https://example.com/images/photo1.jpg',
        'https://example.com/images/photo2.jpg',
        'https://example.com/images/photo3.jpg',
        'https://example.com/audio/recording1.mp3',
        'https://example.com/audio/recording2.mp3'
    ];
    
    $captions = [
        'Beautiful day at the park',
        'Morning coffee thoughts',
        'Sunset view from my window',
        'Audio note about my day',
        'Quick voice memo'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO journal_media (journal_id, media_type, media_url, caption) VALUES (?, ?, ?, ?)");
    
    // Get all journal entries
    $journals = $pdo->query("SELECT id FROM journal_entries")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($journals as $journalId) {
        if (rand(0, 1)) { // 50% chance to add media to an entry
            $type = $mediaTypes[array_rand($mediaTypes)];
            $url = $imageUrls[array_rand($imageUrls)];
            $caption = $captions[array_rand($captions)];
            $stmt->execute([$journalId, $type, $url, $caption]);
        }
    }
    echo "Added journal media\n";

    // 5. Add Mood Analytics
    $stmt = $pdo->prepare("INSERT INTO mood_analytics (user_id, mood, average_intensity, entry_count, date) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($userIds as $userId) {
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $mood = $moods[array_rand($moods)];
            $intensity = rand(50, 100) / 10; // Random float between 5.0 and 10.0
            $count = rand(1, 3);
            $stmt->execute([$userId, $mood, $intensity, $count, $date]);
        }
    }
    echo "Added mood analytics\n";

    // 6. Add AI Insights
    $insightTypes = ['mood_pattern', 'sleep_quality', 'activity_impact', 'stress_pattern', 'productivity'];
    $insightContents = [
        'Your mood tends to be better after exercise.',
        'You sleep better when you avoid screens before bed.',
        'Morning meditation appears to improve your focus throughout the day.',
        'You\'re most productive in the early morning hours.',
        'Social interactions have a positive impact on your mood.'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO ai_insights (user_id, insight_type, content, is_read) VALUES (?, ?, ?, ?)");
    
    foreach ($userIds as $userId) {
        for ($i = 0; $i < 3; $i++) {
            $type = $insightTypes[array_rand($insightTypes)];
            $content = $insightContents[array_rand($insightContents)];
            $isRead = rand(0, 1);
            $stmt->execute([$userId, $type, $content, $isRead]);
        }
    }
    echo "Added AI insights\n";

    // 7. Add Tool Usage
    $tools = ['meditation', 'breathing_exercise', 'gratitude_journal', 'sleep_sounds', 'mood_tracker'];
    $stmt = $pdo->prepare("INSERT INTO tool_usage (user_id, tool_name, duration_minutes) VALUES (?, ?, ?)");
    
    foreach ($userIds as $userId) {
        for ($i = 0; $i < 5; $i++) {
            $tool = $tools[array_rand($tools)];
            $duration = rand(5, 60);
            $stmt->execute([$userId, $tool, $duration]);
        }
    }
    echo "Added tool usage\n";

    // 8. Add Support Contacts
    $relationships = ['Friend', 'Family', 'Therapist', 'Mentor', 'Colleague'];
    $stmt = $pdo->prepare("INSERT INTO support_contacts (user_id, name, relationship, phone, email, is_primary) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($userIds as $userId) {
        // Add 2-3 support contacts per user
        $numContacts = rand(2, 3);
        for ($i = 0; $i < $numContacts; $i++) {
            $name = ['John', 'Sarah', 'Mike', 'Emma', 'David', 'Lisa'][array_rand([0,1,2,3,4,5])] . ' ' . 
                   ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia'][array_rand([0,1,2,3,4,5])];
            $relationship = $relationships[array_rand($relationships)];
            $phone = '1' . rand(2000000000, 9999999999);
            $email = strtolower(explode(' ', $name)[0]) . rand(1, 100) . '@example.com';
            $isPrimary = $i === 0 ? 1 : 0; // First contact is primary
            
            $stmt->execute([$userId, $name, $relationship, $phone, $email, $isPrimary]);
        }
    }
    echo "Added support contacts\n";

    // 9. Add Achievements
    $achievements = [
        ['Early Bird', 'Complete 5 morning check-ins before 8 AM', 'early_bird.png', 50],
        ['Journal Master', 'Write 10 journal entries', 'journal.png', 100],
        ['Mood Tracker', 'Log your mood for 7 consecutive days', 'mood.png', 75],
        ['Self-Care Champion', 'Use self-care tools for 5 days in a row', 'self_care.png', 125],
        ['Consistency King/Queen', 'Use the app for 30 consecutive days', 'consistency.png', 200]
    ];
    
    $achievementIds = [];
    $stmt = $pdo->prepare("INSERT INTO achievements (name, description, icon, points) VALUES (?, ?, ?, ?)");
    
    foreach ($achievements as $achievement) {
        $stmt->execute($achievement);
        $achievementIds[] = $pdo->lastInsertId();
    }
    echo "Added achievements\n";

    // 10. Add User Achievements
    $stmt = $pdo->prepare("INSERT INTO user_achievements (user_id, achievement_id, unlocked_at) VALUES (?, ?, ?)");
    
    foreach ($userIds as $userId) {
        // Each user gets 1-3 random achievements
        $userAchievements = (array) array_rand(array_flip($achievementIds), rand(1, 3));
        if (!is_array($userAchievements)) {
            $userAchievements = [$userAchievements];
        }
        
        foreach ($userAchievements as $achievementId) {
            $unlockedAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
            $stmt->execute([$userId, $achievementId, $unlockedAt]);
        }
    }
    echo "Added user achievements\n";

    // 11. Add Crisis Resources
    $crisisResources = [
        [
            'National Suicide Prevention Lifeline',
            'Free and confidential support for people in distress',
            '988',
            'https://988lifeline.org',
            '24/7'
        ],
        [
            'Crisis Text Line',
            'Text HOME to 741741 for free, 24/7 crisis counseling',
            '741741',
            'https://www.crisistextline.org',
            '24/7'
        ],
        [
            'Veterans Crisis Line',
            'Support for veterans and their loved ones',
            '988',
            'https://www.veteranscrisisline.net',
            '24/7'
        ],
        [
            'Disaster Distress Helpline',
            'Crisis counseling for emotional distress related to disasters',
            '1-800-985-5990',
            'https://www.samhsa.gov/find-help/disaster-distress-helpline',
            '24/7'
        ],
        [
            'The Trevor Project',
            'Crisis intervention and suicide prevention for LGBTQ+ youth',
            '1-866-488-7386',
            'https://www.thetrevorproject.org',
            '24/7'
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO crisis_resources (title, description, phone, website, available_hours) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($crisisResources as $resource) {
        $stmt->execute($resource);
    }
    echo "Added crisis resources\n";

    // 12. Add User Goals
    $goalTypes = ['journal_entries', 'mood_checkins', 'meditation_minutes', 'gratitude_entries', 'exercise_minutes'];
    $stmt = $pdo->prepare("INSERT INTO user_goals (user_id, goal_type, target_value, current_value, target_date, is_completed) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($userIds as $userId) {
        // Each user gets 2-3 goals
        $numGoals = rand(2, 3);
        $userGoalTypes = (array) array_rand(array_flip($goalTypes), $numGoals);
        if (!is_array($userGoalTypes)) {
            $userGoalTypes = [$userGoalTypes];
        }
        
        foreach ($userGoalTypes as $goalType) {
            $targetValue = [
                'journal_entries' => rand(10, 30),
                'mood_checkins' => rand(14, 30),
                'meditation_minutes' => rand(300, 1000),
                'gratitude_entries' => rand(14, 30),
                'exercise_minutes' => rand(500, 1500)
            ][$goalType];
            
            $currentValue = rand(0, $targetValue);
            $targetDate = date('Y-m-d', strtotime('+' . rand(7, 30) . ' days'));
            $isCompleted = $currentValue >= $targetValue ? 1 : 0;
            
            $stmt->execute([$userId, $goalType, $targetValue, $currentValue, $targetDate, $isCompleted]);
        }
    }
    echo "Added user goals\n";

    // 13. Add Notification Settings
    $stmt = $pdo->prepare("INSERT INTO notification_settings (user_id, email_notifications, push_notifications, sms_notifications, daily_reminder, weekly_summary) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($userIds as $userId) {
        $email = rand(0, 1);
        $push = rand(0, 1);
        $sms = $push ? 0 : rand(0, 1); // Don't enable both push and SMS
        $daily = rand(0, 1);
        $weekly = rand(0, 1);
        
        $stmt->execute([$userId, $email, $push, $sms, $daily, $weekly]);
    }
    echo "Added notification settings\n";

    // 14. Add User Activities
    $activityTypes = ['exercise', 'meditation', 'yoga', 'reading', 'walking', 'running', 'swimming', 'cycling'];
    $activityNotes = [
        'Morning session',
        'Evening routine',
        'Quick break activity',
        'Weekend activity',
        'Group session',
        'Solo practice',
        'Guided session',
        'Outdoor activity'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, activity_type, duration_minutes, notes, activity_date) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($userIds as $userId) {
        // Each user gets 5-10 activities
        $numActivities = rand(5, 10);
        for ($i = 0; $i < $numActivities; $i++) {
            $type = $activityTypes[array_rand($activityTypes)];
            $duration = rand(5, 120);
            $note = $activityNotes[array_rand($activityNotes)];
            $date = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days'));
            
            $stmt->execute([$userId, $type, $duration, $note, $date]);
        }
    }
    echo "Added user activities\n";

    // Commit the transaction
    $pdo->commit();
    
    echo "\n✅ Sample data has been successfully added to the database!\n";
    echo "You can now access the application with the following test accounts:\n";
    
    // Display test account information
    $testAccounts = $pdo->query("SELECT email, 'password123' as password FROM users LIMIT 5")->fetchAll();
    foreach ($testAccounts as $i => $account) {
        echo ($i + 1) . ". Email: " . $account['email'] . " | Password: " . $account['password'] . "\n";
    }
    
} catch (Exception $e) {
    // Roll back the transaction if something failed
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
