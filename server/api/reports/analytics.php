<?php
require_once '../../config/database.php';

// DEBUG SEMENTARA
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Ambil parameter filter
$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$year  = isset($_GET['year'])  ? intval($_GET['year'])  : intval(date('Y'));

// Validasi
if ($month < 1 || $month > 12) {
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Bulan tidak valid"]);
    exit;
}

// Query 1: Booking per ruangan (dari view)
$chart_sql = "SELECT room_name, total_bookings, total_minutes_used 
              FROM vw_room_utilization_monthly 
              WHERE period = '" . sprintf('%04d-%02d', $year, $month) . "'
              ORDER BY total_bookings DESC";

$chart_result = mysqli_query($conn, $chart_sql);
$chart_data = [];
while ($row = mysqli_fetch_assoc($chart_result)) {
    $chart_data[] = $row;
}

// Query 2: Summary statistik
$summary_sql = "SELECT 
    COUNT(*) AS total_booking,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS total_approved,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS total_rejected,
    SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) AS total_pending
FROM booking
WHERE MONTH(start_datetime) = $month AND YEAR(start_datetime) = $year";

$summary_result = mysqli_query($conn, $summary_sql);
$summary = mysqli_fetch_assoc($summary_result);

// Query 3: Room popularity (hari & jam tersibuk)
$popularity_sql = "SELECT room_name, day_name, hour_of_day, booking_count
                   FROM vw_room_popularity
                   ORDER BY booking_count DESC
                   LIMIT 10";

$popularity_result = mysqli_query($conn, $popularity_sql);
$popularity_data = [];
while ($row = mysqli_fetch_assoc($popularity_result)) {
    $popularity_data[] = $row;
}

echo json_encode([
    "status"     => true,
    "month"      => $month,
    "year"       => $year,
    "summary"    => $summary,
    "chart_data" => $chart_data,
    "popularity" => $popularity_data
]);
?>