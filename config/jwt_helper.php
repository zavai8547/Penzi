<?php
if (!defined('SECRET_KEY')) {
    define('SECRET_KEY', 'your_super_secret_key');
}

if (!function_exists('base64url_encode')) {
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('generate_jwt')) {
    function generate_jwt($payload, $secret = SECRET_KEY) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $encoded_header = base64url_encode($header);
        $encoded_payload = base64url_encode(json_encode($payload));

        $signature = hash_hmac('sha256', "$encoded_header.$encoded_payload", $secret, true);
        $encoded_signature = base64url_encode($signature);

        return "$encoded_header.$encoded_payload.$encoded_signature";
    }
}

if (!function_exists('validate_jwt')) {
    function validate_jwt($jwt, $secret = SECRET_KEY) {
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) !== 3) return false;

        $header = base64_decode(strtr($tokenParts[0], '-_', '+/'));
        $payload = base64_decode(strtr($tokenParts[1], '-_', '+/'));
        $signature = base64_decode(strtr($tokenParts[2], '-_', '+/'));

        $expected_signature = hash_hmac('sha256', "$tokenParts[0].$tokenParts[1]", $secret, true);

        return hash_equals($signature, $expected_signature) && json_decode($payload, true)['exp'] > time();
    }
}
?>
