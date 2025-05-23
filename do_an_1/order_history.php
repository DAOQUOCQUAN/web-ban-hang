<?php
session_start();
include('db.php');

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy đơn hàng chỉ của user đang đăng nhập
$query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Hàm chuyển trạng thái và thêm màu sắc
function translateStatus($status) {
    $status = strtolower($status);
    switch ($status) {
        case 'pending': 
            return ['Chờ xử lý', 'text-yellow-500'];
        case 'processing': 
            return ['Đang xử lý', 'text-blue-500'];
        case 'shipped': 
            return ['Đã giao hàng', 'text-purple-600'];
        case 'completed': 
            return ['Hoàn thành', 'text-green-600'];
        case 'cancelled': 
            return ['Đã hủy', 'text-red-600'];
        default: 
            return [ucfirst($status), 'text-gray-600'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử đơn hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">🧾 Lịch sử đơn hàng</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()):
            list($status_text, $status_class) = translateStatus($order['status']);
        ?>
            <div class="mb-6 p-4 border rounded shadow-sm bg-gray-50">
                <h2 class="text-lg font-semibold mb-2">
                    <span class="<?= $status_class ?>"><?= $status_text ?></span>
                </h2>
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['name']) ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note']) ?: 'Không có' ?></p>
                <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                <p class="text-green-700 font-semibold mt-2"><strong>Tổng tiền:</strong> <?= number_format($order['total_price'], 0, ',', '.') ?> VND</p>

                <!-- Hiển thị chi tiết sản phẩm trong đơn -->
                <?php
                $items_query = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
                $items_query->bind_param("i", $order['id']);
                $items_query->execute();
                $items_result = $items_query->get_result();
                ?>
                <h3 class="mt-4 font-semibold">Sản phẩm:</h3>
                <ul class="list-disc pl-6">
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <li>
                            <?= htmlspecialchars($item['product_name']) ?> 
                            (x<?= $item['quantity'] ?>) - <?= number_format($item['price'], 0, ',', '.') ?> VND
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php $items_query->close(); ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-600">Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>

    <?php $stmt->close(); ?>
</div>

</body>
</html>
