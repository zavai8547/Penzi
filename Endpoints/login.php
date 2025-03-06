<?php
require_once '../config/jwt_helper.php'; 
require '../auth_middleware.php';

header('Content-Type: application/json');

$user = authenticate();
$api_key = $_POST['api_key'] ?? ''; // Get API key from request
$valid_api_key = "Rodney"; // Define a fixed API key

if ($api_key !== $valid_api_key) {
    echo json_encode(["error" => "Invalid API Key"]);
    exit;
}

// Generate JWT token
function generate_jwt($payload, $secret = SECRET_KEY) {
    $payload['exp'] = time() + (60 * 60 * 24 * 30); // 30 days validity
    return JWT::encode($payload, $secret, 'HS256'); // Use a valid JWT library
}

$token = generate_jwt($payload);

echo json_encode(["token" => $token]);
?>
