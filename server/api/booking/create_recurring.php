<?php
require_once '../../config/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Validasi input
$required = ['user_id', 'room_id', 'event_name', 'organization', 'day_of_week', 
             'start_date', 'end_date', 'start_time', 'end_time'];
foreach ($required as $field) {
    if (empty($data[$field]) && $data[$field] !== 0) {
        http_response_code(400);
        echo json_encode(["status" => false, "message" => "Field '$field' wajib diisi"]);
        exit;
    }
}

$user_id      = intval($data['user_id']);
$room_id      = intval($data['room_id']);
$event_name   = mysqli_real_escape_string($conn, $data['event_name']);
$organization = mysqli_real_escape_string($conn, $data['organization']);
$phone        = mysqli_real_escape_string($conn, $data['phone'] ?? '');
$description  = mysqli_real_escape_string($conn, $data['description'] ?? '');
$day_of_week  = intval($data['day_of_week']); // 0=Minggu, 1=Senin, ..., 6=Sabtu
$start_date   = $data['start_date']; // format: YYYY-MM-DD
$end_date     = $data['end_date'];   // format: YYYY-MM-DD
$start_time   = $data['start_time']; // format: HH:MM
$end_time     = $data['end_time'];   // format: HH:MM
$frequency    = $data['frequency'] ?? 'weekly';
$interval_count = intval($data['interval_count'] ?? 1);

// Validasi tanggal
if (strtotime($start_date) > strtotime($end_date)) {
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Tanggal mulai tidak boleh lebih dari tanggal akhir"]);
    exit;
}

// STEP 1: Kalkulasi semua tanggal target
$dates_array = [];
$current = new DateTime($start_date);
$end     = new DateTime($end_date);

while ($current <= $end) {
    // PHP: 0=Minggu, 1=Senin, ..., 6=Sabtu (sama dengan input)
    if (intval($current->format('w')) === $day_of_week) {
        $dates_array[] = $current->format('Y-m-d');
    }
    $current->modify('+1 day');
}

if (empty($dates_array)) {
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Tidak ada tanggal yang cocok dalam rentang tersebut"]);
    exit;
}

// STEP 2: Cek ketersediaan SEMUA tanggal sebelum insert
$conflicts = [];
foreach ($dates_array as $date) {
    $start_dt = $date . ' ' . $start_time . ':00';
    $end_dt   = $date . ' ' . $end_time . ':00';

    $check_sql = "SELECT b.booking_id FROM booking b
                  JOIN booking_rooms br ON b.booking_id = br.booking_id
                  WHERE br.room_id = $room_id
                  AND b.status != 'rejected'
                  AND b.start_datetime < '$end_dt'
                  AND b.end_datetime > '$start_dt'
                  LIMIT 1";

    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
        $conflicts[] = $date;
    }
}

if (!empty($conflicts)) {
    http_response_code(409);
    echo json_encode([
        "status"    => false,
        "message"   => "Terdapat konflik jadwal pada tanggal berikut",
        "conflicts" => $conflicts
    ]);
    exit;
}

// STEP 3: Atomic insert semua booking
$recurring_group_id = sprintf(
    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
);

mysqli_begin_transaction($conn);

try {
    // Insert recurrence rule
    $rule_sql = "INSERT INTO booking_recurrence_rule 
                 (recurring_group_id, user_id, frequency, interval_count, day_of_week, 
                  recurrence_start_date, recurrence_end_date, total_occurrences, generated_count)
                 VALUES ('$recurring_group_id', $user_id, '$frequency', $interval_count, 
                         $day_of_week, '$start_date', '$end_date', " . count($dates_array) . ", 0)";

    if (!mysqli_query($conn, $rule_sql)) {
        throw new Exception("Gagal menyimpan recurrence rule: " . mysqli_error($conn));
    }

    $booking_ids = [];

    foreach ($dates_array as $date) {
        $start_dt = $date . ' ' . $start_time . ':00';
        $end_dt   = $date . ' ' . $end_time . ':00';

        // Insert booking
        $booking_sql = "INSERT INTO booking 
                        (user_id, event_name, organization, phone, event_description,
                         start_datetime, end_datetime, status, recurring_group_id, created_at)
                        VALUES ($user_id, '$event_name', '$organization', '$phone', '$description',
                                '$start_dt', '$end_dt', 'pending', '$recurring_group_id', NOW())";

        if (!mysqli_query($conn, $booking_sql)) {
            throw new Exception("Gagal insert booking: " . mysqli_error($conn));
        }

        $booking_id = mysqli_insert_id($conn);
        $booking_ids[] = $booking_id;

        // Insert booking_rooms
        $room_sql = "INSERT INTO booking_rooms (booking_id, room_id) VALUES ($booking_id, $room_id)";
        if (!mysqli_query($conn, $room_sql)) {
            throw new Exception("Gagal insert booking_rooms: " . mysqli_error($conn));
        }

        // Cek apakah ruang prioritas marketing
        $priority_sql = "SELECT is_priority_marketing FROM rooms WHERE room_id = $room_id";
        $priority_result = mysqli_query($conn, $priority_sql);
        $priority_row = mysqli_fetch_assoc($priority_result);
        $approval_step = ($priority_row['is_priority_marketing'] == 1) ? 'marketing' : 'bima';

        // Insert booking_approval
        $approval_sql = "INSERT INTO booking_approval (booking_id, step, status)
                         VALUES ($booking_id, '$approval_step', 'pending')";
        if (!mysqli_query($conn, $approval_sql)) {
            throw new Exception("Gagal insert booking_approval: " . mysqli_error($conn));
        }
    }

    // Update generated_count di recurrence_rule
    $update_rule = "UPDATE booking_recurrence_rule 
                    SET generated_count = " . count($dates_array) . "
                    WHERE recurring_group_id = '$recurring_group_id'";
    mysqli_query($conn, $update_rule);

    // Insert notifikasi ke user
    $notif_title   = "Peminjaman Rutin Berhasil Dibuat";
    $notif_message = "Sebanyak " . count($dates_array) . " jadwal berhasil dibuat untuk grup rutin Anda.";
    $notif_sql = "INSERT INTO notifications (user_id, booking_id, title, message)
                  VALUES ($user_id, {$booking_ids[0]}, '$notif_title', '$notif_message')";
    mysqli_query($conn, $notif_sql);

    mysqli_commit($conn);

    echo json_encode([
        "status"             => true,
        "message"            => "Recurring booking berhasil dibuat",
        "recurring_group_id" => $recurring_group_id,
        "total_sessions"     => count($dates_array),
        "dates"              => $dates_array,
        "booking_ids"        => $booking_ids
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode([
        "status"  => false,
        "message" => $e->getMessage()
    ]);
}
?>