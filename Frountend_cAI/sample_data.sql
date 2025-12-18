-- Sample Data for MindCare Database

-- 1. Users
INSERT INTO users (name, mail, phno, password, age, gender, created_at) VALUES
('John Doe', 'john@example.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 30, 'Male', NOW()),
('Jane Smith', 'jane@example.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 28, 'Female', NOW());

-- 2. Journal Entries
INSERT INTO journal_entries (user_id, title, content, mood, entry_date, created_at) VALUES
(1, 'Feeling Good Today', 'Had a great day at work and went for a nice walk in the park.', 'happy', '2023-06-15', NOW()),
(1, 'Stressed Out', 'Work is getting really overwhelming. Need to take a break.', 'stressed', '2023-06-16', NOW()),
(2, 'New Beginnings', 'Started my new job today. Feeling nervous but excited!', 'excited', '2023-06-15', NOW()),
(2, 'Rainy Day', 'Stayed in and read a book. Perfect weather for some self-care.', 'calm', '2023-06-17', NOW());

-- 3. Journal Media
INSERT INTO journal_media (journal_id, media_type, media_url, caption, created_at) VALUES
(1, 'image', 'https://example.com/images/park.jpg', 'Beautiful park view', NOW()),
(1, 'audio', 'https://example.com/audio/birds.mp3', 'Birds chirping', NOW()),
(3, 'image', 'https://example.com/images/office.jpg', 'First day at new job', NOW()),
(4, 'image', 'https://example.com/images/rainy_day.jpg', 'View from my window', NOW());

-- 4. Mood Check-ins
INSERT INTO mood_checkins (user_id, mood, intensity, notes, created_at) VALUES
(1, 'happy', 8, 'Great day overall', '2023-06-15 18:30:00'),
(1, 'anxious', 6, 'Work deadline approaching', '2023-06-16 19:15:00'),
(2, 'excited', 9, 'First day at new job', '2023-06-15 20:00:00'),
(2, 'calm', 7, 'Relaxing evening at home', '2023-06-17 21:00:00');

-- 5. AI Insights
INSERT INTO ai_insights (user_id, insight_type, content, is_read, created_at) VALUES
(1, 'mood_pattern', 'You tend to feel happier on weekends', 0, NOW()),
(1, 'activity_impact', 'Exercise seems to improve your mood by 20%', 1, '2023-06-14 10:00:00'),
(2, 'sleep_quality', 'Better sleep correlates with higher daily mood', 0, NOW()),
(2, 'stress_pattern', 'Work-related stress peaks mid-week', 1, '2023-06-13 09:30:00');

-- 6. Tool Usage
INSERT INTO tool_usage (user_id, tool_name, duration_minutes, created_at) VALUES
(1, 'breathing_exercise', 5, '2023-06-15 19:00:00'),
(1, 'meditation', 10, '2023-06-16 20:00:00'),
(2, 'gratitude_journal', 15, '2023-06-15 21:00:00'),
(2, 'sleep_sounds', 30, '2023-06-17 22:00:00');

-- 7. Support Contacts
INSERT INTO support_contacts (user_id, name, relationship, phone, email, is_primary, created_at) VALUES
(1, 'Sarah Johnson', 'Friend', '1112223333', 'sarah@example.com', 1, NOW()),
(1, 'Dr. Michael Brown', 'Therapist', '4445556666', 'drbrown@clinic.com', 0, NOW()),
(2, 'Alex Taylor', 'Partner', '7778889999', 'alex@example.com', 1, NOW()),
(2, 'Dr. Emily Wilson', 'Counselor', '2223334444', 'emily@wellness.com', 0, NOW());

-- 8. Achievements
INSERT INTO achievements (name, description, icon, points, created_at) VALUES
('Early Bird', 'Complete 5 morning check-ins before 8 AM', 'early_bird.png', 50, NOW()),
('Journal Master', 'Write 10 journal entries', 'journal.png', 100, NOW()),
('Mood Tracker', 'Log your mood for 7 consecutive days', 'mood.png', 75, NOW()),
('Self-Care Champion', 'Use self-care tools for 5 days in a row', 'self_care.png', 125, NOW());

-- 9. User Achievements
INSERT INTO user_achievements (user_id, achievement_id, unlocked_at, created_at) VALUES
(1, 2, '2023-06-10 15:30:00', NOW()),
(1, 3, '2023-06-12 16:45:00', NOW()),
(2, 1, '2023-06-11 07:15:00', NOW()),
(2, 4, '2023-06-14 20:30:00', NOW());

-- 10. User Settings
INSERT INTO user_settings (user_id, theme, notifications_enabled, reminder_time, language, created_at) VALUES
(1, 'light', 1, '20:00:00', 'en', NOW()),
(2, 'dark', 1, '21:00:00', 'en', NOW());

-- 11. Crisis Resources
INSERT INTO crisis_resources (title, description, phone, website, available_hours, created_at) VALUES
('National Suicide Prevention Lifeline', 'Free and confidential support for people in distress', '988', 'https://988lifeline.org', '24/7', NOW()),
('Crisis Text Line', 'Text HOME to 741741 for free, 24/7 crisis counseling', '741741', 'https://www.crisistextline.org', '24/7', NOW()),
('Veterans Crisis Line', 'Support for veterans and their loved ones', '988', 'https://www.veteranscrisisline.net', '24/7', NOW()),
('Disaster Distress Helpline', 'Crisis counseling for emotional distress related to disasters', '1-800-985-5990', 'https://www.samhsa.gov/find-help/disaster-distress-helpline', '24/7', NOW());

-- 12. Mood Analytics
INSERT INTO mood_analytics (user_id, mood, average_intensity, entry_count, date, created_at) VALUES
(1, 'happy', 7.5, 3, '2023-06-15', NOW()),
(1, 'anxious', 6.0, 2, '2023-06-16', NOW()),
(2, 'excited', 8.5, 2, '2023-06-15', NOW()),
(2, 'calm', 7.0, 2, '2023-06-17', NOW());

-- 13. User Goals
INSERT INTO user_goals (user_id, goal_type, target_value, current_value, target_date, is_completed, created_at) VALUES
(1, 'journal_entries', 10, 5, '2023-07-15', 0, NOW()),
(1, 'mood_checkins', 14, 7, '2023-07-01', 0, NOW()),
(2, 'meditation_minutes', 300, 150, '2023-07-10', 0, NOW()),
(2, 'gratitude_entries', 21, 10, '2023-07-20', 0, NOW());

-- 14. Notification Settings
INSERT INTO notification_settings (user_id, email_notifications, push_notifications, sms_notifications, daily_reminder, weekly_summary, created_at) VALUES
(1, 1, 1, 0, 1, 1, NOW()),
(2, 1, 1, 1, 1, 0, NOW());

-- 15. User Activities
INSERT INTO user_activities (user_id, activity_type, duration_minutes, notes, created_at) VALUES
(1, 'exercise', 30, 'Morning jog', '2023-06-15 07:00:00'),
(1, 'meditation', 15, 'Evening session', '2023-06-16 20:30:00'),
(2, 'yoga', 45, 'Online class', '2023-06-15 18:00:00'),
(2, 'reading', 60, 'Self-help book', '2023-06-17 21:00:00');
