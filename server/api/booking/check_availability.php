<?php
// 1. CORS Header
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Error Reporting off untuk production
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
require_once '../../config/database.php';

try {
    // 3. Tangkap Input
    $inputJSON = json_decode(file_get_contents("php://input"), true);
    
    $room = $inputJSON['room'] ?? $_GET['room'] ?? '';
    $date = $inputJSON['date'] ?? $_GET['date'] ?? ''; 
    
    // START & END JADI OPTIONAL
    $start = $inputJSON['start'] ?? $_GET['start'] ?? ''; 
    $end   = $inputJSON['end'] ?? $_GET['end'] ?? '';   

    // Validasi Cukup Room & Date saja
    if (empty($room) || empty($date)) {
        throw new Exception("Mohon pilih Ruangan dan Tanggal.");
    }

    // LOGIKA PENTING:
    // Kalau jam tidak dikirim frontend, kita set manual jadi SEHARIAN (00:00 - 23:59)
    if (empty($start) || empty($end)) {
        $startTime = "00:00:00";
        $endTime   = "23:59:59";
    } else {
        $startTime = $start;
        $endTime   = $end;
    }

    // Format jadi Y-m-d H:i:s untuk Query
    $reqStart = date('Y-m-d H:i:s', strtotime("$date $startTime"));
    $reqEnd   = date('Y-m-d H:i:s', strtotime("$date $endTime"));

    // 4. Query Cek Bentrok
    // Logika: Cari apakah ada booking AKTIF di rentang waktu tersebut
    $query = "
        SELECT b.booking_id
        FROM booking b
        LEFT JOIN booking_approval ba ON b.booking_id = ba.booking_id
        WHERE 
            b.rooms LIKE ? 
            AND (
                (b.start_datetime < ? AND b.end_datetime > ?) 
            )
            AND (ba.status IS NULL OR ba.status NOT IN ('rejected', 'cancelled', 'ditolak'))
        LIMIT 1
    ";

    $stmt = $conn->prepare($query);
    $roomPattern = "%\"$room\"%"; 
    $stmt->bind_param("sss", $roomPattern, $reqEnd, $reqStart);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conflict = $result->fetch_assoc();

    // 5. Kirim Response SESUAI Frontend Logic
    // Frontend kamu mengecek: if (json.status === 'booked')
    
    if ($conflict) {
        // Ada yang booking di tanggal itu
        echo json_encode([
            "status" => "booked", // <--- Sesuaikan string ini dengan frontend
            "message" => "Ruangan sudah penuh di tanggal ini."
        ]);
    } else {
        // Kosong
        echo json_encode([
            "status" => "available", // <--- Sesuaikan string ini dengan frontend
            "message" => "Ruangan tersedia, silakan booking!"
        ]);
    }

} catch (Exception $e) {
    http_response_code(200);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>