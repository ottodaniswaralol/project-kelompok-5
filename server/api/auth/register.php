<?php
// 1. WAJIB: Panggil CORS paling atas
require_once 'cors.php'; 

// 2. Database
require_once '../../config/database.php';

header('Content-Type: application/json');

// 3. Baca data JSON dari React (karena $_POST sering kosong di API)
$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? '';
$username = $data['username'] ?? '';
$password = isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : '';
$role = $data['role'] ?? 'mahasiswa';

if (empty($name) || empty($username) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit;
}

// 4. Pake Prepared Statement biar aman dari SQL Injection 
$stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $username, $password, $role);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>