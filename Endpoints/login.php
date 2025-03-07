<?php
require_once __DIR__ . '/../config/jwt_helper.php';
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Authenticate user
$user = authenticate_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

// Validate API key
$api_key = $_POST['api_key'] ?? '';
if ($api_key !== VALID_API_KEY) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid API key']);
    exit;
}

// Generate JWT
$payload = [
    'sub' => $user['id'],
    'iat' => time(),
    'exp' => time() + (60 * 60 * 24 * 30) // 30 days expiration
];

try {
    $token = generate_jwt($payload);
    echo json_encode(['token' => $token], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Token generation failed']);
}
?>
