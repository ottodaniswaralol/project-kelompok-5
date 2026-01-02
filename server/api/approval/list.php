<?php
// Keluar dari approval/, keluar dari api/, masuk ke auth/ 
require_once '../auth/cors.php'; 
require_once '../../config/database.php';

header('Content-Type: application/json');

// Ambil booking_id dari URL (contoh: list.php?booking_id=10)
$booking_id = $_GET['booking_id'] ?? '';

if (empty($booking_id)) {
    echo json_encode([]);
    exit;
}

// Pake Prepared Statement biar gak error kalau ada karakter aneh 
$stmt = $conn->prepare("SELECT * FROM booking_approval WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
