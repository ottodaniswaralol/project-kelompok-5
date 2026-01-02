<?php
require_once '../auth/cors.php'; 
require_once '../../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $data = json_decode(file_get_contents("php://input"), true); // Baca JSON
    
    $user_id = $data['user_id'] ?? 1;
    $event_name = $data['event_name'] ?? '';
    $org = $data['organization'] ?? '';
    $rooms_json = json_encode($data['rooms'] ?? []); // Simpan array room sebagai JSON

    $conn->begin_transaction();

    $sql1 = "INSERT INTO booking (user_id, event_name, organization, rooms, start_datetime, end_datetime) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("isssss", $user_id, $event_name, $org, $rooms_json, $data['start_datetime'], $data['end_datetime']);
    $stmt1->execute();
    
    $new_id = $conn->insert_id;
    $conn->query("INSERT INTO booking_approval (booking_id, step, status) VALUES ($new_id, 'baa', 'pending')");

    $conn->commit();
    echo json_encode(["status" => "success", "id" => $new_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>