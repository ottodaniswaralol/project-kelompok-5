<?php
// === 1. FORCE CORS (Wajib di awal) ===
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

// === 3. KONEKSI DATABASE AMAN ===
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
    echo json_encode(["status" => "error", "message" => "Parameter tanggal atau ruangan kurang"]);
    exit;
}

// Query untuk mencari booking yang bentrok (Status selain Ditolak/Batal)
$query = "SELECT rooms FROM booking WHERE DATE(start_datetime) = ? AND status NOT IN ('Ditolak', 'Batal', 'Rejected')";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Query Error: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$isBooked = false;

// Loop semua booking di tanggal itu
while ($row = $result->fetch_assoc()) {
    // Decode JSON ruangan dari database
    // Handle format lama (string biasa) dan format baru (JSON Array)
    $roomsDB = $row['rooms'];
    
    // Coba decode JSON
    $bookedRoomsArray = json_decode($roomsDB, true);
    
    // Jika gagal decode (berarti format lama string biasa), jadikan array manual
    if (!is_array($bookedRoomsArray)) {
        $bookedRoomsArray = [$roomsDB];
    }

    // Cek apakah ruangan yang diminta ada di dalam daftar ruangan yang sudah dibooking
    if (in_array($roomRequested, $bookedRoomsArray)) {
        $isBooked = true;
        break; // Ketemu bentrok, stop searching
    }
}

echo json_encode(["available" => !$isBooked]);
?>