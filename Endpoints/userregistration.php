<?php 
require_once '../auth_middleware.php'; // Prevent duplicate inclusion
require_once '../config/db.php';

// Allow CORS for local development
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight request for OPTIONS method
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// Authenticate user
$user = authenticate();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

// Read raw input data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data['message']) || empty($data['message'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Message not provided"]);
    exit();
}

$message = trim($data['message']);

// Check if the input contains the "start#" prefix
if (strpos($message, 'start#') !== 0) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid message format"]);
    exit();
}

// Remove 'start#' and split the remaining string by '#'
$parts = explode('#', substr($message, 6));

if (count($parts) < 6) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Incomplete details. Format should be: start#name#age#gender#county#town#phoneNumber"]);
    exit();
}

// Assign extracted values
$name = trim($parts[0]);
$age = trim($parts[1]);
$gender = trim($parts[2]);
$county = trim($parts[3]);
$town = trim($parts[4]);
$phoneNumber = trim($parts[5]);

// Validate required fields
if (!$name || !$age || !$gender || !$county || !$town || !$phoneNumber) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

// Validate age and phone number format
if (!is_numeric($age)) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Age must be a number"]);
    exit();
}
if (!preg_match('/^\d{10}$/', $phoneNumber)) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid phone number format. Must be 10 digits."]);
    exit();
}

// Secure inputs to prevent SQL injection
$name = mysqli_real_escape_string($conn, $name);
$gender = mysqli_real_escape_string($conn, $gender);
$county = mysqli_real_escape_string($conn, $county);
$town = mysqli_real_escape_string($conn, $town);
$phoneNumber = mysqli_real_escape_string($conn, $phoneNumber);

try {
    $stmt = $conn->prepare("INSERT INTO users (name, Age, Gender, County, Town, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("SQL Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sissss", $name, $age, $gender, $county, $town, $phoneNumber); 

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        echo json_encode([
            "message" => "Your profile has been created successfully, $name.\nSMS details#levelOfEducation#profession#maritalStatus#religion#ethnicity",
            "user_id" => $user_id
        ]);
    } else {
        throw new Exception("SQL Execution failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

$conn->close();
?>
