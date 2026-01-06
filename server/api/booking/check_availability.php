<?php
// 1. CORS HARDCODE
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Koneksi Database
require_once '../../config/database.php'; 

header("Content-Type: application/json; charset=UTF-8");

$date = $_GET['date'] ?? '';
$roomRequested = $_GET['room'] ?? '';

if (empty($date) || empty($roomRequested)) {
    echo json_encode(["status" => "error", "message" => "Data kurang"]);
    exit;
}

// Cek booking yang statusnya BUKAN Ditolak/Batal
$query = "SELECT rooms FROM booking WHERE DATE(start_datetime) = ? AND status NOT IN ('Ditolak', 'Batal', 'Rejected')";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$isBooked = false;
while ($row = $result->fetch_assoc()) {
    $bookedRoomsArray = json_decode($row['rooms'], true) ?: [$row['rooms']];
    foreach ($bookedRoomsArray as $bookedRoom) {
        if (trim($bookedRoom) === trim($roomRequested)) { $isBooked = true; break 2; }
    }
}

echo json_encode(["status" => $isBooked ? "booked" : "available"]);
?>