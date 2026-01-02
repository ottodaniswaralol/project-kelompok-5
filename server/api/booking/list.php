<?php
// 1. Panggil CORS paling atas agar ijin akses terkirim
require_once '../auth/cors.php'; 

// 2. Matikan display error agar tidak merusak format JSON
ini_set('display_errors', 0);

// 3. Koneksi Database
require_once '../../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    // Ambil user_id dari URL (contoh: list.php?user_id=5)
    $user_id = $_GET['user_id'] ?? 0;

    if ($user_id == 0) {
        echo json_encode([]);
        exit;
    }

    // QUERY SAKTI: Ambil data booking + status terbaru dari tabel approval
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
        
        // Rapikan JSON Room (Karena di DB disimpan sebagai string JSON)
        $roomRaw = $row['rooms'];
        $jsonRoom = json_decode($roomRaw, true);
        $roomClean = is_array($jsonRoom) ? implode(", ", $jsonRoom) : $roomRaw;

        // Rapikan Status ke Bahasa Indonesia untuk UI Frontend
        $statusIndo = 'Menunggu';
        if ($row['real_status'] == 'approved') $statusIndo = 'Disetujui';
        if ($row['real_status'] == 'rejected') $statusIndo = 'Ditolak';

        // Format data agar sesuai dengan kebutuhan state di React
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