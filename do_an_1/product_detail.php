<?php
session_start();
include('db.php');

if (!isset($_GET['id'])) {
    echo "Không tìm thấy sản phẩm.";
    exit;
}

$product_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Sản phẩm không tồn tại.";
    exit;
}

// Lấy sản phẩm liên quan
$related_result = $conn->query("SELECT * FROM products WHERE id != $product_id ORDER BY RAND() LIMIT 3");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - <?php echo $product['name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-5xl mx-auto py-10 px-4">
        <a href="home.php" class="text-blue-600 hover:underline mb-4 inline-block">← Quay lại trang chủ</a>

        <div class="bg-white rounded-xl shadow-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <img src="images/<?php echo $product['image']; ?>" alt="Ảnh sản phẩm" class="w-full h-auto object-cover rounded-xl">
            <div>
                <h1 class="text-3xl font-bold mb-2"><?php echo $product['name']; ?></h1>
                <p class="text-gray-600 mb-4"><?php echo $product['description']; ?></p>
                <p class="text-xl text-green-600 font-semibold mb-6"><?php echo number_format($product['price'], 0, ',', '.'); ?> VND</p>
                <form method="POST" action="home.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $product['image']; ?>">
                    <input type="number" name="quantity" min="1" value="1" class="border rounded-md px-3 py-2 w-20 mr-2">
                    <button type="submit" name="add_to_cart" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Thêm vào giỏ</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 mt-10">
            <h2 class="text-xl font-semibold mb-4">Đánh giá sản phẩm</h2>
            <p class="text-gray-500 italic">Chức năng đánh giá sẽ phát triển sau.</p>
        </div>

        <div class="mt-10">
            <h2 class="text-2xl font-bold mb-4">Sản phẩm liên quan</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php while ($related = $related_result->fetch_assoc()): ?>
                    <div class="bg-white rounded-xl shadow-md p-4 text-center">
                        <img src="images/<?php echo $related['image']; ?>" class="w-full h-40 object-cover rounded-md mb-2">
                        <h3 class="text-lg font-semibold"><?php echo $related['name']; ?></h3>
                        <p class="text-green-600 font-semibold"><?php echo number_format($related['price'], 0, ',', '.'); ?> VND</p>
                        <a href="product_detail.php?id=<?php echo $related['id']; ?>" class="inline-block mt-2 text-blue-600 hover:underline">Xem chi tiết</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
