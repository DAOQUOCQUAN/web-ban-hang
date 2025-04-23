<?php
session_start();
include('db.php');

// Kiểm tra xem người dùng đã đăng nhập với quyền admin chưa
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: home.php');
    exit;
}

// Xử lý thêm sản phẩm
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $target = "images/" . basename($image);

    // Thêm sản phẩm vào cơ sở dữ liệu
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $description, $price, $image);
    $stmt->execute();

    // Di chuyển ảnh vào thư mục images
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        echo "Sản phẩm đã được thêm thành công.";
    } else {
        echo "Lỗi khi tải ảnh lên.";
    }
}

// Xử lý sửa sản phẩm
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $target = "images/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $description, $price, $image, $id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=? WHERE id=?");
        $stmt->bind_param("ssii", $name, $description, $price, $id);
    }

    $stmt->execute();
    echo "Sản phẩm đã được cập nhật.";
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "Sản phẩm đã được xóa.";
}

// Lấy danh sách sản phẩm
$products_query = "SELECT * FROM products";
$products_result = $conn->query($products_query);

// Lấy danh sách đơn hàng
$orders_query = "SELECT * FROM orders";
$orders_result = $conn->query($orders_query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Admin - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .product, .order {
            border: 1px solid #ddd;
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .product img, .order img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .form-input {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
        }
        button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<h1 class="text-3xl font-semibold mb-4">Quản lý Admin - RicRice</h1>

<!-- Thêm sản phẩm -->
<h2 class="text-xl font-semibold mb-2">Thêm sản phẩm</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" class="form-input" placeholder="Tên sản phẩm" required>
    <textarea name="description" class="form-input" placeholder="Mô tả sản phẩm" required></textarea>
    <input type="number" name="price" class="form-input" placeholder="Giá sản phẩm (VND)" required>
    <input type="file" name="image" class="form-input" required>
    <button type="submit" name="add_product">Thêm sản phẩm</button>
</form>

<!-- Quản lý sản phẩm -->
<h2 class="text-xl font-semibold mt-4">Quản lý sản phẩm</h2>
<?php while ($product = $products_result->fetch_assoc()): ?>
    <div class="product">
        <h3><?php echo $product['name']; ?></h3>
        <p><?php echo $product['description']; ?></p>
        <p><strong>Giá:</strong> <?php echo number_format($product['price'], 0, ',', '.'); ?> VND</p>
        <img src="images/<?php echo $product['image']; ?>" alt="Ảnh sản phẩm">
        <a href="admin.php?edit_product=<?php echo $product['id']; ?>">Sửa</a> | 
        <a href="admin.php?delete_product=<?php echo $product['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</a>
    </div>
<?php endwhile; ?>

<!-- Quản lý đơn hàng -->
<h2 class="text-xl font-semibold mt-4">Quản lý đơn hàng</h2>
<?php while ($order = $orders_result->fetch_assoc()): ?>
    <div class="order">
        <h3>Đơn hàng #<?php echo $order['id']; ?></h3>
        <p><strong>Trạng thái:</strong> <?php echo $order['status']; ?></p>
        <p><strong>Ngày đặt:</strong> <?php echo $order['order_date']; ?></p>
        <!-- Liệt kê các sản phẩm trong đơn hàng -->
        <p><strong>Sản phẩm trong đơn:</strong></p>
        <?php
        $order_id = $order['id'];
        $order_items_query = "SELECT * FROM order_items WHERE order_id = $order_id";
        $order_items_result = $conn->query($order_items_query);
        while ($item = $order_items_result->fetch_assoc()):
        ?>
            <p><?php echo $item['product_name']; ?> - Số lượng: <?php echo $item['quantity']; ?> - Giá: <?php echo number_format($item['price'], 0, ',', '.'); ?> VND</p>
        <?php endwhile; ?>
    </div>
<?php endwhile; ?>

</body>
</html>

