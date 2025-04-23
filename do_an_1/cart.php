<?php
session_start();
include('db.php');

// Kiểm tra nếu người dùng đã nhấn "Thêm vào giỏ"
if (isset($_POST['add_to_cart'])) {
    // Lấy dữ liệu sản phẩm từ form
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $quantity = $_POST['quantity'];

    // Nếu giỏ hàng chưa được tạo, tạo giỏ hàng mới
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    $product_exists = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $product_id) {
            // Nếu sản phẩm đã có, chỉ cần cập nhật số lượng
            $_SESSION['cart'][$key]['quantity'] += $quantity;
            $product_exists = true;
            break;
        }
    }

    // Nếu sản phẩm chưa có trong giỏ, thêm mới vào giỏ
    if (!$product_exists) {
        $new_item = array(
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_image' => $product_image,
            'quantity' => $quantity
        );
        $_SESSION['cart'][] = $new_item;
    }

    // Chuyển hướng về trang giỏ hàng
    header("Location: cart.php");
    exit();
}

// Kiểm tra nếu người dùng muốn xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $product_id_to_remove = $_GET['id'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $product_id_to_remove) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    // Đảm bảo mảng giỏ hàng không còn giá trị trống
    $_SESSION['cart'] = array_values($_SESSION['cart']);
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
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f8f8f8; }
        table { width: 100%; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        .cart-button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .cart-button:hover { background-color: #45a049; }
        .delete-button { background-color: red; color: white; padding: 5px 10px; border: none; cursor: pointer; }
        .delete-button:hover { background-color: #d32f2f; }
    </style>
</head>
<body>

    <h1>Giỏ Hàng</h1>

    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Ảnh</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng tiền</th>
                    <th>Hành động</th>
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
                    <td><img src="images/<?php echo $item['product_image']; ?>" width="50" alt="<?php echo $item['product_name']; ?>"></td>
                    <td><?php echo number_format($item['product_price']); ?> VND</td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item_total); ?> VND</td>
                    <td><a href="cart.php?action=delete&id=<?php echo $item['product_id']; ?>" class="delete-button">Xóa</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Tổng cộng: <?php echo number_format($total_price); ?> VND</h3>
        <a href="checkout.php" class="cart-button">Tiến hành thanh toán</a>
    <?php else: ?>
        <p>Giỏ hàng của bạn hiện tại không có sản phẩm nào.</p>
        <a href="home.php" class="cart-button">Mua sắm thêm</a>
    <?php endif; ?>

</body>
</html>
