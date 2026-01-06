<?php
// === 1. FORCE CORS ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json; charset=UTF-8");

// === 2. DEBUGGING ===
ini_set('display_errors', 1);
error_reporting(E_ALL);

// === 3. KONEKSI DATABASE ===
$db_path = '../../config/database.php'; 

if (!file_exists($db_path)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database file not found at: " . realpath('../../config/')]);
    exit();
}

require_once $db_path;

if (!isset($conn) || !$conn) {
    echo json_encode(["status" => "error", "message" => "Koneksi DB Gagal"]);
    exit;
}

// === 4. LOGIC ===
$date = $_GET['date'] ?? '';
$roomRequested = $_GET['room'] ?? '';

if (empty($date) || empty($roomRequested)) {
    echo json_encode(["status" => "error", "message" => "Harap pilih Tanggal dan Ruangan terlebih dahulu"]);
    exit;
}

// Query dengan JOIN untuk cek status approval
// Kita gunakan b.booking_id (sesuai delete.php kamu). 
// JIKA ERROR SQL, ubah b.booking_id menjadi b.id
$query = "
    SELECT b.rooms 
    FROM booking b
    JOIN booking_approval ba ON b.booking_id = ba.booking_id
    WHERE DATE(b.start_datetime) = ? 
    AND ba.status NOT IN ('Ditolak', 'Batal', 'Rejected')
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Query Error: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$isBooked = false;

while ($row = $result->fetch_assoc()) {
    $roomsDB = $row['rooms'];
    
    // Handle format JSON array vs String biasa
    $bookedRoomsArray = json_decode($roomsDB, true);
    if (!is_array($bookedRoomsArray)) {
        $bookedRoomsArray = [$roomsDB];
    }

    if (in_array($roomRequested, $bookedRoomsArray)) {
        $isBooked = true;
        break; 
    }
}

// === RESPONSE FINAL YANG LEBIH LENGKAP ===
if ($isBooked) {
    echo json_encode([
        "status" => "success", 
        "available" => false, 
        "message" => "Ruangan sudah terisi pada tanggal tersebut."
    ]);
} else {
    echo json_encode([
        "status" => "success", 
        "available" => true, 
        "message" => "Ruangan tersedia!"
    ]);
}
?>