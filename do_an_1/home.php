<?php
session_start();
include('db.php');
if (isset($_GET['id'])) {
    include 'product_detail.php';
    exit();
}
// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
    $product_price = isset($_POST['product_price']) ? $_POST['product_price'] : 0;
    $product_image = isset($_POST['product_image']) ? $_POST['product_image'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($product_id && $product_name && $product_price > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'price' => $product_price,
                'image' => $product_image,
                'quantity' => $quantity
            ];
        }

        $_SESSION['message'] = "\"{$product_name}\" đã được thêm vào giỏ hàng!";
    } else {
        $_SESSION['message'] = "Dữ liệu sản phẩm không hợp lệ.";
    }
    header("Location: home.php");
    exit;
}
// Xử lý tìm kiếm
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    $search_param = "%" . $search . "%";
    $stmt->bind_param("s", $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM products");
}

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
<div class="nav">
    <div>
        <h1 class="text-3xl font-semibold">RiceRice</h1>
    </div>
    <div class="nav-right">
        <?php if (isset($_SESSION['username'])): ?>
            <span>Xin chào, <strong><?php echo $_SESSION['username']; ?></strong></span> |
            <a href="order_history.php">🛍️ Đơn hàng của tôi</a> |
            <a href="cart.php">🛒 Giỏ hàng (<?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?>)</a> |
            <a href="logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="login.php">Đăng nhập</a> |
            <a href="register.php">Đăng ký</a>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 border border-green-300">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<div class="intro">
    <div class="intro-text">
        <h2>Giới thiệu về RiceRice</h2>
        <p>Chào mừng bạn đến với RiceRice! Chúng tôi chuyên cung cấp các loại gạo chất lượng cao...</p>
        <p>Với các loại gạo như gạo trắng, gạo lứt, gạo nếp và gạo thơm...</p>
    </div>
    <div class="intro-img">
        <img src="images/store.png" alt="Giới thiệu cửa hàng">
    </div>
</div>

<form class="search-form" method="GET" action="home.php" style="text-align: center;">
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm kiếm sản phẩm...">
    <button type="submit">Tìm</button>
</form>

<h2 class="text-2xl font-semibold mt-4">🌾 Danh sách sản phẩm</h2>
<div class="flex flex-wrap">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                $image_path = 'images/' . $row['image'];
                if (!file_exists($image_path)) {
                    $image_path = 'images/default.png';
                }
            ?>
            <div class="product hover:shadow-lg transition-shadow duration-200">
                <a href="product_detail.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                    <img src="<?php echo $image_path; ?>" alt="Ảnh sản phẩm">
                    <h3 class="text-lg font-semibold mt-2 hover:text-green-600"><?php echo $row['name']; ?></h3>
                    <p class="text-gray-600 mt-2"><?php echo $row['description']; ?></p>
                    <p class="mt-2 text-green-600"><strong>Giá:</strong> <?php echo number_format($row['price'], 0, ',', '.'); ?> VND</p>
                </a>

                <form method="POST" action="home.php" class="mt-4">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $row['image']; ?>">
                    <label>Số lượng:</label>
                    <input type="number" name="quantity" value="1" min="1" class="w-16 px-2 py-1 border rounded mt-1">
                    <button type="submit" name="add_to_cart" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-2">🛒 Thêm vào giỏ</button>
                </form>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm nào.</p>
    <?php endif; ?>
</div>
</body>
</html>