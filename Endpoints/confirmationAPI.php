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

// Validate input
if (!isset($data['response'], $data['request_id'], $data['target_phone'])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

$response = strtoupper($data['response']);
$request_id = intval($data['request_id']);
$target_phone = $data['target_phone'];

if (!in_array($response, ['YES', 'NO'])) {
    echo json_encode(["error" => "Invalid response. Use YES/NO"]);
    exit();
}

try {
    // Get target user's ID
    $stmt = $conn->prepare("SELECT UserID FROM users WHERE PhoneNumber = ?");
    $stmt->bind_param("s", $target_phone);
    $stmt->execute();
    $target = $stmt->get_result()->fetch_assoc();
    $target_id = $target['UserID'] ?? null;
    $stmt->close();

    if (!$target_id) {
        echo json_encode(["error" => "Target user not found"]);
        exit();
    }

    // Get original request details
    $stmt = $conn->prepare("
        SELECT UserID AS requester_id 
        FROM matchrequests 
        WHERE MatchRequestID = ?
    ");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$request) {
        echo json_encode(["error" => "Invalid match request"]);
        exit();
    }
    $requester_id = $request['requester_id'];

    // Store confirmation
    $stmt = $conn->prepare("
        INSERT INTO userconfirmations 
        (request_id, UserID, ConfirmationStatus, Timestamp)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("iis", $request_id, $target_id, $response);
    $stmt->execute();
    $stmt->close();

    // Check for mutual interest
    $is_mutual = false;
    if ($response === 'YES') {
        $check_stmt = $conn->prepare("
            SELECT * FROM userconfirmations 
            WHERE request_id = ? 
            AND UserID = ?
            AND ConfirmationStatus = 'YES'
        ");
        $check_stmt->bind_param("ii", $request_id, $requester_id);
        $check_stmt->execute();
        $is_mutual = $check_stmt->get_result()->num_rows > 0;
        $check_stmt->close();
    }

    // Prepare response
    if ($is_mutual) {
        // Get requester's contact info
        $contact_stmt = $conn->prepare("
            SELECT PhoneNumber FROM users WHERE UserID = ?
        ");
        $contact_stmt->bind_param("i", $requester_id);
        $contact_stmt->execute();
        $contact = $contact_stmt->get_result()->fetch_assoc()['PhoneNumber'];
        $contact_stmt->close();

        echo json_encode([
            "status" => "mutual",
            "message" => "Mutual interest confirmed! Contact: $contact"
        ]);
    } else {
        echo json_encode([
            "status" => $response === 'YES' ? "pending" : "rejected",
            "message" => $response === 'YES' 
                ? "Interest recorded. We'll notify you if mutual." 
                : "Interest declined."
        ]);
    }

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>