<?php
// 1. CORS HARDCODE (Wajib!)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. MATIKAN ERROR DISPLAY (PENTING BANGET!)
// Supaya warning PHP gak ngerusak format JSON
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';

try {
    // 3. Validasi Koneksi
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Gagal koneksi database");
    }

    // 4. Ambil Input
    $date = $_GET['date'] ?? '';
    $roomRequested = $_GET['room'] ?? '';

    if (empty($date) || empty($roomRequested)) {
        echo json_encode(["status" => "error", "message" => "Pilih tanggal dan ruangan dulu!"]);
        exit;
    }

    // 5. Query Cek Bentrok
    // Ambil semua booking di tanggal itu, KECUALI yang sudah ditolak/batal
    $query = "
        SELECT b.rooms, ba.status 
        FROM booking b
        LEFT JOIN booking_approval ba ON b.booking_id = ba.booking_id
        WHERE DATE(b.start_datetime) = ? 
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $isBooked = false;
    $statusConflict = "";

    while ($row = $result->fetch_assoc()) {
        // Normalisasi status (handle jika NULL -> 'pending')
        $statusRaw = $row['status'] ?? 'pending';
        $status = strtolower($statusRaw);

        // Jika status Rejected/Cancelled, ruangan dianggap KOSONG (lanjut loop)
        if (in_array($status, ['rejected', 'cancelled', 'ditolak', 'batal'])) {
            continue;
        }

        // Cek Ruangan dalam JSON
        // Database mungkin simpan: "R1" (string) atau ["R1", "R2"] (array JSON)
        $roomsDB = $row['rooms'];
        $bookedRoomsArray = json_decode($roomsDB, true);

        // Fallback jika gagal decode atau bukan array (misal data lama cuma string)
        if (!is_array($bookedRoomsArray)) {
            $bookedRoomsArray = [$roomsDB];
        }

        // Cek bentrok
        if (in_array($roomRequested, $bookedRoomsArray)) {
            $isBooked = true;
            $statusConflict = $statusRaw; // Simpan status booking yang bikin bentrok
            break; // Udah ketemu bentrok, stop looping
        }
    }

    // 6. Response Final
    if ($isBooked) {
        echo json_encode([
            "status" => "success", // Request berhasil diproses
            "available" => false,  // TAPI ruangan tidak tersedia
            "message" => "Yah, ruangan penuh! (Status booking lain: $statusConflict)"
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "available" => true,
            "message" => "Aman bro, ruangan tersedia!"
        ]);
    }

} catch (Exception $e) {
    // Kalau ada error fatal, kirim JSON error
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server Error: " . $e->getMessage()]);
}
?>