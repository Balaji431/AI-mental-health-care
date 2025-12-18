<?php
// Database configuration
$host = 'localhost';
$dbname = 'mindcare';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password

try {
    // Create connection without database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read SQL file
    $sql = file_get_contents('database_schema.sql');
    
    // Split the SQL file into individual queries
    $queries = explode(';', $sql);
    
    // Execute each query
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            try {
                $pdo->exec($query);
                echo "Executed: " . substr($query, 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nDatabase setup completed successfully!\n";
    
    // Import sample data
    echo "\nImporting sample data...\n";
    require_once 'import_sample_data.php';
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Create or update db.php with database configuration
$dbConfig = '<?php
// db.php - Database configuration
$host = \'' . addslashes($host) . '\';
$db   = \'' . addslashes($dbname) . '\';
$user = \'' . addslashes($username) . '\';
$pass = \'' . addslashes($password) . '\';
$charset = \'utf8mb4\';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    header(\'Content-Type: application/json\');
    echo json_encode([\'success\' => false, \'message\' => \'Database connection failed\']);
    error_log(\'DB connection error: \'.$e->getMessage());
    exit;
}';

file_put_contents('db.php', $dbConfig);
echo "\ndb.php configuration file has been updated.\n";

echo "\nSetup completed successfully! You can now access the application.\n";
