<?php
session_start();
include('../db.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../admin_login.php');
    exit;
}

// Lấy ID sản phẩm từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Nếu bấm nút XÓA
if (isset($_POST['delete_product'])) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<p class='text-red-600'>❌ Sản phẩm đã bị xóa.</p>";
    echo "<a href='manage_products.php' class='text-blue-600 underline'>Quay lại danh sách sản phẩm</a>";
    exit;
}

// Nếu bấm nút CẬP NHẬT
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $target = "../images/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $desc, $price, $image, $id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=? WHERE id=?");
        $stmt->bind_param("ssii", $name, $desc, $price, $id);
    }

    $stmt->execute();
    echo "<p class='text-green-600'>✅ Sản phẩm đã được cập nhật.</p>";
    header("Location: product_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-4 text-center">Sửa sản phẩm</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-input w-full" required>
            <textarea name="description" class="form-input w-full" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            <input type="number" name="price" value="<?= $product['price'] ?>" class="form-input w-full" required>
            <input type="file" name="image" class="form-input w-full">
            <div class="flex gap-4">
                <button type="submit" name="update_product" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Cập nhật</button>
                <button type="submit" name="delete_product" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này không?')">Xóa</button>
            </div>
        </form>
    </div>
</body>
</html>
