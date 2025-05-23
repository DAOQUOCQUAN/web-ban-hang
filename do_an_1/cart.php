<?php
session_start();
include('db.php');

// Cập nhật số lượng sản phẩm
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $qty) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = max(1, (int)$qty); // Không cho số lượng < 1
        }
    }
    header("Location: cart.php");
    exit();
}

// Xóa sản phẩm khỏi giỏ
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">🛒 Giỏ Hàng của bạn</h1>

        <?php if (!empty($_SESSION['cart'])): ?>
        <form method="POST">
            <table class="w-full table-auto mb-4">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-3 py-2">Sản phẩm</th>
                        <th class="px-3 py-2">Ảnh</th>
                        <th class="px-3 py-2">Giá</th>
                        <th class="px-3 py-2">Số lượng</th>
                        <th class="px-3 py-2">Tổng</th>
                        <th class="px-3 py-2">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $id => $item): 
                        $item_total = $item['price'] * $item['quantity'];
                        $total += $item_total;
                    ?>
                    <tr class="border-b">
                        <td class="px-3 py-2"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="px-3 py-2">
                            <img src="images/<?php echo htmlspecialchars($item['image']); ?>" width="50" alt="Ảnh sản phẩm">
                        </td>
                        <td class="px-3 py-2"><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</td>
                        <td class="px-3 py-2">
                            <input type="number" name="quantities[<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="w-16 border rounded px-2">
                        </td>
                        <td class="px-3 py-2"><?php echo number_format($item_total, 0, ',', '.'); ?> VND</td>
                        <td class="px-3 py-2">
                            <a href="cart.php?action=delete&id=<?php echo $id; ?>" class="text-red-500 hover:underline">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="flex justify-between items-center mb-4">
                <button type="submit" name="update_cart" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Cập nhật giỏ hàng</button>
                <h3 class="text-xl font-semibold">Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?> VND</h3>
            </div>

            <div class="flex gap-4">
                <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">← Tiếp tục mua hàng</a>
                <a href="checkout.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Thanh toán</a>
            </div>
        </form>
        <?php else: ?>
            <p>Giỏ hàng của bạn đang trống.</p>
            <a href="home.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">← Quay lại mua hàng</a>
        <?php endif; ?>
    </div>
</body>
</html>
