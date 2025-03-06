<?php
require_once '../config/jwt_helper.php'; 

header('Content-Type: application/json');

$api_key = $_POST['api_key'] ?? ''; // Get API key from request
$valid_api_key = "Rodney"; // Define a fixed API key

if ($api_key !== $valid_api_key) {
    echo json_encode(["error" => "Invalid API Key"]);
    exit;
}

// Generate JWT token
$payload = [
    "iss" => "PenziAPI",
    "exp" => time() + (60 * 60), 
    "scope" => "protected"
];

$token = generate_jwt($payload);

echo json_encode(["token" => $token]);
?>
