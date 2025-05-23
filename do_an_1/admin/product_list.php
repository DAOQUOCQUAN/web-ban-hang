<?php
session_start();
include('../db.php');
include('header.php');

$result = $conn->query("SELECT * FROM products");
?>

<h2 class="text-xl font-semibold mb-4">Danh sách sản phẩm</h2>
<?php while ($row = $result->fetch_assoc()): ?>
    <div class="product mb-4 border p-3 bg-white">
        <h3 class="font-semibold"><?= $row['name'] ?></h3>
        <p><?= $row['description'] ?></p>
        <p>Giá: <?= number_format($row['price'], 0, ',', '.') ?> VND</p>
        <img src="../images/<?= $row['image'] ?>" width="100"><br>
        <a href="edit_product.php?id=<?= $row['id'] ?>" class="text-blue-500">Sửa</a> |
        <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Bạn chắc chắn xóa?')" class="text-red-500">Xóa</a>
    </div>
<?php endwhile; ?>
