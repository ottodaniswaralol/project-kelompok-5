<?php
// 1. CORS HARDCODE (Biar frontend gak teriak error CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle Preflight Request (Browser suka nanya dulu sebelum kirim data beneran)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Koneksi Database
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    // 3. Ambil & Validasi Input (Dipaksa jadi Integer)
    $booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

    // 4. Logika Query Aman (Prepared Statement)
    if ($booking_id > 0) {
        // Kalau ada ID, cari spesifik
        $stmt = $conn->prepare("SELECT * FROM booking_approval WHERE booking_id = ? ORDER BY approval_date DESC");
        $stmt->bind_param("i", $booking_id);
    } else {
        // Kalau gak ada ID, ambil semua (dibatasi 100 biar ringan)
        $stmt = $conn->prepare("SELECT * FROM booking_approval ORDER BY approval_date DESC LIMIT 100");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}
?>