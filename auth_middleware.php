<?php
require_once 'C:/xampp/htdocs/penzi/config/jwt_helper.php';


function getAuthorizationHeader() {
    if (isset($_SERVER['Authorization'])) {
        return $_SERVER['Authorization'];
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or FastCGI
        return $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        return $headers['Authorization'] ?? null;
    }
    return null;
}

function authenticate() {
    // Get Authorization header
    $authHeader = getAuthorizationHeader();

    if (!$authHeader || !preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["error" => "Authorization header missing or invalid"]);
        exit;
    }

    $jwt = $matches[1];
    $payload = validate_jwt($jwt);

    if (!$payload) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid or expired token"]);
        exit;
    }

    return $payload;
}
?>
