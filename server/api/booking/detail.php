<?php
require_once '../auth/cors.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

if ($id == 0) {
    echo json_encode(["status" => "error", "message" => "ID tidak valid"]);
    exit;
}

// 1. Ambil Data Utama Booking
$q = $conn->query("SELECT * FROM booking WHERE booking_id='$id'");
$booking = $q->fetch_assoc();

// 2. Ambil List Ruangan
$r = $conn->query("SELECT rooms.room_id, room_name 
                   FROM booking_rooms 
                   JOIN rooms ON rooms.room_id = booking_rooms.room_id
                   WHERE booking_id='$id'");
$rooms = [];
while ($row = $r->fetch_assoc()) $rooms[] = $row;

// 3. Ambil List Inventaris
$i = $conn->query("SELECT booking_inventory.quantity, item_name
                   FROM booking_inventory
                   JOIN inventory ON inventory.inventory_id = booking_inventory.inventory_id
                   WHERE booking_id='$id'");
$inv = [];
while ($row = $i->fetch_assoc()) $inv[] = $row;

// 4. Ambil Status Approval
$a = $conn->query("SELECT * FROM booking_approval WHERE booking_id='$id'");
$approval = [];
while ($row = $a->fetch_assoc()) $approval[] = $row;

echo json_encode([
    "status" => "success",
    "data" => [
        "booking" => $booking,
        "rooms" => $rooms,
        "inventory" => $inv,
        "approval" => $approval
    ]
]);
?>
