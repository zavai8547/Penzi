<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

$response = [
    "reply" => "Welcome to our dating service with 6000 potential dating partners! To register, SMS start#name#age#gender#county#town#PhoneNumber E.g., start#Zavai Rodney#26#Male#Nakuru#Naivasha#0758265242"
];

echo json_encode($response);
?>
