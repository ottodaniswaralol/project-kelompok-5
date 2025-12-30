<?php
// 1. Matikan semua error reporting biar gak ngerusak output JSON
error_reporting(0);
ini_set('display_errors', 0);

// 2. Header CORS WAJIB ADA (Pake bintang biar gak ribet dulu)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// 3. Handle Preflight Request (Ini yang bikin error di log lu)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ... Baris koneksi database dan logika login lu di sini ...
require_once '../../config/database.php';

// Ambil data JSON dari body
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode([
        "status" => false,
        "message" => "Username dan password wajib diisi"
    ]);
    exit;
}

// Query user
$stmt = $conn->prepare("
  SELECT * FROM users 
  WHERE username = ?
     OR name = ?
     OR name LIKE ?
");
$likeName = "%$username%";
$stmt->bind_param("sss", $username, $username, $likeName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => false,
        "message" => "Username tidak ditemukan"
    ]);
    exit;
}

$user = $result->fetch_assoc();

// Verifikasi password
if (!password_verify($password, $user['password'])) {
    echo json_encode([
        "status" => false,
        "message" => "Password salah"
    ]);
    exit;
}

// Login sukses
echo json_encode([
    "status" => true,
    "user" => [
        "user_id" => $user["user_id"],
        "name" => $user["name"],
        "role" => $user["role"]
    ]
]);

