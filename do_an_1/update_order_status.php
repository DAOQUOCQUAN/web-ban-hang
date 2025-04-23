<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db.php');

// Kiểm tra xem có tham số id trong URL không
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    
    // Lấy thông tin đơn hàng từ database
    $result = $conn->query("SELECT * FROM orders WHERE id = $order_id");
    $order = $result->fetch_assoc();
    
    if ($order) {
        // Cập nhật trạng thái đơn hàng
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $status = $_POST['status'];

            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $order_id);
            $stmt->execute();
            header("Location: orders.php");
            exit();
        }
    } else {
        echo "Không tìm thấy đơn hàng.";
    }
} else {
    echo "Không có đơn hàng nào để cập nhật.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật trạng thái đơn hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow mt-8">
    <h1 class="text-xl font-semibold mb-4">Cập nhật trạng thái đơn hàng</h1>
    <form method="POST">
        <div class="mb-4">
            <label for="status" class="block text-gray-700">Trạng thái đơn hàng</label>
            <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded">
                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chưa xử lý</option>
                <option value="processed" <?= $order['status'] == 'processed' ? 'selected' : '' ?>>Đã xử lý</option>
                <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Đã giao</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Cập nhật trạng thái</button>
    </form>
</div>

</body>
</html>
