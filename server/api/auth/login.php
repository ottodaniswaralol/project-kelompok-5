<?php
// 1. Panggil CORS paling atas 
require_once 'cors.php'; 

// 2. Koneksi Database (Naik 2 tingkat ke folder config)
require_once '../../config/database.php';

header("Content-Type: application/json");

// Ambil data JSON dari body request
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => false, "message" => "Username & password wajib diisi"]);
    exit;
}

// Query User (Gunakan $conn dari database.php)
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR name = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => false, "message" => "User tidak ditemukan"]);
    exit;
}

$user = $result->fetch_assoc();

/**
 * PENTING: Jika password di database masih tulisan biasa (bukan hash),
 * ganti password_verify() menjadi: if ($password !== $user['password'])
 */
if (!password_verify($password, $user['password'])) {
    // Jika gagal login karena plain text, coba bandingkan langsung (Hanya untuk debug!)
    if ($password === $user['password']) {
        // Lanjut proses jika cocok manual
    } else {
        echo json_encode(["status" => false, "message" => "Password salah"]);
        exit;
    }
}

// Berhasil login
echo json_encode([
    "status" => true, 
    "user" => [
        "user_id" => $user["user_id"],
        "name" => $user["name"],
        "role" => $user["role"]
    ]
]);
?>