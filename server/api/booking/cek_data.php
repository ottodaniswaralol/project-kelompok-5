<?php
// Tampilkan error biar jelas
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
include '../../config/database.php';

// Cek koneksi
if (!isset($conn)) {
    echo json_encode(["status" => "error", "message" => "Koneksi DB Gagal"]);
    exit;
}

// Ambil SEMUA data booking
$result = $conn->query("SELECT * FROM booking ORDER BY start_datetime DESC");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "jumlah_data" => count($data),
    "isi_tabel" => $data
], JSON_PRETTY_PRINT);
?>