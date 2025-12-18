<?php
// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database configuration
require_once 'config.php';

// Initialize response array
$response = array(
    'success' => false,
    'message' => '',
    'errors' => []
);

// Get the raw POST data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Debug: Log the raw input and parsed data
error_log("Raw input: " . $input);
error_log("Parsed data: " . print_r($data, true));

// Check if data is valid
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Only POST method is allowed';
    $response['errors'][] = 'Invalid request method';
    http_response_code(405);
    echo json_encode($response);
    exit;
}

if (json_last_error() !== JSON_ERROR_NONE) {
    $response['message'] = 'Invalid JSON';
    $response['errors'][] = 'Invalid JSON format in request body';
    $response['raw_input'] = $input;
    http_response_code(400);
    echo json_encode($response);
    exit;
}

if (empty($data)) {
    $response['message'] = 'No data received';
    $response['errors'][] = 'Request body is empty';
    http_response_code(400);
    echo json_encode($response);
    exit;
}

try {
    // Validate input
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $phno = $data['phno'] ?? '';
    $age = $data['age'] ?? null;
    $gender = $data['gender'] ?? null;

    // Validation
    if (empty($name)) {
        $response['errors'][] = "Name is required";
    }

    if (empty($email)) {
        $response['errors'][] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Please enter a valid email address";
    }

    if (empty($password)) {
        $response['errors'][] = "Password is required";
    } elseif (strlen($password) < 6) {
        $response['errors'][] = "Password must be at least 6 characters";
    }

    if (empty($phno)) {
        $response['errors'][] = "Phone number is required";
    }

    // If validation passes
    if (empty($response['errors'])) {
        // Check if email already exists
        $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->execute([$email]);
        
        if ($checkEmail->rowCount() > 0) {
            $response['message'] = 'Registration failed';
            $response['errors'][] = 'Email already exists';
            http_response_code(409);
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert new user
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password, phno, age, gender, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            if ($stmt->execute([$name, $email, $hashedPassword, $phno, $age, $gender])) {
                $userId = $pdo->lastInsertId();
                
                // Get the created user (without password)
                $userStmt = $pdo->prepare("
                    SELECT id, name, email, phno, age, gender, created_at 
                    FROM users 
                    WHERE id = ?
                ");
                $userStmt->execute([$userId]);
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                
                $response['success'] = true;
                $response['message'] = 'User registered successfully';
                $response['user'] = $user;
                http_response_code(201);
            } else {
                $response['message'] = 'Failed to register user';
                $response['errors'][] = 'Database error occurred';
                http_response_code(500);
            }
        }
    } else {
        $response['message'] = 'Validation failed';
        http_response_code(422);
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error occurred';
    $response['errors'][] = $e->getMessage();
    http_response_code(500);
} catch (Exception $e) {
    $response['message'] = 'An error occurred';
    $response['errors'][] = $e->getMessage();
    http_response_code(500);
}

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);