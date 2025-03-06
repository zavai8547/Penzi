<?php
require '../config/db.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);


if (!isset($data['requester_phone'], $data['target_phone'])) {
    echo json_encode(["error" => "Missing phone numbers"]);
    exit();
}

try {
    
    $stmt = $conn->prepare("
        SELECT name, Age, County 
        FROM users 
        WHERE PhoneNumber = ?
    ");
    $stmt->bind_param("s", $data['requester_phone']);
    $stmt->execute();
    $requester = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$requester) {
        echo json_encode(["error" => "Requester not found"]);
        exit();
    }

    // get users name 
    $stmt = $conn->prepare("SELECT name FROM users WHERE PhoneNumber = ?");
    $stmt->bind_param("s", $data['target_phone']);
    $stmt->execute();
    $target = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$target) {
        echo json_encode(["error" => "Target user not found"]);
        exit();
    }

    
    $message = sprintf(
        "Hi %s, a person called %s is interested in you and requested your details.\n"
        . "He is aged %d based in %s.\n"
        . "Do you want to know more about him? Send YES to 22141",
        $target['name'],
        $requester['name'],
        $requester['Age'],
        $requester['County']
    );

    
    echo json_encode([
        "message" => "Notification sent to target user",
        "sms_content" => $message
    ]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>