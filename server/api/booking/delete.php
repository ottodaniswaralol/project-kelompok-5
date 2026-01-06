<?php
// === 1. FORCE CORS ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

// === 2. KONEKSI DATABASE ===
$db_path = '../../config/database.php';

if (!file_exists($db_path)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "File database tidak ditemukan"]);
    exit();
}

require_once $db_path;

// === 3. LOGIC DELETE ===
$input = file_get_contents("php://input");
$data = json_decode($input, true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "ID Kosong. Data input: " . $input]);
    exit;
}

// Disable FK Check (Opsional, hati-hati pakai ini)
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

$stmt = $conn->prepare("DELETE FROM booking WHERE booking_id = ?"); // Pastikan nama kolom primary key benar 'booking_id' atau 'id'
if (!$stmt) {
     echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
     exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Data berhasil dihapus"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal delete: " . $stmt->error]);
}

$conn->query("SET FOREIGN_KEY_CHECKS = 1");
?>