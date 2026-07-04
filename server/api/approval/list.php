<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $role       = isset($_GET['role']) ? $_GET['role'] : '';
    $booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

    // Filter step berdasarkan role
    $step_filter = '';
    if ($role === 'marketing') {
        $step_filter = "AND ba.step = 'marketing'";
    } elseif ($role === 'bima') {
        $step_filter = "AND ba.step = 'bima'";
    } elseif ($role === 'ga') {
        $step_filter = "AND ba.step = 'ga'";
    }

    if ($booking_id > 0) {
        $sql = "SELECT ba.*, b.event_name, b.organization, b.start_datetime, b.end_datetime,
                       b.phone, b.event_description, b.memo_file, b.status AS booking_status,
                       b.recurring_group_id, u.name AS peminjam, u.role AS peminjam_role,
                       r.room_name
                FROM booking_approval ba
                JOIN booking b ON b.booking_id = ba.booking_id
                JOIN users u ON u.user_id = b.user_id
                JOIN booking_rooms br ON br.booking_id = b.booking_id
                JOIN rooms r ON r.room_id = br.room_id
                WHERE ba.booking_id = $booking_id
                ORDER BY ba.approved_at DESC";
    } else {
        $sql = "SELECT ba.*, b.event_name, b.organization, b.start_datetime, b.end_datetime,
                       b.phone, b.event_description, b.memo_file, b.status AS booking_status,
                       b.recurring_group_id, u.name AS peminjam, u.role AS peminjam_role,
                       r.room_name
                FROM booking_approval ba
                JOIN booking b ON b.booking_id = ba.booking_id
                JOIN users u ON u.user_id = b.user_id
                JOIN booking_rooms br ON br.booking_id = b.booking_id
                JOIN rooms r ON r.room_id = br.room_id
                WHERE 1=1 $step_filter
                ORDER BY ba.approved_at DESC
                LIMIT 100";
    }

$result = $conn->query($sql);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error, "sql" => $sql]);
        exit;
    }
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>