<?php
$host = "localhost";
$username = "root";
$password = "2003";
$dbname = "ricrice1";

$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
