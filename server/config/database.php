<?php
// Ambil variabel environment dari Railway
$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "";
$db   = getenv('MYSQLDATABASE') ?: "room_booking";
$port = getenv('MYSQLPORT') ?: "3306";

// Koneksi CUKUP SATU KALI
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    header("Content-Type: application/json");
    die(json_encode(["status" => false, "message" => "Database Error: " . mysqli_connect_error()]));
}
?>