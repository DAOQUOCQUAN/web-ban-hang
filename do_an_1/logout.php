<?php
session_start();
session_destroy(); // Hủy tất cả session
header("Location: home.php"); // Chuyển hướng về trang chủ
?>
