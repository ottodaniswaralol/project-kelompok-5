<?php
// 1. CORS Header
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Support POST/GET
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Matikan Error Text PHP (PENTING!)
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
require_once '../../config/database.php';

try {
    // 3. Tangkap Input (Bisa dari POST JSON atau GET URL)
    $inputJSON = json_decode(file_get_contents("php://input"), true);
    
    // Support input dari JSON Body atau URL Parameter
    $room   = $inputJSON['room'] ?? $_GET['room'] ?? '';
    $date   = $inputJSON['date'] ?? $_GET['date'] ?? '';     // Format: YYYY-MM-DD
    $start  = $inputJSON['start'] ?? $_GET['start'] ?? '';   // Format: HH:mm
    $end    = $inputJSON['end'] ?? $_GET['end'] ?? '';       // Format: HH:mm

    // Validasi Kelengkapan
    if (empty($room) || empty($date) || empty($start) || empty($end)) {
        throw new Exception("Mohon lengkapi Ruangan, Tanggal, Jam Mulai, dan Selesai.");
    }

    // Gabungkan Tanggal + Jam
    $reqStart = date('Y-m-d H:i:s', strtotime("$date $start"));
    $reqEnd   = date('Y-m-d H:i:s', strtotime("$date $end"));

    // 4. Query Cek Bentrok (Anti SQL Injection)
    // Logika: Cari booking lain yang waktunya tumpang tindih
    // DAN statusnya BUKAN 'rejected' atau 'cancelled'
    $query = "
        SELECT b.booking_id, b.start_datetime, b.end_datetime, ba.status 
        FROM booking b
        LEFT JOIN booking_approval ba ON b.booking_id = ba.booking_id
        WHERE 
            b.rooms LIKE ? 
            AND (
                (b.start_datetime < ? AND b.end_datetime > ?) -- Logika Overlap
            )
            AND (ba.status IS NULL OR ba.status NOT IN ('rejected', 'cancelled', 'ditolak'))
        LIMIT 1
    ";

    $stmt = $conn->prepare($query);
    $roomPattern = "%\"$room\"%"; // Mencari string room di dalam JSON/Array DB
    $stmt->bind_param("sss", $roomPattern, $reqEnd, $reqStart);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conflict = $result->fetch_assoc();

    // 5. Kirim Response JSON (Bukan Text!)
    if ($conflict) {
        // Ada bentrok
        echo json_encode([
            "status" => "success",
            "available" => false,
            "message" => "Yah, ruangan sudah terisi di jam segitu!"
        ]);
    } else {
        // Kosong (Aman)
        echo json_encode([
            "status" => "success",
            "available" => true,
            "message" => "Aman bro, ruangan tersedia!"
        ]);
    }

} catch (Exception $e) {
    // Error Server / Input
    http_response_code(200); // Tetap 200 biar frontend bisa baca message errornya
    echo json_encode([
        "status" => "error",
        "available" => false,
        "message" => $e->getMessage()
    ]);
}
?>