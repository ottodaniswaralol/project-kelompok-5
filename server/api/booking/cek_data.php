<?php
// === 1. FORCE CORS ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json; charset=UTF-8");

// === 2. KONEKSI DATABASE ===
$db_path = '../../config/database.php';

if (!file_exists($db_path)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "File database tidak ditemukan"]);
    exit();
}

require_once $db_path;

if (!isset($conn)) {
    echo json_encode(["status" => "error", "message" => "Koneksi DB Gagal (variable conn null)"]);
    exit;
}

// === 3. AMBIL DATA ===
$result = $conn->query("SELECT * FROM booking ORDER BY start_datetime DESC");

if (!$result) {
    echo json_encode(["status" => "error", "message" => "Query Error: " . $conn->error]);
    exit;
}

$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(["jumlah_data" => count($data), "isi_tabel" => $data]);
?>