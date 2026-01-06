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

// === 4. LOGIC CHECK AVAILABILITY ===
$date = $_GET['date'] ?? '';
$roomRequested = $_GET['room'] ?? '';

if (empty($date) || empty($roomRequested)) {
    echo json_encode(["status" => "error", "message" => "Parameter tanggal atau ruangan kurang"]);
    exit;
}

// === QUERY BARU (JOIN TABLE) ===
// Kita ambil data ruangan dari tabel booking (b)
// Tapi kita cek statusnya di tabel booking_approval (ba)
// ASUMSI: Nama Primary Key di tabel booking adalah 'booking_id' (sesuai file delete.php kamu)
// JIKA ERROR "Unknown column 'b.booking_id'", GANTI JADI 'b.id'

$query = "
    SELECT b.rooms 
    FROM booking b
    JOIN booking_approval ba ON b.booking_id = ba.booking_id
    WHERE DATE(b.start_datetime) = ? 
    AND ba.status NOT IN ('Ditolak', 'Batal', 'Rejected')
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    // Tampilkan error query biar gampang debug
    echo json_encode(["status" => "error", "message" => "Query Error: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$isBooked = false;

// Loop semua booking di tanggal itu
while ($row = $result->fetch_assoc()) {
    $roomsDB = $row['rooms'];
    
    // Coba decode JSON
    $bookedRoomsArray = json_decode($roomsDB, true);
    
    // Jika format lama (bukan JSON), jadikan array manual
    if (!is_array($bookedRoomsArray)) {
        $bookedRoomsArray = [$roomsDB];
    }

    // Cek bentrok
    if (in_array($roomRequested, $bookedRoomsArray)) {
        $isBooked = true;
        break; 
    }
}

echo json_encode(["available" => !$isBooked]);
?>