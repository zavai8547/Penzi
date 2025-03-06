<?php
require 'jwt_helper.php';

function authenticate() {
    $secret = 'your-256-bit-secret'; // Replace with env variable
    
    // Get Authorization header
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["error" => "Authorization header missing"]);
        exit();
    }

    $authHeader = $headers['Authorization'];
    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid token format"]);
        exit();
    }

    $jwt = $matches[1];
    
    if (!validate_jwt($jwt, $secret)) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid or expired token"]);
        exit();
    }

    // Return decoded payload
    $tokenParts = explode('.', $jwt);
    return json_decode(base64_decode(strtr($tokenParts[1], '-_', '+/')), true);
}
?>