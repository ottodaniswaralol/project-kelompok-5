<?php
// ==========================================
// 1. HEADER CORS "PAKSA" (Wajib di Paling Atas)
// ==========================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Matikan Preflight Request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==========================================
// 2. BAGIAN LOGIC LOGIN
// ==========================================

header("Content-Type: application/json");

// Include database (Pastikan path ../ benar)
include '../../config/database.php'; 

// Cek koneksi database
if (!isset($conn)) {
    echo json_encode(["status" => false, "message" => "Koneksi Database Gagal"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => false, "message" => "Isi data lengkap"]);
    exit;
}

// Query ke database
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR name = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => false, "message" => "User tidak ditemukan"]);
    exit;
}

$user = $result->fetch_assoc();

// ==========================================
// 3. VERIFIKASI PASSWORD
// ==========================================

// Gunakan password_verify jika di DB ter-hash
if (!password_verify($password, $user['password'])) {
    // Fallback: Cek plain text (hanya untuk development/migrasi)
    // Hapus blok 'else if' ini jika semua user sudah di-hash
    if ($password !== $user['password']) {
        echo json_encode(["status" => false, "message" => "Password salah"]);
        exit;
    }
}

// ==========================================
// 4. SUKSES
// ==========================================

echo json_encode([
    "status" => true, 
    "user" => [
        "user_id" => $user["user_id"], 
        "name" => $user["name"], 
        "role" => $user["role"]
    ]
]);
?>