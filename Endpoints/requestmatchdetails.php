<?php
require '../config/db.php';
require '../auth_middleware.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$user = authenticate();

$phoneNumber = $_GET['phoneNumber'] ?? '';

if (empty($phoneNumber)) {
    echo json_encode(["error" => "Phone number is required"]);
    exit();
}

try {

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    
    $stmt = $conn->prepare("
        SELECT 
            u.name, u.Age, u.County, u.Town,
            a.EducationLevel, a.Profession, a.MaritalStatus, a.Religion, a.Ethnicity
        FROM users u
        LEFT JOIN user_additional_details a ON u.UserID = a.UserID
        WHERE u.PhoneNumber = ?
    ");

    
    if ($stmt === false) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $phoneNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        echo json_encode(["error" => "User not found"]);
        exit();
    }

    // Format response (with null coalescing operators)
    $message = sprintf(
        "%s aged %d, %s County, %s town, %s, %s, %s, %s, %s. Send DESCRIBE %s to get more details about %s",
        $user['name'],
        $user['Age'],
        $user['County'] ?? 'Not specified',
        $user['Town'] ?? 'Not specified',
        $user['EducationLevel'] ?? 'Not specified',
        $user['Profession'] ?? 'Not specified',
        strtolower($user['MaritalStatus'] ?? 'Not specified'),
        $user['Religion'] ?? 'Not specified',
        $user['Ethnicity'] ?? 'Not specified',
        $phoneNumber,
        explode(' ', $user['name'])[0] // First name
    );

    echo json_encode(["message" => $message]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>