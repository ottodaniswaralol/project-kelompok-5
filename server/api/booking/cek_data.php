<?php
require_once '../auth/cors.php'; // Baris 1
require_once '../../config/database.php'; // Baris 2

header("Content-Type: application/json");

if (!isset($conn)) {
    echo json_encode(["status" => "error", "message" => "Koneksi DB Gagal"]);
    exit;
}

$result = $conn->query("SELECT * FROM booking ORDER BY start_datetime DESC");
$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(["jumlah_data" => count($data), "isi_tabel" => $data]);
?>