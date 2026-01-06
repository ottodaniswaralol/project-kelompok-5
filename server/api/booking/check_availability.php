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
    echo json_encode(["status" => "error", "message" => "Database file not found"]);
    exit();
}

require_once $db_path;

if (!isset($conn) || !$conn) {
    echo json_encode(["status" => "error", "message" => "Koneksi DB Gagal"]);
    exit;
}

// === 4. AMBIL INPUT ===
$date = $_GET['date'] ?? '';
$roomRequested = $_GET['room'] ?? '';

if (empty($date) || empty($roomRequested)) {
    echo json_encode(["status" => "error", "message" => "Pilih Tanggal dan Ruangan dulu bro!"]);
    exit;
}

// === 5. QUERY SUPER AMAN (LEFT JOIN) ===
// Kita pakai LEFT JOIN agar data booking yang belum ada status approval-nya TETAP KEAMBIL
// Asumsi: Primary Key tabel booking adalah 'booking_id'
$query = "
    SELECT b.rooms, ba.status 
    FROM booking b
    LEFT JOIN booking_approval ba ON b.booking_id = ba.booking_id
    WHERE DATE(b.start_datetime) = ? 
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
$bookedBy = ""; // Untuk debugging: Siapa yang pakai?

while ($row = $result->fetch_assoc()) {
    // 1. Cek Status Approval
    // Kalau status NULL (gak ada di tabel approval), kita anggap 'pending' (Booking Tetap Berlaku)
    $status = $row['status'] ?? 'pending';
    
    // Kalau statusnya Ditolak/Batal, berarti ruangan BEBAS (Skip loop ini)
    if (in_array(strtolower($status), ['ditolak', 'batal', 'rejected', 'cancelled'])) {
        continue; 
    }

    // 2. Cek Ruangan
    $roomsDB = $row['rooms'];
    
    // Decode JSON (misal ["Ruang A"]) atau String biasa ("Ruang A")
    $bookedRoomsArray = json_decode($roomsDB, true);
    if (!is_array($bookedRoomsArray)) {
        $bookedRoomsArray = [$roomsDB];
    }

    // Cek apakah ruangan yang diminta ada di daftar booking ini
    if (in_array($roomRequested, $bookedRoomsArray)) {
        $isBooked = true;
        // Kita catat statusnya buat info di frontend
        $bookedBy = "Status: " . ($status ?: 'pending'); 
        break; // Ketemu bentrok! Stop cari.
    }
}

// === 6. RESPONSE FINAL ===
if ($isBooked) {
    echo json_encode([
        "status" => "success", 
        "available" => false, 
        "message" => "Yah, ruangan sudah terisi pada tanggal ini! ($bookedBy)"
    ]);
} else {
    echo json_encode([
        "status" => "success", 
        "available" => true, 
        "message" => "Aman bro, ruangan tersedia!"
    ]);
}
?>