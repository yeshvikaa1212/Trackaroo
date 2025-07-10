<?php
$host = "localhost";
$db_user = "root";
$db_pass = "yeshvikaa@2004."; // ✅ This must match the password that worked in XAMPP shell
$db_name = "trackaroo";
$port = 3307;

$conn = new mysqli($host, $db_user, $db_pass, $db_name,$port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
