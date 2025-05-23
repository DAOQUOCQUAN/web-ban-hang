<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <div class="max-w-4xl mx-auto py-10 px-6">
        <div class="bg-white rounded-xl shadow-md p-6 mb-8 text-center">
            <h1 class="text-3xl font-bold text-green-600">Bảng điều khiển quản trị</h1>
            <p class="mt-2 text-gray-600">Xin chào, <?php echo $_SESSION['admin_username']; ?>!</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Quản lý sản phẩm -->
            <a href="add_product.php" class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold text-green-700 mb-2">Thêm sản phẩm</h2>
                <p class="text-gray-500">Thêm sản phẩm mới vào cửa hàng.</p>
            </a>

            <a href="product_list.php" class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold text-green-700 mb-2">Quản lý sản phẩm</h2>
                <p class="text-gray-500">Xem, sửa và xóa các sản phẩm hiện có.</p>
            </a>

            <!-- Quản lý đơn hàng -->
            <a href="orders.php" class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold text-green-700 mb-2">Quản lý đơn hàng</h2>
                <p class="text-gray-500">Xem danh sách các đơn hàng của khách hàng.</p>
            </a>

            <!-- Đăng xuất -->
            <a href="admin_logout.php" class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold text-red-600 mb-2">Đăng xuất</h2>
                <p class="text-gray-500">Thoát khỏi trang quản trị.</p>
            </a>
        </div>
    </div>
</body>
</html>
