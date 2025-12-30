<?php
// Ambil data dari Railway environment
$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "";
$db   = getenv('MYSQLDATABASE') ?: "room_booking";
$port = getenv('MYSQLPORT') ?: "3306";

// Satu kali koneksi saja
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    // Balikin JSON biar gak ngerusak CORS
    header("Content-Type: application/json");
    die(json_encode(["status" => false, "message" => "DB Error"]));
}
?>