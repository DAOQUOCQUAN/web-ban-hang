<?php
session_start();
include('db.php');

if (!isset($_SESSION['ricpay_order_id'])) {
    header("Location: home.php");
    exit();
}

$order_id = $_SESSION['ricpay_order_id'];
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'] ?? '';

    if ($otp === '123456') {
        $stmt = $conn->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        unset($_SESSION['ricpay_order_id']);
        $_SESSION['message'] = "Thanh toán qua Ví RicPay thành công!";
        header("Location: home.php");
        exit();
    } else {
        $error = "Mã OTP không đúng. Vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác minh RicePay</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4">🔐 Xác minh thanh toán RicPay</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <p class="mb-4">Nhập mã OTP đã gửi tới số điện thoại của bạn để xác nhận thanh toán:</p>
        <form method="POST">
            <input type="text" name="otp" placeholder="Nhập mã OTP (ví dụ: 123456)" required class="border p-2 rounded w-full mb-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded w-full">✅ Xác nhận</button>
        </form>
    </div>
</body>
</html>
