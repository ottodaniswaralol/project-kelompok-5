<?php
require_once '../auth/cors.php'; 
require_once '../../config/database.php';

header('Content-Type: application/json');

// Ambil ID dari URL (?booking_id=...)
$booking_id = $_GET['booking_id'] ?? ''; 

if (empty($booking_id)) {
    $q = $conn->query("SELECT * FROM booking_approval"); // Kalau ID kosong, tampilin semua
} else {
    $q = $conn->query("SELECT * FROM booking_approval WHERE booking_id='$booking_id'");
}

$data = [];
while ($row = $q->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>