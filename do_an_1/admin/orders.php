<?php
session_start();
include('../db.php');
include('header.php');

// Lấy danh sách đơn hàng từ cơ sở dữ liệu
$orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>

<h2 class="text-xl font-semibold mb-4">Danh sách đơn hàng</h2>

<?php while ($order = $orders->fetch_assoc()): ?>
    <div class="order border p-4 bg-white mb-3 rounded shadow">
        <h3 class="text-lg font-semibold">Đơn hàng #<?= $order['id'] ?> - 
            <span class="text-<?= ($order['status'] == 'completed') ? 'green' : (($order['status'] == 'cancelled') ? 'red' : 'yellow') ?>-600"><?= ucfirst($order['status']) ?></span>
        </h3>
        <p class="text-sm text-gray-500">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p class="text-gray-600">Địa chỉ giao hàng: <?= htmlspecialchars($order['address']) ?></p>

        <!-- Hiển thị sản phẩm trong đơn hàng -->
        <ul class="list-disc pl-5">
        <?php
        $items = $conn->query("SELECT * FROM order_items WHERE order_id = " . $order['id']);
        while ($item = $items->fetch_assoc()):
        ?>
            <li><?= $item['product_name'] ?> (<?= $item['quantity'] ?>) - <?= number_format($item['price'], 0, ',', '.') ?> VND</li>
        <?php endwhile; ?>
        </ul>

        <!-- Thay đổi trạng thái đơn hàng -->
        <form method="POST" action="update_order_status.php" class="mt-4">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <select name="status" class="border p-2 rounded w-full mt-1">
                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Hủy bỏ</option>
            </select>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mt-2 w-full">Cập nhật trạng thái</button>
        </form>
    </div>
<?php endwhile; ?>

<?php
if (isset($_SESSION['message'])) {
    echo '<div class="bg-green-500 text-white p-4 rounded mb-6">';
    echo $_SESSION['message'];
    echo '</div>';
    unset($_SESSION['message']);
}
?>