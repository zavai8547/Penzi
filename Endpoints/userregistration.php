<?php
require '../config/db.php';
require '../auth_middleware.php';


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$user = authenticate();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}


$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate JSON format
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON format"]);
    exit();
}


$name = $data['Name'] ?? null;
$age = $data['Age'] ?? null;
$gender = $data['Gender'] ?? null;
$county = $data['County'] ?? null;
$town = $data['Town'] ?? null;
$phoneNumber = $data['Phone'] ?? null; 


if (!$name || !$age || !$gender || !$county || !$town || !$phoneNumber) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

try {
    
    $stmt = $conn->prepare("INSERT INTO users (name, Age, Gender, County, Town, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $name, $age, $gender, $county, $town, $phoneNumber);
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        if (!$user_id) {
            echo json_encode(["error" => "Failed to retrieve user ID"]);
            exit();
        }
        echo json_encode(["message" => "Your profile has been created successfully $name.
SMS details#levelOfEducation#profession#maritalStatus#religion#ethnicity", "user_id" => $user_id]);
    } else {
        echo json_encode(["error" => "Registration failed: " . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>