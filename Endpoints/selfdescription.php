<?php
require '../config/db.php';
require '../auth_middleware.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Script started\n"; // Debugging: Check if script starts

header("Content-Type: application/json");

// Dynamic CORS Handling
$allowed_origins = ["http://localhost:3000", "https://your-production-domain.com"];
$origin = $_SERVER['HTTP_ORIGIN'] ?? ''; // Safely get origin

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $origin);
} else {
    header("Access-Control-Allow-Origin: http://localhost:3000"); // Default
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

echo "CORS headers set\n"; // Debugging: Check if CORS headers are set

// Authenticate user
$user = authenticate();
if (!$user || empty($user['UserID'])) {
    error_log("❌ Authentication failed: Invalid token or missing UserID.");
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized: Authentication failed."]);
    exit();
}

$auth_user_id = (int) $user['UserID']; // Authenticated user ID

echo "User authenticated\n"; // Debugging: Check if user is authenticated

// Read JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("❌ JSON Decode Error: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON format"]);
    exit();
}

echo "JSON decoded\n"; // Debugging: Check if JSON is decoded


if (empty($data['phone']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields (phone, message)"]);
    exit();
}

$phone = trim($data['phone']);
$description = trim($data['message']);


if (stripos($description, "MYSELF") !== 0) {
    http_response_code(400);
    echo json_encode(["error" => "Description must start with 'MYSELF'"]);
    exit();
}

echo "Fields validated\n"; 


$query = $conn->prepare("SELECT UserID FROM users WHERE PhoneNumber = ?");
if (!$query) {
    error_log("❌ Database error: " . $conn->error);
    http_response_code(500);
    echo json_encode(["error" => "Database error: SQL preparation failed"]);
    exit();
}

$query->bind_param("s", $phone);
$query->execute();
$query->store_result();

if ($query->num_rows === 0) {
    error_log("❌ User not found for phone: " . $phone);
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
    exit();
}

$query->bind_result($phone_user_id);
$query->fetch();
$query->close();

echo "User ID fetched\n"; 


if ($phone_user_id !== $auth_user_id) {
    error_log("❌ Forbidden: Authenticated UserID ($auth_user_id) does not match phone owner ($phone_user_id)");
    http_response_code(403);
    echo json_encode(["error" => "Forbidden: Phone number does not match authenticated user"]);
    exit();
}

echo "User ID matched\n"; 

// Update user self-description
$stmt = $conn->prepare("UPDATE user_additional_details SET SelfDescription = ?, Updated_at = NOW() WHERE UserID = ?");
if (!$stmt) {
    error_log("❌ SQL Preparation Error: " . $conn->error);
    http_response_code(500);
    echo json_encode(["error" => "Database error: SQL preparation failed"]);
    exit();
}

$stmt->bind_param("si", $description, $auth_user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["message" => "Profile updated successfully!"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "No changes made or user not found"]);
}

echo "Update executed\n"; 

$stmt->close();
$conn->close();

echo "Script finished\n"; 
?>