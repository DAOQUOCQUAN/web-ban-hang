<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Chuyển hướng tới trang đăng nhập nếu chưa đăng nhập
    exit();
}

include('db.php');

// Kiểm tra nếu người dùng muốn thanh toán
if (isset($_POST['checkout'])) {
    // Lấy thông tin thanh toán từ form
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $note = $_POST['note'];

    // Xử lý thanh toán (lưu thông tin đơn hàng vào cơ sở dữ liệu, cập nhật sản phẩm trong kho, v.v.)
    $user_id = $_SESSION['user_id']; // Giả sử bạn lưu user_id trong session sau khi đăng nhập
    $total_price = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['product_price'] * $item['quantity'];
    }

    // Lưu đơn hàng vào cơ sở dữ liệu
    $query = "INSERT INTO orders (user_id, total_price, name, address, phone, note, status) 
              VALUES ('$user_id', '$total_price', '$name', '$address', '$phone', '$note', 'pending')";
    $conn->query($query);
    $order_id = $conn->insert_id;

    // Lưu các sản phẩm trong đơn hàng
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['product_price'];

        // Lưu thông tin sản phẩm vào bảng order_items
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                  VALUES ('$order_id', '$product_id', '$quantity', '$price')";
        $conn->query($query);
    }

    // Sau khi thanh toán thành công, xóa giỏ hàng
    unset($_SESSION['cart']);
    echo "<p>Thanh toán thành công! Đơn hàng của bạn đã được xử lý.</p>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f8f8f8; }
        .cart-button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .cart-button:hover { background-color: #45a049; }
        .input-field { padding: 8px; width: 100%; margin-bottom: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <h1>Thanh Toán</h1>

    <!-- Kiểm tra nếu giỏ hàng có sản phẩm -->
    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <form method="POST" action="checkout.php">
            <h3>Thông tin giỏ hàng</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_price = 0;
                    foreach ($_SESSION['cart'] as $item):
                        $item_total = $item['product_price'] * $item['quantity'];
                        $total_price += $item_total;
                    ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo number_format($item['product_price']); ?> VND</td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item_total); ?> VND</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Tổng cộng: <?php echo number_format($total_price); ?> VND</h3>

            <!-- Thông tin thanh toán -->
            <h3>Thông tin thanh toán</h3>
            <label for="name">Tên người nhận:</label>
            <input type="text" name="name" class="input-field" required>

            <label for="address">Địa chỉ giao hàng:</label>
            <input type="text" name="address" class="input-field" required>

            <label for="phone">Số điện thoại:</label>
            <input type="text" name="phone" class="input-field" required>

            <label for="note">Ghi chú (tuỳ chọn):</label>
            <textarea name="note" class="input-field"></textarea>

            <button type="submit" name="checkout" class="cart-button">Hoàn tất thanh toán</button>
        </form>
    <?php else: ?>
        <p>Giỏ hàng của bạn hiện tại không có sản phẩm nào.</p>
        <a href="home.php" class="cart-button">Mua sắm thêm</a>
    <?php endif; ?>

</body>
</html>

