<?php
session_start();
include('db.php');

// Xử lý tìm kiếm với prepared statement để bảo vệ khỏi SQL injection
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // Prepared statement để tránh SQL injection
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    $search_param = "%" . $search . "%";
    $stmt->bind_param("s", $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM products");
}

// Thực hiện truy vấn
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .nav {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-right {
            display: flex;
            gap: 15px;
        }
        .product {
            border: 1px solid #ddd;
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            display: inline-block;
            width: 250px;
            vertical-align: top;
            margin-right: 15px;
            border-radius: 8px;
        }
        .product img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }
        .search-form input[type="text"] {
            padding: 8px;
            width: 300px;
        }
        .search-form button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #45a049;
        }
        .intro {
            background-color: #ffffff;
            padding: 40px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .intro-text {
            width: 60%;
        }
        .intro-text h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #2C7A7B;
        }
        .intro-text p {
            color: #4A5568;
            line-height: 1.8;
            margin-top: 10px;
        }
        .intro-img img {
            width: 250px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<!-- Thanh điều hướng -->
<div class="nav">
    <div>
        <h1 class="text-3xl font-semibold">RiceRice</h1>
    </div>
    <div class="nav-right">
        <?php if (isset($_SESSION['username'])): ?>
            <span>Xin chào, <strong><?php echo $_SESSION['username']; ?></strong></span> |
            <a href="cart.php">🛒 Giỏ hàng</a> |
            <a href="logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="login.php">Đăng nhập</a> | 
            <a href="register.php">Đăng ký</a>
        <?php endif; ?>
    </div>
</div>

<!-- Phần giới thiệu cửa hàng -->
<div class="intro">
    <div class="intro-text">
        <h2>Giới thiệu về RiceRice</h2>
        <p>
            Chào mừng bạn đến với RiceRice! Chúng tôi chuyên cung cấp các loại gạo chất lượng cao, được chọn lọc từ những vùng đất trồng gạo nổi tiếng tại Việt Nam. Mỗi hạt gạo của chúng tôi đều được kiểm tra kỹ lưỡng để đảm bảo đạt chuẩn về chất lượng, an toàn cho người tiêu dùng.
        </p>
        <p>
            Tại RicRice, chúng tôi cam kết mang đến cho bạn những sản phẩm gạo tươi ngon, sạch sẽ, và đảm bảo dinh dưỡng. Với các loại gạo như gạo trắng, gạo lứt, gạo nếp và gạo thơm, bạn sẽ luôn tìm thấy lựa chọn phù hợp cho từng món ăn và nhu cầu của gia đình.
        </p>
    </div>
    <div class="intro-img">
        <img src="images/store.png" alt="Giới thiệu cửa hàng">
    </div>
</div>

<!-- Form tìm kiếm -->
<form class="search-form" method="GET" action="home.php" style="text-align: center;">
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm kiếm sản phẩm...">
    <button type="submit">Tìm</button>
</form>

<h2 class="text-2xl font-semibold mt-4">🌾 Danh sách sản phẩm</h2>

<!-- Hiển thị sản phẩm -->
<div class="flex flex-wrap">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product">
                <img src="images/<?php echo $row['image']; ?>" alt="Ảnh sản phẩm">
                <h3 class="text-lg font-semibold mt-2"><?php echo $row['name']; ?></h3>
                <p class="text-gray-600 mt-2"><?php echo $row['description']; ?></p>
                <p class="mt-2 text-green-600"><strong>Giá:</strong> <?php echo number_format($row['price'], 0, ',', '.'); ?> VND</p>

                <!-- Nút thêm vào giỏ -->
                <form method="POST" action="cart.php" class="mt-4">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $row['image']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" name="add_to_cart" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">🛒 Thêm vào giỏ</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm nào.</p>
    <?php endif; ?>
</div>

</body>
</html>
