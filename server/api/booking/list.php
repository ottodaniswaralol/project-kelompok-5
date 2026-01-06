<?php
// 1. CORS HARDCODE
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Matikan display error agar JSON bersih
ini_set('display_errors', 0);

// 3. Koneksi Database
require_once '../../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    // Ambil user_id
    $user_id = $_GET['user_id'] ?? 0;

    if ($user_id == 0) {
        echo json_encode([]);
        exit;
    }

    // Query Booking + Status Approval Terakhir
    $query = "
        SELECT 
            b.*,
            COALESCE(
                (SELECT status FROM booking_approval ba 
                 WHERE ba.booking_id = b.booking_id 
                 ORDER BY ba.approval_id DESC LIMIT 1), 
                'pending'
            ) as real_status
        FROM booking b
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        
        // Rapikan JSON Room
        $roomRaw = $row['rooms'];
        $jsonRoom = json_decode($roomRaw, true);
        $roomClean = is_array($jsonRoom) ? implode(", ", $jsonRoom) : $roomRaw;

        // Rapikan Status
        $statusIndo = 'Menunggu';
        if ($row['real_status'] == 'approved') $statusIndo = 'Disetujui';
        if ($row['real_status'] == 'rejected') $statusIndo = 'Ditolak';

        $data[] = [
            "id" => $row['booking_id'], 
            "event" => $row['event_name'],
            "org" => $row['organization'],
            "date" => date('d-m-Y', strtotime($row['start_datetime'])),
            "time" => date('H:i', strtotime($row['start_datetime'])) . ' - ' . date('H:i', strtotime($row['end_datetime'])),
            "room" => $roomClean,
            "notes" => $row['event_description'],
            "status" => $statusIndo
        ];
    }

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>