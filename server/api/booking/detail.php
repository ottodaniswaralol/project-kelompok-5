<?php
header('Content-Type: application/json');
include '../../config/database.php';

$id = $_GET['id'];

$q = $conn->query("SELECT * FROM booking WHERE booking_id='$id'");
$booking = $q->fetch_assoc();

// ROOMS
$r = $conn->query("SELECT rooms.room_id, room_name 
                   FROM booking_rooms 
                   JOIN rooms ON rooms.room_id = booking_rooms.room_id
                   WHERE booking_id='$id'");

$rooms = [];
while ($row = $r->fetch_assoc()) $rooms[] = $row;

// INVENTORY
$i = $conn->query("SELECT booking_inventory.quantity, item_name
                   FROM booking_inventory
                   JOIN inventory ON inventory.inventory_id = booking_inventory.inventory_id
                   WHERE booking_id='$id'");

$inv = [];
while ($row = $i->fetch_assoc()) $inv[] = $row;

// APPROVAL
$a = $conn->query("SELECT * FROM booking_approval WHERE booking_id='$id'");

$approval = [];
while ($row = $a->fetch_assoc()) $approval[] = $row;

echo json_encode([
    "booking" => $booking,
    "rooms" => $rooms,
    "inventory" => $inv,
    "approval" => $approval
]);
?>
