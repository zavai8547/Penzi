<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "penzi_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if (isset($_GET['id'])) {
    $userID = intval($_GET['id']);
    $sql = "DELETE FROM users WHERE UserID = $userID";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => "Error deleting user: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "User ID not provided"]);
}

$conn->close();
?>
