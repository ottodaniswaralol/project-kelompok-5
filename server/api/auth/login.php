<?php
// Izinkan domain Netlify lu buat ngakses API ini
header("Access-Control-Allow-Origin: https://project-kelompok-5.netlify.app");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Browser bakal kirim request 'OPTIONS' dulu buat nanya izin, jawab 200 OK
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
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

