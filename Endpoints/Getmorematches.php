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


if (!isset($data['trigger']) || $data['trigger'] !== "NEXT") {
    echo json_encode(["error" => "Invalid trigger"]);
    exit();
}


$age = isset($data['age']) ? intval($data['age']) : null;
$town = isset($data['town']) ? $data['town'] : null;

// Pagination logic (cycles of 3)
$page = isset($data['page']) ? intval($data['page']) : 1;
$limit = 3;
$offset = ($page - 1) * $limit;

try {
    
    $query = "SELECT Name, Age, Gender, County, Town, PhoneNumber AS Phone FROM users WHERE 1=1";
    $params = [];
    $types = "";

    if ($age) {
        $query .= " AND Age = ?";
        $params[] = $age;
        $types .= "i";
    }

    if ($town) {
        $query .= " AND Town = ?";
        $params[] = $town;
        $types .= "s";
    }

    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $matches = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($matches)) {
        echo json_encode(["message" => "We are sorry, we do not have matches at the moment. Try a different age or Town to get your mpenzi."]);
    } else {
        echo json_encode([
            "matches" => $matches,
            "next_page" => $page + 1
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
