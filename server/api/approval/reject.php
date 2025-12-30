<?php
header('Content-Type: application/json');
include '../../config/database.php';

$approval_id = $_POST['approval_id'];
$approver_id = $_POST['approver_id'];
$notes = $_POST['notes'];

$sql = "UPDATE booking_approval 
       SET status='rejected', approver_id='$approver_id', notes='$notes', approved_at=NOW()
       WHERE approval_id='$approval_id'";

$conn->query($sql);
echo json_encode(["status" => "success"]);
?>
