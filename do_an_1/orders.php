<?php
session_start();

// Kiểm tra xem người dùng có quyền truy cập vào trang này không
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Header -->
<div class="bg-white shadow p-4">
    <h1 class="text-2xl font-bold text-green-700">👨‍💼 Quản trị RicRice</h1>
    <div class="mt-4">
        <a href="logout.php" class="text-blue-600 hover:underline">Đăng xuất</a>
    </div>
</div>

<!-- Sidebar -->
<div class="flex">
    <div class="w-1/4 bg-gray-200 p-4">
        <ul>
            <li><a href="admin.php" class="text-blue-600">Quản lý sản phẩm</a></li>
            <li><a href="orders.php" class="text-blue-600">Quản lý đơn hàng</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="w-3/4 p-6">
        <h2 class="text-xl font-semibold">Quản lý đơn hàng</h2>

        <!-- Hiển thị danh sách đơn hàng -->
        <table class="w-full mt-6 table-auto border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="py-2 px-4">Họ tên</th>
                    <th class="py-2 px-4">Số điện thoại</th>
                    <th class="py-2 px-4">Địa chỉ</th>
                    <th class="py-2 px-4">Tổng giá</th>
                    <th class="py-2 px-4">Trạng thái</th>
                    <th class="py-2 px-4">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM orders");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='border-b'>";
                    echo "<td class='py-2 px-4'>" . $row['fullname'] . "</td>";
                    echo "<td class='py-2 px-4'>" . $row['phone'] . "</td>";
                    echo "<td class='py-2 px-4'>" . $row['address'] . "</td>";
                    echo "<td class='py-2 px-4'>" . number_format($row['total_price']) . " VND</td>";
                    echo "<td class='py-2 px-4'>" . $row['status'] . "</td>";
                    echo "<td class='py-2 px-4'>
                            <a href='update_order_status.php?id=" . $row['id'] . "' class='text-yellow-500 hover:underline'>Cập nhật trạng thái</a> |
                            <a href='delete_order.php?id=" . $row['id'] . "' class='text-red-500 hover:underline'>Xóa</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
