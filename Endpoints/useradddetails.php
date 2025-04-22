<?php
require_once '/var/www/html/config/db.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

// Read input JSON
$data = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON format"]);
    exit();
}

// Validate required fields
if (!isset($data['phone']) || !isset($data['message'])) {
    echo json_encode(["error" => "Missing phone number or message field"]);
    exit();
}

$phone = trim($data['phone']);
$message = trim($data['message']);

// Ensure phone exists and fetch UserID
$checkStmt = $conn->prepare("SELECT UserID FROM users WHERE PhoneNumber = ?");
if (!$checkStmt) {
    echo json_encode(["error" => "SQL Prepare Error: " . $conn->error]);
    exit();
}

$checkStmt->bind_param("s", $phone);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    echo json_encode(["error" => "User not found"]);
    exit();
}

$checkStmt->bind_result($user_id);
$checkStmt->fetch();
$checkStmt->close();

// Parse message
$parts = explode("#", $message);

if (count($parts) < 6) {
    echo json_encode(["error" => "Invalid message format. Required format: details#Educationlevel#Profession#MaritalStatus#Religion#Ethnicity"]);
    exit();
}

// Extract values
$education = trim($parts[1]);
$profession = trim($parts[2]);
$marital_status = trim($parts[3]);
$religion = trim($parts[4]);
$ethnicity = trim($parts[5]);

// Insert into database
try {
    $stmt = $conn->prepare("
        INSERT INTO user_additional_details 
        (UserID, Educationlevel, Profession, MaritalStatus, Religion, Ethnicity, Update_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    if (!$stmt) {
        echo json_encode(["error" => "SQL Prepare Error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("isssss", 
        $user_id, 
        $education, 
        $profession, 
        $marital_status, 
        $religion, 
        $ethnicity
    );

    if ($stmt->execute()) {
        echo json_encode(["message" => "This is the last stage of registration. SMS a brief description of yourself starting with the word MYSELF"]);
    } else {
        echo json_encode(["error" => "Execution Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>