<?php
// 1. Ganti header manual dengan cors.php
require_once '../auth/cors.php'; 
require_once '../../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    // 2. Ambil Data format JSON (Bukan $_POST)
    $data = json_decode(file_get_contents("php://input"), true);
    
    $user_id = $data['user_id'] ?? 1;
    $event_name = $data['event_name'] ?? '';
    $org = $data['organization'] ?? '';
    $pic = $data['pic'] ?? '';
    $phone = $data['phone'] ?? '';
    $desc = $data['event_description'] ?? '';
    $start_datetime = $data['start_datetime'] ?? '';
    $end_datetime = $data['end_datetime'] ?? '';
    $rooms_json = json_encode($data['rooms'] ?? []); // Pastikan jadi string JSON buat DB

    $conn->begin_transaction();

    $sql1 = "INSERT INTO booking (user_id, event_name, organization, pic, phone, event_description, start_datetime, end_datetime, rooms) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("issssssss", $user_id, $event_name, $org, $pic, $phone, $desc, $start_datetime, $end_datetime, $rooms_json);
    
    if (!$stmt1->execute()) throw new Exception("Gagal insert booking");
    
    $new_booking_id = $conn->insert_id;

    $sql2 = "INSERT INTO booking_approval (booking_id, step, status) VALUES (?, 'baa', 'pending')";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $new_booking_id);
    
    if (!$stmt2->execute()) throw new Exception("Gagal insert approval");

    $conn->commit();
    echo json_encode(["status" => "success", "id" => $new_booking_id]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>