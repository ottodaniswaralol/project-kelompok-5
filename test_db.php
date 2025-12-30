<?php
include 'config/database.php';

if ($conn) {
    echo "OK: Database connected!";
} else {
    echo "FAILED";
}
?>
