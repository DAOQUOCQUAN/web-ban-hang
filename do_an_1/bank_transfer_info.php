<?php
session_start();

if (!isset($_SESSION['bank_order_id'])) {
    header("Location: home.php");
    exit();
}

$order_id = $_SESSION['bank_order_id'];
unset($_SESSION['bank_order_id']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chuyển khoản ngân hàng - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-yellow-50 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-xl shadow-md border border-yellow-300">
        <h1 class="text-2xl font-bold text-yellow-700 mb-4">🏦 Hướng dẫn chuyển khoản ngân hàng</h1>

        <p class="mb-3 text-gray-700">
            Đơn hàng của bạn <strong>#<?= htmlspecialchars($order_id) ?></strong> đã được ghi nhận.
            Vui lòng chuyển khoản theo thông tin bên dưới:
        </p>

        <div class="bg-yellow-100 p-4 rounded-lg border border-yellow-300 mb-4">
            <p><strong>🔸 Ngân hàng:</strong> Vietcombank (VCB)</p>
            <p><strong>🔸 Số tài khoản:</strong> <span class="font-mono text-lg">0123456789</span></p>
            <p><strong>🔸 Tên người nhận:</strong> Cửa hàng RicRice</p>
            <p><strong>🔸 Nội dung chuyển khoản:</strong>
                <span class="font-semibold bg-yellow-200 px-2 py-1 rounded text-yellow-900">
                    RicRice-<?= htmlspecialchars($order_id) ?>
                </span>
            </p>
        </div>

        <p class="mb-4 text-gray-600">
            ⚠️ <em>Hãy chuyển khoản đúng nội dung để chúng tôi xác minh và xử lý nhanh đơn hàng của bạn.</em>
        </p>

        <a href="home.php"
           class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded shadow-md">
            🏠 Quay về trang chủ
        </a>
    </div>
</body>
</html>

