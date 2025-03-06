<?php
require '../config/db.php';
require '../auth_middleware.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$user = authenticate();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['min_age'], $data['max_age'], $data['town'], $data['requester_phone'])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

$min_age = intval($data['min_age']);
$max_age = intval($data['max_age']);
$town = $data['town'];
$requester_phone = $data['requester_phone'];

try {
    // Get requester's UserID and Gender
    $stmt = $conn->prepare("SELECT UserID, Gender FROM users WHERE PhoneNumber = ?");
    $stmt->bind_param("s", $requester_phone);
    $stmt->execute();
    $requester = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$requester) {
        echo json_encode(["error" => "Requester not found"]);
        exit();
    }

    $user_id = $requester['UserID'];
    $requester_gender = $requester['Gender'];

    // Save match request to database
    $insert_stmt = $conn->prepare("
        INSERT INTO matchrequests 
        (UserID, AgeRange, Town, RequestDate) 
        VALUES (?, ?, ?, NOW())
    ");
    $age_range = $min_age . '-' . $max_age;
    $insert_stmt->bind_param("iss", $user_id, $age_range, $town);
    $insert_stmt->execute();
    $insert_stmt->close();

    // Also save request in the `requests` table
    $request_data = json_encode([
        "min_age" => $min_age,
        "max_age" => $max_age,
        "town" => $town
    ]);

    $req_stmt = $conn->prepare("
        INSERT INTO requests 
        (UserID, RequestType, RequestData, RequestDate, update_at) 
        VALUES (?, 'match_request', ?, NOW(), NOW())
    ");
    $req_stmt->bind_param("is", $user_id, $request_data);
    $req_stmt->execute();
    $req_stmt->close();

    // Determine target gender
    $target_gender = ($requester_gender === 'Male') ? 'Female' : 'Male';

    
    $count_stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM users 
        WHERE Age BETWEEN ? AND ? 
        AND Town = ? 
        AND Gender = ?
        AND PhoneNumber != ?
    ");
    $count_stmt->bind_param("iisss", $min_age, $max_age, $town, $target_gender, $requester_phone);
    $count_stmt->execute();
    $total = $count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();

    
    $match_stmt = $conn->prepare("
        SELECT name, Age, PhoneNumber 
        FROM users 
        WHERE Age BETWEEN ? AND ? 
        AND Town = ? 
        AND Gender = ?
        AND PhoneNumber != ?
        LIMIT 3
    ");
    $match_stmt->bind_param("iisss", $min_age, $max_age, $town, $target_gender, $requester_phone);
    $match_stmt->execute();
    $matches = $match_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $match_stmt->close();

    
    $gender_term = ($target_gender === 'Female') ? 'ladies' : 'men';
    $response = "We have $total $gender_term who match your choice! ";
    $response .= "We will send you details of 3 of them shortly.\n\n";

    foreach ($matches as $match) {
        $response .= "{$match['name']} aged {$match['Age']}, {$match['PhoneNumber']}.\n";
    }

    $response .= "\nTo get more details about someone, SMS their number e.g., 070817089 to 22141";

    echo json_encode(["message" => $response]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
