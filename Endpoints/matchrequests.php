<?php
require_once '/var/www/html/config/db.php';
// require '../auth_middleware.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3002");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit();
}

// Get input data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON format"]);
    exit();
}


$requiredFields = ['min_age', 'max_age', 'town', 'requester_phone'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required field: $field"]);
        exit();
    }
}


$min_age = filter_var($data['min_age'], FILTER_VALIDATE_INT);
$max_age = filter_var($data['max_age'], FILTER_VALIDATE_INT);
$town = htmlspecialchars($data['town'], ENT_QUOTES, 'UTF-8');
$requester_phone = filter_var($data['requester_phone'], FILTER_SANITIZE_STRING);


if ($min_age === false || $max_age === false || $min_age > $max_age) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid age range"]);
    exit();
}

try {
   
    $stmt = $conn->prepare("SELECT UserID, Gender FROM users WHERE PhoneNumber = ?");
    $stmt->bind_param("s", $requester_phone);
    $stmt->execute();
    $requester = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$requester) {
        http_response_code(404);
        echo json_encode(["error" => "Requester not found"]);
        exit();
    }

    
    $age_range = "$min_age-$max_age";
    $insert_stmt = $conn->prepare("INSERT INTO matchrequests (UserID, AgeRange, Town, RequestDate) VALUES (?, ?, ?, NOW())");
    $insert_stmt->bind_param("iss", $requester['UserID'], $age_range, $town);
    $insert_stmt->execute();
    $insert_stmt->close();

    
    echo json_encode([
        "message" => "Match request processed successfully",
        "total_matches" => 0, // Implement actual matching logic
        "matches" => []
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
?>