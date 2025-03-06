<?php
require '../config/db.php';
require '../auth_middleware.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$user = authenticate();

$target_phone = $_GET['phoneNumber'] ?? '';
$requester_phone = $_GET['requester'] ?? ''; 

if (empty($target_phone)) { 
    echo json_encode(["error" => "Target phone number is required"]);
    exit();
}

try {
    //Get target user's self-description
    $stmt = $conn->prepare("
        SELECT a.SelfDescription 
        FROM user_additional_details a
        JOIN users u ON a.UserID = u.UserID
        WHERE u.PhoneNumber = ?
    ");
    $stmt->bind_param("s", $target_phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $description = $result->fetch_assoc()['SelfDescription'] ?? null;
    $stmt->close();

    
    if (!empty($requester_phone)) {
        // Get UserIDs
        $stmt = $conn->prepare("
            SELECT u.UserID AS target_id, r.UserID AS requester_id
            FROM users u
            JOIN users r ON r.PhoneNumber = ?
            WHERE u.PhoneNumber = ?
        ");
        $stmt->bind_param("ss", $requester_phone, $target_phone);
        $stmt->execute();
        $ids = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($ids) {
            $insert_stmt = $conn->prepare("
                INSERT INTO matches 
                (UserID, MatchedUserID, MatchingDate)
                VALUES (?, ?, NOW())
            ");
            $insert_stmt->bind_param("ii", 
                $ids['requester_id'], 
                $ids['target_id']
            );
            $insert_stmt->execute();
            $insert_stmt->close();
        }
    }

    echo json_encode([
        "phone_number" => $target_phone,
        "self_description" => $description ?? "No description available"
    ]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>