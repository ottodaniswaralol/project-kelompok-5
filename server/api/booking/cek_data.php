<?php
// 1. CORS HARDCODE
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. MATIKAN ERROR DISPLAY (Wajib biar JSON bersih)
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';

try {
    // 3. Validasi Koneksi
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Koneksi DB Gagal");
    }

    // 4. Query (Ambil 200 data terakhir aja biar ringan)
    // Gak perlu Prepared Statement karena gak ada input user (variable) di query ini
    $sql = "SELECT * FROM booking ORDER BY start_datetime DESC LIMIT 200";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Query Error: " . $conn->error);
    }

    // fetch_all(MYSQLI_ASSOC) butuh driver mysqlnd. 
    // Kalau server lo jadul & error di baris ini, ganti jadi while loop.
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // 5. Output Standar
    echo json_encode([
        "status" => "success",
        "total" => count($data),
        "data" => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Server Error: " . $e->getMessage()
    ]);
}
?>
?>