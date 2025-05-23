<?php
include('db.php');

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_raw = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($username === $password_raw) {
        $error = "❌ Tên đăng nhập và mật khẩu không được giống nhau!";
    } elseif ($password_raw !== $confirm_password) {
        $error = "❌ Mật khẩu xác nhận không khớp!";
    } else {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "❌ Tên đăng nhập đã tồn tại!";
        } else {
            $password = password_hash($password_raw, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $insert_stmt->bind_param("ss", $username, $password);
            if ($insert_stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "❌ Lỗi khi tạo tài khoản!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-2xl rounded-xl p-8 w-full max-w-md border-t-4 border-green-600">
        <h2 class="text-3xl font-bold mb-6 text-center text-green-700">🔐 Đăng ký tài khoản</h2>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="username" required placeholder="Tên đăng nhập"
                   class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
            <input type="password" name="password" required placeholder="Mật khẩu"
                   class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
            <input type="password" name="confirm_password" required placeholder="Xác nhận mật khẩu"
                   class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
            <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-lg text-lg font-semibold hover:bg-green-700 transition">
                Đăng ký
            </button>
        </form>

        <p class="text-sm text-center mt-5">Đã có tài khoản? 
            <a href="login.php" class="text-blue-600 hover:underline font-medium">Đăng nhập</a>
        </p>
    </div>

</body>
</html>
