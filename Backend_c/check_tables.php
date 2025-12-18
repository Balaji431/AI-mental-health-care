<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

try {
    // Check if tables exist
    $tables = [
        'users',
        'mood_analytics',
        'tool_usage',
        'ai_insights',
        'user_settings'
    ];

    echo "Checking database tables...\n\n";
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists\n";
            
            // Show table structure
            $stmt = $pdo->query("DESCRIBE $table");
            echo "   Columns:\n";
            while ($row = $stmt->fetch()) {
                echo "   - {$row['Field']} ({$row['Type']})\n";
            }
            echo "\n";
        } else {
            echo "❌ Table '$table' is MISSING\n";
        }
    }
    
    // Count records in each table
    echo "\nRecord counts:\n";
    foreach ($tables as $table) {
        try {
            $count = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch()['count'];
            echo "- $table: $count records\n";
        } catch (PDOException $e) {
            echo "- $table: Error - " . $e->getMessage() . "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    echo "Connection details: " . 
         "host=localhost, " . 
         "dbname=mindcare, " . 
         "user=root, " . 
         "password=" . (empty($pass) ? '[empty]' : '[set]') . "\n";
}
?>
