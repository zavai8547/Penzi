<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$servername = "database";
$username = "root";  
$password = "rodney";      
$database = "penzi_db";


$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT MatchRequestID, UserID, AgeRange, Town, RequestDate FROM matchrequests";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $matchRequests = [];
    while ($row = $result->fetch_assoc()) {
        $matchRequests[] = $row;
    }
    echo json_encode($matchRequests);
} else {
    echo json_encode([]);
}

$conn->close();
?>
