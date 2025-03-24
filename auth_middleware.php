<?php 
require_once 'C:/xampp/htdocs/penzi/config/jwt_helper.php';

function getAuthorizationHeader() {
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) { // Standard case
        return $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (!empty($_SERVER['Authorization'])) { // Alternative case
        return $_SERVER['Authorization'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        return $headers['Authorization'] ?? null;
    }
    return null;
}

function authenticate() {
    // Fetch Authorization header
    $authHeader = getAuthorizationHeader();

    if (!$authHeader) {
        error_log("❌ Authorization failed: Missing Authorization header");
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized: Missing Authorization header"]);
        exit;
    }

    // Extract Bearer token
    if (!preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
        error_log("❌ Authorization failed: Invalid token format");
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized: Invalid token format"]);
        exit;
    }

    $jwt = trim($matches[1]);

    // Validate JWT
    $payload = validate_jwt($jwt);

    if (!$payload) {
        error_log("❌ Authorization failed: Invalid or expired token");
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized: Invalid or expired token"]);
        exit;
    }

    // Debugging: Log JWT payload (remove in production)
    error_log("✅ JWT Payload: " . json_encode($payload));

    // Ensure UserID is present in the JWT payload
    if (empty($payload['UserID']) || !is_numeric($payload['UserID'])) {
        error_log("❌ Authentication failed: Missing or invalid UserID in token");
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized: User ID missing in token"]);
        exit;
    }

    return ["UserID" => intval($payload['UserID'])]; // Ensure UserID is an integer
}
?>