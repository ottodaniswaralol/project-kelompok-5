<?php
// 1. CORS & Error Handling
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// MATIKAN DISPLAY ERROR (Biar JSON gak rusak)
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json; charset=UTF-8");
require_once '../../config/database.php';

try {
    if (!isset($conn)) throw new Exception("Koneksi Database Gagal");

    // 2. Ambil Data (Dibatasi 100 data terakhir biar ringan)
    $sql = "SELECT * FROM booking ORDER BY start_datetime DESC LIMIT 100";
    $result = $conn->query($sql);

    $data = [];
    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
    }

    echo json_encode([
        "status" => "success",
        "total" => count($data),
        "data" => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>