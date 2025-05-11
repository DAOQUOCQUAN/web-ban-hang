<?php
include('db.php');

// Truy vấn toàn bộ đơn hàng (không lọc theo người dùng)
$query = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($query);
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

    <?php
// Hàm chuyển trạng thái từ tiếng Anh sang tiếng Việt
function translateStatus($status) {
    switch (strtolower($status)) {
        case 'pending': return 'Chờ xử lý';
        case 'processing': return 'Đang xử lý';
        case 'shipped': return 'Đã giao hàng';
        case 'completed': return 'Hoàn thành';
        case 'cancelled': return 'Đã hủy';
        default: return ucfirst($status);
    }
}
?>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="mb-6 p-4 border rounded shadow-sm bg-gray-50">
                <h2 class="text-lg font-semibold mb-2">Đơn hàng #<?= $order['id'] ?> - <span class="text-blue-600"><?= ucfirst($order['status']) ?></span></h2>
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['name']) ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note']) ?></p>
                <p><strong>Ngày đặt:</strong> <?= $order['created_at'] ?></p>
                <p><strong>Trạng thái:</strong> <?= translateStatus($order['status']) ?></p>
                <p class="text-green-700 font-semibold mt-2"><strong>Tổng tiền:</strong> <?= number_format($order['total_price'], 0, ',', '.') ?> VND</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-600">Chưa có đơn hàng nào được ghi nhận.</p>
    <?php endif; ?>
</div>

</body>
</html>
