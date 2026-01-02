<?php
require_once '../auth/cors.php'; // Baris 1
require_once '../../config/database.php'; // Baris 2

header("Content-Type: application/json; charset=UTF-8");

$date = $_GET['date'] ?? '';
$roomRequested = $_GET['room'] ?? '';

if (empty($date) || empty($roomRequested)) {
    echo json_encode(["status" => "error", "message" => "Data kurang"]);
    exit;
}

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