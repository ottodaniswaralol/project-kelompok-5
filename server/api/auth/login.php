<?php
require_once 'cors.php'; // Paling atas!
require_once '../../config/database.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => false, "message" => "Isi data lengkap"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR name = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => false, "message" => "User tidak ditemukan"]);
    exit;
}

$user = $result->fetch_assoc();

// Jika di database lu passwordnya belum di-hash, bandingkan langsung:
// if ($password !== $user['password'])
if (!password_verify($password, $user['password'])) {
    echo json_encode(["status" => false, "message" => "Password salah"]);
    exit;
}

echo json_encode(["status" => true, "user" => ["user_id" => $user["user_id"], "name" => $user["name"], "role" => $user["role"]]]);
?>