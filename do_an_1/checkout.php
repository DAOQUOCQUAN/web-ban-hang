<?php
session_start();
include('db.php');

// Kiểm tra đăng nhập
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("⚠️ Bạn cần đăng nhập để đặt hàng. <a href='login.php' class='text-blue-600 underline'>Đăng nhập</a>");
}

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $note = trim($_POST['note'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';

    if ($name && $address && $phone && $payment_method) {
        $conn->begin_transaction();

        try {
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Ghi đơn hàng (THÊM user_id)
            $stmt = $conn->prepare("INSERT INTO orders (user_id, name, address, phone, total_price, note, payment_method, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            if ($stmt === false) {
                throw new Exception('Lỗi SQL: ' . $conn->error);
            }
            $stmt->bind_param("isssdss", $user_id, $name, $address, $phone, $total, $note, $payment_method);
            if (!$stmt->execute()) {
                throw new Exception('Lỗi thực thi câu lệnh: ' . $stmt->error);
            }

            $order_id = $stmt->insert_id;

            // Thêm sản phẩm vào chi tiết đơn hàng
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
            if ($stmt_item === false) {
                throw new Exception('Lỗi chuẩn bị chi tiết sản phẩm: ' . $conn->error);
            }

            foreach ($_SESSION['cart'] as $item) {
                $stmt_item->bind_param("isid", $order_id, $item['name'], $item['quantity'], $item['price']);
                if (!$stmt_item->execute()) {
                    throw new Exception('Lỗi thêm sản phẩm: ' . $stmt_item->error);
                }
            }

            $conn->commit();
            unset($_SESSION['cart']);

            if ($payment_method === 'ricpay') {
                $_SESSION['ricpay_order_id'] = $order_id;
                header("Location: ricpay_verify.php");
                exit();
            } elseif ($payment_method === 'bank') {
                $_SESSION['bank_order_id'] = $order_id;
                header("Location: bank_transfer_info.php");
                exit();
            } else {
                $_SESSION['message'] = "✅ Đặt hàng thành công! Thanh toán khi nhận hàng.";
                header("Location: home.php");
                exit();
            }

        } catch (Exception $e) {
            $conn->rollback();
            echo "<p class='text-red-600 font-semibold'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='text-red-600 font-semibold'>❌ Vui lòng nhập đầy đủ thông tin và chọn hình thức thanh toán.</p>";
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

    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <input type="text" name="name" placeholder="Họ tên người nhận" required class="border p-2 rounded">
            <input type="text" name="phone" placeholder="Số điện thoại" required class="border p-2 rounded">
            <textarea name="address" placeholder="Địa chỉ giao hàng (ghi đầy đủ)" rows="3" required class="border p-2 rounded md:col-span-2"></textarea>
            <textarea name="note" placeholder="Ghi chú đơn hàng (nếu có)" rows="3" class="w-full p-2 border rounded md:col-span-2"></textarea>
        </div>

        <div class="mb-6">
            <label class="block mb-2 font-semibold">💳 Hình thức thanh toán:</label>
            <select name="payment_method" class="border p-2 rounded w-full" required>
                <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                <option value="ricpay">Ví RicPay (ảo)</option>
                <option value="bank">Chuyển khoản ngân hàng</option>
            </select>
        </div>

        <h2 class="text-xl font-semibold mb-4">📦 Đơn hàng</h2>
        <table class="w-full mb-6 border-collapse">
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
                    <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                    <tr class="border-b">
                        <td class="px-3 py-2"><?= htmlspecialchars($item['name']) ?></td>
                        <td class="px-3 py-2"><?= $item['quantity'] ?></td>
                        <td class="px-3 py-2"><?= number_format($item['price']) ?> VND</td>
                        <td class="px-3 py-2"><?= number_format($subtotal) ?> VND</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-right text-xl font-semibold text-green-700 mb-6">
            Tổng cộng: <?= number_format($total) ?> VND
        </div>

        <div class="flex justify-between">
            <a href="cart.php" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">← Quay lại giỏ hàng</a>
            <button type="submit" name="checkout" onclick="return confirm('Bạn có chắc chắn muốn đặt hàng không?')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">
                ✅ Đặt hàng
            </button>
        </div>
    </form>
</div>
</body>
</html>
