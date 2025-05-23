<?php
session_start();
include('db.php');

// Kiểm tra xem giỏ hàng có trống không
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin đơn hàng từ form
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $note = $_POST['note'];
    $total_price = 0;

    // Tính tổng tiền
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // Lưu thông tin đơn hàng vào bảng orders
    $stmt = $conn->prepare("INSERT INTO orders (fullname, phone, address, note, total_price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $fullname, $phone, $address, $note, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id;  // Lấy ID đơn hàng vừa tạo

    // Lưu chi tiết sản phẩm vào bảng order_items
    foreach ($_SESSION['cart'] as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?)");
        $total_item = $item['price'] * $item['quantity'];
        $stmt->bind_param("iisiis", $order_id, $item['id'], $item['name'], $item['quantity'], $item['price'], $total_item);
        $stmt->execute();
    }

    // Xóa giỏ hàng sau khi đặt hàng
    unset($_SESSION['cart']);

    // Chuyển hướng đến trang xác nhận đơn hàng
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();
}
?>

