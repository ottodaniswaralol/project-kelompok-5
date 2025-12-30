<?php
// 1. Matikan error reporting biar gak ngerusak output JSON
error_reporting(0);
ini_set('display_errors', 0);

// 2. Header CORS & JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// 3. Handle Preflight Request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 4. Hubungkan ke database (Arahkan ke folder config)
require_once '../config/database.php';

// 5. Ambil data JSON
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => false, "message" => "Username dan password wajib diisi"]);
    exit;
}

// 6. Query User (Gunakan $conn dari database.php)
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR name = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => false, "message" => "Username tidak ditemukan"]);
    exit;
}

$user = $result->fetch_assoc();

// 7. Verifikasi Password
if (!password_verify($password, $user['password'])) {
    echo json_encode(["status" => false, "message" => "Password salah"]);
    exit;
}

// 8. Berhasil
echo json_encode([
    "status" => true, 
    "user" => [
        "user_id" => $user["user_id"],
        "name" => $user["name"],
        "role" => $user["role"]
    ]
]);
?>