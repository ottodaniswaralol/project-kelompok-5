<?php
header('Content-Type: application/json');
include '../../config/database.php';

$name = $_POST['name'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

$sql = "INSERT INTO users(name, username, password, role)
        VALUES ('$name', '$username', '$password', '$role')";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>
