<?php
session_start();
include('db.php');

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            header("Location: home.php");
            exit();
        } else {
            $error = "❌ Sai mật khẩu!";
        }
    } else {
        $error = "❌ Tài khoản không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - RicRice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-2xl rounded-xl p-8 w-full max-w-md border-t-4 border-green-600">
        <h2 class="text-3xl font-bold mb-6 text-center text-green-700">🔑 Đăng nhập</h2>

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
            <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-lg text-lg font-semibold hover:bg-green-700 transition">
                Đăng nhập
            </button>
        </form>

        <p class="text-sm text-center mt-5">Chưa có tài khoản? 
            <a href="register.php" class="text-blue-600 hover:underline font-medium">Đăng ký ngay</a>
        </p>
    </div>

</body>
</html>
