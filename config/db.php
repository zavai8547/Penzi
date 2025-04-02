<?php
$servername = "database"; 
$username = "root"; 
$password = "";     
$dbname = "penzi_db";     

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
} 
?>
