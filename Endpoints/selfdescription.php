<?php
require_once '/var/www/html/config/db.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
header("Access-Control-Allow-Credentials: true");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


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


if (!isset($data['phone']) || !isset($data['message'])) {
    echo json_encode(["error" => "Missing phone number or message field"]);
    exit();
}

$phone = trim($data['phone']);
$description = trim($data['message']);

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

// Update user self-description
$stmt = $conn->prepare("UPDATE user_additional_details SET SelfDescription = ?, Update_at = NOW() WHERE UserID = ?");
if (!$stmt) {
    echo json_encode(["error" => "SQL Prepare Error: " . $conn->error]);
    exit();
}

$stmt->bind_param("si", $description, $user_id); 
$stmt->execute();

if ($stmt->affected_rows > 0) {
    
    echo json_encode([
        "message" => "You are now registered for dating.\nTo search for a MPENZI, Send match#age#town to and meet the person of\nyour dreams.\nE.g., match#23-25#Kisumu"
    ]);
} else {
    echo json_encode(["error" => "No users found "]);
}

$stmt->close();
$conn->close();
?>