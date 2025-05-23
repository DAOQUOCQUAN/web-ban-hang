<?php
session_start();
include('db.php');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Lấy thông tin đơn hàng
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    // Lấy các sản phẩm trong đơn hàng
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items = $stmt->get_result();
} else {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Header -->
<div class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-green-700">✔️ Xác nhận đơn hàng</h1>
    <a href="home.php" class="text-blue-600 hover:underline">Trở lại trang chủ</a>
</div>

<!-- Nội dung -->
<div class="max-w-4xl mx-auto bg-white p-8 mt-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Thông tin đơn hàng</h2>

    <p><strong>Họ và tên:</strong> <?php echo $order['fullname']; ?></p>
    <p><strong>Số điện thoại:</strong> <?php echo $order['phone']; ?></p>
    <p><strong>Địa chỉ giao hàng:</strong> <?php echo $order['address']; ?></p>
    <p><strong>Ghi chú:</strong> <?php echo $order['note']; ?></p>
    
    <h3 class="text-lg font-semibold mt-4">Chi tiết sản phẩm:</h3>
    <ul class="list-disc pl-6">
        <?php while ($item = $items->fetch_assoc()): ?>
            <li>
                <?php echo $item['product_name']; ?> - Số lượng: <?php echo $item['quantity']; ?>, Giá: <?php echo number_format($item['price']); ?> VND, Tổng: <?php echo number_format($item['total']); ?> VND
            </li>
        <?php endwhile; ?>
    </ul>

    <p class="mt-4 text-lg font-semibold">Tổng cộng: <span class="text-green-700"><?php echo number_format($order['total_price']); ?> VND</span></p>
    <p class="mt-4 text-gray-600">Đơn hàng của bạn đang chờ xử lý. Cảm ơn bạn đã mua hàng tại RicRice!</p>
</div>

</body>
</html>
