<?php
session_start();

// Hủy tất cả session
session_unset();
session_destroy();

// Quay lại trang đăng nhập
header("Location: ../admin_login.php");
exit;
?>
