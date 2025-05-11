<?php
session_start();
include('db.php');

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Nếu giỏ hàng trống, chuyển hướng về trang giỏ hàng
    header("Location: cart.php");
    exit();
}

// Xử lý khi người dùng gửi đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';

    if ($name && $address && $phone) {
        $conn->begin_transaction();

        try {
            // Tính tổng giá trị đơn hàng
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Lưu vào bảng orders
            $stmt = $conn->prepare("INSERT INTO orders (name, address, phone, total_price, created_at) VALUES (?, ?, ?, ?, NOW())");

            // Kiểm tra nếu việc prepare không thành công
            if ($stmt === false) {
                throw new Exception('Lỗi SQL khi chuẩn bị câu lệnh: ' . $conn->error);
            }

            $stmt->bind_param("sssd", $name, $address, $phone, $total);
            if (!$stmt->execute()) {
                throw new Exception('Lỗi khi thực thi câu lệnh: ' . $stmt->error);
            }

            $order_id = $stmt->insert_id;

            // Lưu chi tiết từng sản phẩm vào bảng order_items
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");

            // Kiểm tra nếu việc prepare không thành công
            if ($stmt_item === false) {
                throw new Exception('Lỗi SQL khi chuẩn bị câu lệnh chi tiết sản phẩm: ' . $conn->error);
            }

            foreach ($_SESSION['cart'] as $item) {
                $stmt_item->bind_param("isid", $order_id, $item['name'], $item['quantity'], $item['price']);
                if (!$stmt_item->execute()) {
                    throw new Exception('Lỗi khi thêm sản phẩm vào chi tiết đơn hàng: ' . $stmt_item->error);
                }
            }

            // Cam kết transaction
            $conn->commit();

            // Xóa giỏ hàng sau khi thanh toán thành công
            unset($_SESSION['cart']);
            $_SESSION['message'] = "Đặt hàng thành công!";

            // Chuyển hướng về trang chủ hoặc trang cảm ơn
            header("Location: home.php");
            exit();

        } catch (Exception $e) {
            // Nếu có lỗi, rollback transaction
            $conn->rollback();
            echo "<p class='text-red-600'>Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='text-red-600'>Vui lòng điền đầy đủ thông tin giao hàng.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">🧾 Thông tin thanh toán</h1>

        <form method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <input type="text" name="name" placeholder="Họ tên người nhận" required class="border p-2 rounded">
                <input type="text" name="phone" placeholder="Số điện thoại" required class="border p-2 rounded">
                <textarea name="address" placeholder="Địa chỉ giao hàng(Ghi đầy đủ)" rows="3" required class="border p-2 rounded md:col-span-2"></textarea>
                <textarea name="note" id="note" rows="4" class="w-full p-2 border rounded" placeholder="Ghi chú đơn hàng (nếu có)..."></textarea>
            </div>

            <h2 class="text-xl font-semibold mb-4">📦 Đơn hàng</h2>
            <table class="w-full mb-6 table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-3 py-2 text-left">Sản phẩm</th>
                        <th class="px-3 py-2 text-left">Số lượng</th>
                        <th class="px-3 py-2 text-left">Giá</th>
                        <th class="px-3 py-2 text-left">Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <?php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr class="border-b">
                            <td class="px-3 py-2"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="px-3 py-2"><?php echo $item['quantity']; ?></td>
                            <td class="px-3 py-2"><?php echo number_format($item['price']); ?> VND</td>
                            <td class="px-3 py-2"><?php echo number_format($subtotal); ?> VND</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-right text-xl font-semibold text-green-700 mb-6">
                Tổng cộng: <?php echo number_format($total); ?> VND
            </div>

            <div class="flex justify-between">
                <a href="cart.php" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">← Quay lại giỏ hàng</a>
                <button type="submit" name="checkout" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">✅ Đặt hàng</button>
            </div>
        </form>
    </div>
</body>
</html>
