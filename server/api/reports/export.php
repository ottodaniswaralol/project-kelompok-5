<?php
require_once '../../config/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$format = isset($_GET['format']) ? $_GET['format'] : 'csv';
$month  = isset($_GET['month'])  ? intval($_GET['month'])  : intval(date('m'));
$year   = isset($_GET['year'])   ? intval($_GET['year'])   : intval(date('Y'));

// Query data lengkap
$sql = "SELECT 
    b.booking_id,
    u.name AS peminjam,
    u.role,
    b.event_name,
    b.organization,
    b.phone,
    r.room_name,
    b.start_datetime,
    b.end_datetime,
    TIMESTAMPDIFF(MINUTE, b.start_datetime, b.end_datetime) AS durasi_menit,
    b.status,
    b.created_at
FROM booking b
JOIN users u ON u.user_id = b.user_id
JOIN booking_rooms br ON br.booking_id = b.booking_id
JOIN rooms r ON r.room_id = br.room_id
WHERE MONTH(b.start_datetime) = $month AND YEAR(b.start_datetime) = $year
ORDER BY b.start_datetime ASC";

$result = mysqli_query($conn, $sql);
$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}

if ($format === 'csv') {
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=laporan_booking_{$year}_{$month}.csv");

    $output = fopen('php://output', 'w');

    // Header CSV
    fputcsv($output, [
        'ID', 'Peminjam', 'Role', 'Nama Kegiatan', 'Organisasi',
        'No. Telepon', 'Ruangan', 'Mulai', 'Selesai', 'Durasi (Menit)', 'Status', 'Dibuat'
    ]);

    // Data rows
    foreach ($rows as $row) {
        fputcsv($output, array_values($row));
    }

    fclose($output);
    exit;
}

if ($format === 'pdf') {
    // Karena tidak ada library PDF di Railway, return JSON dulu
    // Nanti di frontend bisa pakai jsPDF untuk generate PDF dari data ini
    header("Content-Type: application/json");
    echo json_encode([
        "status" => true,
        "month"  => $month,
        "year"   => $year,
        "data"   => $rows
    ]);
    exit;
}

// Default
header("Content-Type: application/json");
echo json_encode(["status" => false, "message" => "Format tidak valid. Gunakan 'csv' atau 'pdf'"]);
?>