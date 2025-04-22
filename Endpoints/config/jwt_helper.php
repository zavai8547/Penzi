<?php

define('SECRET_KEY', 'your-256-bit-secret-here!'); 
define('VALID_API_KEY', 'Rodney');


function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}


function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}


function generate_jwt($payload) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload['exp'] = time() + (60 * 60 * 24 * 30); // 30 days

    $encoded_header = base64url_encode($header);
    $encoded_payload = base64url_encode(json_encode($payload));

    $signature = hash_hmac('sha256', "$encoded_header.$encoded_payload", SECRET_KEY, true);
    $encoded_signature = base64url_encode($signature);

    return "$encoded_header.$encoded_payload.$encoded_signature";
}

// Validate JWT
function validate_jwt($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;

    list($encoded_header, $encoded_payload, $encoded_signature) = $parts;

    // Verify signature
    $signature = base64url_decode($encoded_signature);
    $expected_signature = hash_hmac('sha256', "$encoded_header.$encoded_payload", SECRET_KEY, true);

    if (!hash_equals($signature, $expected_signature)) return false;

    $payload = json_decode(base64url_decode($encoded_payload), true);

    // Check expiration
    if (isset($payload['exp']) && $payload['exp'] < time()) return false;

    return $payload;
}

// Authentication function
function authenticate_user() {
    $dummy_users = [
        ['id' => 1, 'username' => 'testuser', 'password' => password_hash('password123', PASSWORD_DEFAULT)],
    ];

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    foreach ($dummy_users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}
?>
