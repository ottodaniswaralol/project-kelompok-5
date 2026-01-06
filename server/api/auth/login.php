<?php
// ==========================================
// 1. BAGIAN CORS (WAJIB PALING ATAS)
// ==========================================

// Izinkan semua domain (Gunakan ini dulu biar tidak pusing saat dev)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Tangani Preflight Request (Browser Cek Ombak)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit; // Stop di sini, jangan lanjut ke bawah
}

// ==========================================
// 2. BAGIAN LOGIC LOGIN
// ==========================================

header("Content-Type: application/json");

// Cek path database kamu. Pastikan ../ nya sudah benar sesuai struktur folder
// Gunakan include, bukan require_once, biar kalau error path-nya ketahuan
include '../../config/database.php'; 

// Cek koneksi database (Optional, buat debugging)
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

// PENTING: Pilih salah satu metode di bawah ini sesuai isi database kamu

// METODE A: Jika password di database sudah di-hash (pake password_hash)
if (!password_verify($password, $user['password'])) {
    echo json_encode(["status" => false, "message" => "Password salah (Hash)"]);
    exit;
}

/* // METODE B: Jika password di database masih POLOS (Plain Text)
// Uncomment baris di bawah ini dan matikan METODE A jika passwordmu belum dienkripsi
if ($password !== $user['password']) {
    echo json_encode(["status" => false, "message" => "Password salah (Plain)"]);
    exit;
}
*/

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
?>