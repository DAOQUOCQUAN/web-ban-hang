<?php
session_start();
include('../db.php');

// Kiểm tra dữ liệu từ form gửi lên
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Cập nhật trạng thái đơn hàng
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Trạng thái đơn hàng đã được cập nhật!";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.";
    }

    header("Location: orders.php");
    exit();
} else {
    $_SESSION['message'] = "Thông tin không hợp lệ.";
    header("Location: orders.php");
    exit();
}
