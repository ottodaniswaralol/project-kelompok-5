<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $month  = isset($_GET['month'])  ? intval($_GET['month'])  : 0;
    $year   = isset($_GET['year'])   ? intval($_GET['year'])   : 0;

    $where = "WHERE 1=1";
    if ($status) $where .= " AND b.status = '$status'";
    if ($month)  $where .= " AND MONTH(b.start_datetime) = $month";
    if ($year)   $where .= " AND YEAR(b.start_datetime) = $year";

    $sql = "SELECT b.booking_id, b.event_name, b.organization, b.phone,
                   b.start_datetime, b.end_datetime, b.status, b.created_at,
                   b.recurring_group_id, b.event_description,
                   u.name AS peminjam, u.role AS peminjam_role,
                   r.room_name
            FROM booking b
            JOIN users u ON u.user_id = b.user_id
            JOIN booking_rooms br ON br.booking_id = b.booking_id
            JOIN rooms r ON r.room_id = br.room_id
            $where
            ORDER BY b.created_at DESC
            LIMIT 200";

    $result = $conn->query($sql);

    if (!$result) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
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