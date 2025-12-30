<?php
header('Content-Type: application/json');
include '../../config/database.php';

$booking_id = $_POST['booking_id'];
$user_id = $_POST['user_id'];
$rating = $_POST['rating'];
$message = $_POST['message'];

$sql = "INSERT INTO booking_feedback(booking_id, user_id, rating, message)
        VALUES ('$booking_id', '$user_id', '$rating', '$message')";

$conn->query($sql);

echo json_encode(["status" => "success"]);
?>
