<?php
session_start();

// Kiểm tra xem người dùng có quyền truy cập vào trang này không
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_POST['image'];  // Upload hình ảnh (bạn cần xử lý upload ảnh)
    $stock = $_POST['stock'];

    // Thêm sản phẩm vào cơ sở dữ liệu
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $price, $description, $image, $stock);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow mt-8">
    <h1 class="text-xl font-semibold mb-4">Thêm sản phẩm mới</h1>
    <form method="POST">
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Tên sản phẩm</label>
            <input type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded" required>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-gray-700">Giá</label>
            <input type="number" name="price" id="price" class="w-full p-2 border border-gray-300 rounded" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700">Mô tả</label>
            <textarea name="description" id="description" class="w-full p-2 border border-gray-300 rounded" required></textarea>
        </div>
        <div class="mb-4">
            <label for="image" class="block text-gray-700">Hình ảnh</label>
            <input type="text" name="image" id="image" class="w-full p-2 border border-gray-300 rounded" required>
        </div>
        <div class="mb-4">
            <label for="stock" class="block text-gray-700">Số lượng tồn kho</label>
            <input type="number" name="stock" id="stock" class="w-full p-2 border border-gray-300 rounded" required>
        </div>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Thêm sản phẩm</button>
    </form>
</div>

</body>
</html>
