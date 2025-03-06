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

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON format"]);
    exit();
}


if (!isset(
    $data['user_id'],
    $data['education_level'],
    $data['profession'],
    $data['marital_status'],
    $data['religion'],
    $data['ethnicity']
)) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}


$user_id = intval($data['user_id']);
$education = $data['education_level'];
$profession = $data['profession'];
$marital_status = $data['marital_status'];
$religion = $data['religion'];
$ethnicity = $data['ethnicity'];

try {
    
    $sql = "
        INSERT INTO user_additional_details 
        (UserID, EducationLevel, Profession, MaritalStatus, Religion, Ethnicity, Update_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ";

    $stmt = $conn->prepare($sql);

    // Check for SQL syntax errors
    if ($stmt === false) {
        echo json_encode(["error" => "SQL Prepare Error: " . $conn->error]);
        exit();
    }

    // Bind parameters
    $stmt->bind_param("isssss", 
        $user_id, 
        $education, 
        $profession, 
        $marital_status, 
        $religion, 
        $ethnicity
    );

    if ($stmt->execute()) {
        echo json_encode(["message" => "This is the last stage of registration.
SMS a brief description of yourself starting with the word MYSELF"]);
    } else {
        echo json_encode(["error" => "Execution Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>