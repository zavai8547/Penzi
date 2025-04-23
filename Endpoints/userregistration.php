<?php

header("Access-Control-Allow-Origin: http://localhost:3002");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Vary: Origin");
header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(204);
    exit();
}

// database connection
require_once '/var/www/html/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON format"]);
    exit();
}

if (!$data || !isset($data['message']) || empty(trim($data['message']))) {
    http_response_code(400);
    echo json_encode(["error" => "Message not provided or empty"]);
    exit();
}

$message = trim($data['message']);

if (strpos($message, 'start#') !== 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid message format. Must start with 'start#'"]);
    exit();
}

$parts = explode('#', substr($message, 6));

error_log("Extracted parts count: " . count($parts));
error_log("Extracted parts: " . print_r($parts, true));

if (count($parts) !== 6) {
    http_response_code(400);
    echo json_encode(["error" => "Incorrect format. Expected: start#name#age#gender#county#town#phoneNumber"]);
    exit();
}

list($name, $age, $gender, $county, $town, $phoneNumber) = array_map('trim', $parts);

if (!$name || !$age || !$gender || !$county || !$town || !$phoneNumber) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

if (!is_numeric($age)) {
    http_response_code(400);
    echo json_encode(["error" => "Age must be a number"]);
    exit();
}


$phoneNumber = preg_replace('/\D/', '', $phoneNumber);
if (strlen($phoneNumber) === 9) {
    $phoneNumber = '0' . $phoneNumber;
}
if (!preg_match('/^07\d{8}$/', $phoneNumber)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid phone number format. Must be in format 07XXXXXXXX."]);
    exit();
}

// ensure hakuna numbers kama hio kwa db.
$checkStmt = $conn->prepare("SELECT PhoneNumber FROM users WHERE PhoneNumber = ?");
$checkStmt->bind_param("s", $phoneNumber);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $checkStmt->close();
    http_response_code(409); 
    echo json_encode(["error" => "Phone number already registered"]); 
    exit();
}
$checkStmt->close();

$name = mysqli_real_escape_string($conn, $name);
$gender = mysqli_real_escape_string($conn, $gender);
$county = mysqli_real_escape_string($conn, $county);
$town = mysqli_real_escape_string($conn, $town);
$phoneNumber = mysqli_real_escape_string($conn, $phoneNumber);

try {
    $stmt = $conn->prepare("INSERT INTO users (name, Age, Gender, County, Town, PhoneNumber, RegistrationDate) VALUES (?, ?, ?, ?, ?, ?, NOW())");

    if (!$stmt) {
        throw new Exception("SQL Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sissss", $name, $age, $gender, $county, $town, $phoneNumber);

    if ($stmt->execute()) {
        echo json_encode([
            "message" => "Your profile has been created successfully, $name. Age: $age please sms: details#levelOfEducation#profession#maritalStatus#religion#ethnicity",
            "phone" => $phoneNumber,
            "age" => $age,
        ]);
    } else {
        error_log("SQL Error: " . $stmt->error);
        http_response_code(500);
        echo json_encode(["error" => "Registration failed: " . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

$conn->close();
?>