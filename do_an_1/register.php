<?php
include('db.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Tài khoản đã tồn tại hoặc lỗi!";
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
<body class="bg-green-50 flex items-center justify-center h-screen">

    <div class="bg-white shadow-xl rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-green-700">🔐 Đăng ký tài khoản</h2>

        <?php if (isset($error)) echo "<p class='text-red-500 text-sm mb-4'>$error</p>"; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="username" required placeholder="Tên đăng nhập"
                   class="w-full p-2 border border-gray-300 rounded focus:outline-green-500">
            <input type="password" name="password" required placeholder="Mật khẩu"
                   class="w-full p-2 border border-gray-300 rounded focus:outline-green-500">
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                Đăng ký
            </button>
        </form>

        <p class="text-sm text-center mt-4">Đã có tài khoản? <a href="login.php" class="text-blue-600 hover:underline">Đăng nhập</a></p>
    </div>

</body>
</html>
