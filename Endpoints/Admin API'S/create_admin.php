<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=penzi_db", "root", "");

if (!isset($_SESSION["admin_role"]) || $_SESSION["admin_role"] !== "super_admin") {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (username, password, role) VALUES (:username, :password, 'admin')";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['username' => $username, 'password' => $password])) {
        echo json_encode(["success" => true, "message" => "Admin created successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to create admin"]);
    }
}
?>
