<?php
$host = getenv('MYSQLHOST'); 
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

if (!$host) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "room_booking";
    $port = "3306";
}

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi Gagal");
}
?>