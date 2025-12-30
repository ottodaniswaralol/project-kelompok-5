<?php
header('Content-Type: application/json');
include '../../config/database.php';

$booking_id = $_GET['booking_id'];

$q = $conn->query("SELECT * FROM booking_approval WHERE booking_id='$booking_id'");

$data = [];
while ($row = $q->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
