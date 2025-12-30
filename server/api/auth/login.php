<?php
// 1. Izinkan akses dari mana saja (Origin Netlify lu)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// 2. TANGKAP REQUEST OPTIONS (PREFLIGHT)
// Ini kunci buat benerin error 'No Access-Control-Allow-Origin header'
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

// 3. Hubungkan ke database (Arahkan ke folder config)
require_once '../config/database.php';

// 4. Ambil data JSON
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => false, "message" => "Username dan password wajib diisi"]);
    exit;
}

// 5. Query User (Gunakan $conn dari database.php)
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR name = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => false, "message" => "Username tidak ditemukan"]);
    exit;
}

$user = $result->fetch_assoc();

// 6. Verifikasi Password
if (!password_verify($password, $user['password'])) {
    echo json_encode(["status" => false, "message" => "Password salah"]);
    exit;
}

// 7. Berhasil
echo json_encode([
    "status" => true, 
    "user" => [
        "user_id" => $user["user_id"],
        "name" => $user["name"],
        "role" => $user["role"]
    ]
]);
?>