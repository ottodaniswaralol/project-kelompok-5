<?php
// 1. CORS HARDCODE
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Detail biasanya cuma butuh GET
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    // 2. Validasi ID (Paksa jadi Integer)
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id == 0) {
        echo json_encode(["status" => "error", "message" => "ID tidak valid"]);
        exit;
    }

    // 3. Ambil Data Utama Booking (Prepared Statement)
    $stmt = $conn->prepare("SELECT * FROM booking WHERE booking_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $bookingResult = $stmt->get_result();
    $booking = $bookingResult->fetch_assoc();

    // 4. Cek Ketersediaan Data
    // Kalau booking utamanya aja gak ada, gak usah cari room/inventory/dll
    if (!$booking) {
        echo json_encode(["status" => "error", "message" => "Data booking tidak ditemukan"]);
        exit;
    }

    // 5. Ambil List Ruangan
    $stmtRooms = $conn->prepare("SELECT rooms.room_id, room_name 
                       FROM booking_rooms 
                       JOIN rooms ON rooms.room_id = booking_rooms.room_id
                       WHERE booking_id = ?");
    $stmtRooms->bind_param("i", $id);
    $stmtRooms->execute();
    $resRooms = $stmtRooms->get_result();
    $rooms = [];
    while ($row = $resRooms->fetch_assoc()) $rooms[] = $row;

    // 6. Ambil List Inventaris
    $stmtInv = $conn->prepare("SELECT booking_inventory.quantity, item_name
                       FROM booking_inventory
                       JOIN inventory ON inventory.inventory_id = booking_inventory.inventory_id
                       WHERE booking_id = ?");
    $stmtInv->bind_param("i", $id);
    $stmtInv->execute();
    $resInv = $stmtInv->get_result();
    $inv = [];
    while ($row = $resInv->fetch_assoc()) $inv[] = $row;

    // 7. Ambil Status Approval
    $stmtApp = $conn->prepare("SELECT * FROM booking_approval WHERE booking_id = ? ORDER BY approval_id DESC");
    $stmtApp->bind_param("i", $id);
    $stmtApp->execute();
    $resApp = $stmtApp->get_result();
    $approval = [];
    while ($row = $resApp->fetch_assoc()) $approval[] = $row;

    // 8. Output Final
    echo json_encode([
        "status" => "success",
        "data" => [
            "booking" => $booking,
            "rooms" => $rooms,
            "inventory" => $inv,
            "approval" => $approval
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server Error: " . $e->getMessage()]);
}
?>