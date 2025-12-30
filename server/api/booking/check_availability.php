<?php
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../../config/database.php';

try {
    if (!isset($conn)) throw new Exception("DB Error");

    $date = $_GET['date'] ?? '';
    $roomRequested = $_GET['room'] ?? '';

    if (empty($date) || empty($roomRequested)) throw new Exception("Data kurang");

    // PERBAIKAN: Pakai 'booking_id' (bukan id)
    $query = "SELECT booking_id, rooms FROM booking 
              WHERE DATE(start_datetime) = ? 
              AND status NOT IN ('Ditolak', 'Batal', 'Rejected')";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $isBooked = false;

    while ($row = $result->fetch_assoc()) {
        // Decode JSON rooms
        $bookedRoomsArray = json_decode($row['rooms'], true);
        if (!is_array($bookedRoomsArray)) $bookedRoomsArray = [$row['rooms']];

        // Cek bentrok text
        foreach ($bookedRoomsArray as $bookedRoom) {
            if (trim($bookedRoom) === trim($roomRequested)) {
                $isBooked = true;
                break 2;
            }
        }
    }

    if ($isBooked) {
        echo json_encode(["status" => "booked", "message" => "Penuh"]);
    } else {
        echo json_encode(["status" => "available", "message" => "Aman"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>