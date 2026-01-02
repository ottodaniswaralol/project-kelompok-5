<?php
require_once '../auth/cors.php'; // Pakai file pusat
require_once '../../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $date = $_GET['date'] ?? '';
    $roomRequested = $_GET['room'] ?? '';

    if (empty($date) || empty($roomRequested)) throw new Exception("Data kurang");

    // Query cek jadwal yang tidak ditolak/batal
    $query = "SELECT rooms FROM booking 
              WHERE DATE(start_datetime) = ? 
              AND status NOT IN ('Ditolak', 'Batal', 'Rejected')";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $isBooked = false;
    while ($row = $result->fetch_assoc()) {
        $bookedRoomsArray = json_decode($row['rooms'], true);
        if (!is_array($bookedRoomsArray)) $bookedRoomsArray = [$row['rooms']];

        foreach ($bookedRoomsArray as $bookedRoom) {
            if (trim($bookedRoom) === trim($roomRequested)) {
                $isBooked = true;
                break 2;
            }
        }
    }

    echo json_encode([
        "status" => $isBooked ? "booked" : "available",
        "message" => $isBooked ? "Ruangan sudah terisi" : "Ruangan tersedia"
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>