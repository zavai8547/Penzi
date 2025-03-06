<?php
require '../config/db.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$user = authenticate();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON format"]);
    exit();
}


if (!isset($data['user_id'], $data['description'])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

$user_id = intval($data['user_id']);
$description = $data['description'];

try {
    
    $stmt = $conn->prepare("
        UPDATE user_additional_details 
        SET SelfDescription = ?, Update_at = NOW() 
        WHERE UserID = ?
    ");
    
    $stmt->bind_param("si", $description, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["message" => "You are now registered for dating, To search for a mpenzi sms #age #town and mee your partner. For example#20-30#Nairobi"]);
        } else {
            echo json_encode(["error" => "No matching user found or no changes made"]);
        }
    } else {
        echo json_encode(["error" => "Update failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>