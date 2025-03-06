<?php
// Generate JWT secret key (run once and store in .env)
// $secret = bin2hex(random_bytes(32));
// echo $secret;

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); // Added missing closing parenthesis and '=' parameter
}

function generate_jwt($payload, $secret) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $encoded_header = base64url_encode($header);
    $encoded_payload = base64url_encode(json_encode($payload));
    $signature = hash_hmac('sha256', "$encoded_header.$encoded_payload", $secret, true);
    $encoded_signature = base64url_encode($signature);
    
    return "$encoded_header.$encoded_payload.$encoded_signature";
}

function validate_jwt($jwt, $secret) {
    $tokenParts = explode('.', $jwt);
    if (count($tokenParts) !== 3) return false;
    
    $header = base64_decode(strtr($tokenParts[0], '-_', '+/'));
    $payload = base64_decode(strtr($tokenParts[1], '-_', '+/'));
    $signature = base64_decode(strtr($tokenParts[2], '-_', '+/'));
    
    $expected_signature = hash_hmac('sha256', "$tokenParts[0].$tokenParts[1]", $secret, true);
    
    return hash_equals($signature, $expected_signature) && 
           json_decode($payload, true)['exp'] > time();
}
?> 