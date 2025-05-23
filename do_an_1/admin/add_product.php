<?php
session_start();
include('../db.php');
include('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $target = "../images/" . basename($image);

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $description, $price, $image);
    $stmt->execute();

    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    echo "<p class='text-green-600'>✅ Sản phẩm đã thêm thành công.</p>";
}
?>

<h2 class="text-xl font-semibold mb-4">Thêm sản phẩm mới</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Tên sản phẩm" required class="form-input"><br>
    <textarea name="description" placeholder="Mô tả" required class="form-input"></textarea><br>
    <input type="number" name="price" placeholder="Giá" required class="form-input"><br>
    <input type="file" name="image" required class="form-input"><br>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-2">Thêm</button>
</form>
