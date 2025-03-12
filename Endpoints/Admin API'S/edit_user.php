<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "penzi_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"));

if (isset($data->UserID) && isset($data->name) && isset($data->Age) && isset($data->Gender) && isset($data->County) && isset($data->Town) && isset($data->PhoneNumber)) {
    $userID = $data->UserID;
    $name = $conn->real_escape_string($data->name);
    $age = intval($data->Age);
    $gender = $conn->real_escape_string($data->Gender);
    $county = $conn->real_escape_string($data->County);
    $town = $conn->real_escape_string($data->Town);
    $phone = $conn->real_escape_string($data->PhoneNumber);

    $sql = "UPDATE users SET name='$name', Age=$age, Gender='$gender', County='$county', Town='$town', PhoneNumber='$phone' WHERE UserID=$userID";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        echo json_encode(["error" => "Error updating user: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Missing required fields"]);
}

$conn->close();
?>
