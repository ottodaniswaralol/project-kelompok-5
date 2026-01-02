<?php
require_once '../auth/cors.php'; // WAJIB
require_once '../../config/database.php';

header("Content-Type: application/json");

try {
    // Ambil SEMUA data booking
    $result = $conn->query("SELECT * FROM booking ORDER BY start_datetime DESC");

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "jumlah_data" => count($data),
        "isi_tabel" => $data
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>