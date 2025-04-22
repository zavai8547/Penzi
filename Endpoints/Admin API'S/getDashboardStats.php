<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$host = "database";
$user = "root";
$pass = "rodney";
$dbname = "penzi_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

function fetchCount($conn, $query) {
    $result = $conn->query($query);
    if (!$result) {
        die(json_encode(["error" => "SQL Error: " . $conn->error]));
    }
    return $result->fetch_assoc()["count"];
}

// Fetch total users
$totalUsers = fetchCount($conn, "SELECT COUNT(*) AS count FROM users");
// Fetch active interests
$activeInterests = fetchCount($conn, "SELECT COUNT(*) AS count FROM userconfirmations WHERE ConfirmationStatus= 'Yes'");
// Fetch total match requests
$totalMatchRequests = fetchCount($conn, "SELECT COUNT(*) AS count FROM matchrequests");
// Fetch total matches
$totalMatches = fetchCount($conn, "SELECT COUNT(*) AS count FROM matches");

$conn->close();

// Return JSON response
echo json_encode([
    "totalUsers" => $totalUsers,
    "activeInterests" => $activeInterests,
    "totalMatchRequests" => $totalMatchRequests,
    "totalMatches" => $totalMatches
]);
?>