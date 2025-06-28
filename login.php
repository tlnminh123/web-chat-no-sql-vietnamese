<?php
session_start();

// Nếu đã đăng nhập thì chuyển luôn
if (isset($_SESSION["username"])) {
    header("Location: /chat/index.php");
    exit();
}

$error = "";

// Xử lý khi submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = strtolower(trim($_POST["username"]));
    $password = trim($_POST["password"]);
    $user_file = __DIR__ . "/users/user.xml";

    if (file_exists($user_file)) {
        $users_data = simplexml_load_file($user_file);

        $username_found = false;

        foreach ($users_data->user as $user) {
            if ((string)$user->username === $username) {
                $username_found = true;

                if ((string)$user->password === $password) {
                    // 👉 CHẶN NẾU BỊ BAN
                    if ((string)$user->banned === "yes") {
                        $error = "🚫 Bạn đã bị ban bởi Admin. Nhấn vào <a href='/unban.php'>đây</a> để yêu cầu gỡ ban.";
                        break;
                    }

                    // Đăng nhập thành công
                    $_SESSION["username"] = $username;
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "❌ Sai tên đăng nhập hoặc mật khẩu! Nếu quên mật khẩu, nhấn vào <a href='/repass_form.php'>đây</a> để yêu cầu đổi mật khẩu.";
                    break;
                }
            }
        }

        if (!$username_found) {
            $error = "❗ Tài khoản này không có trong hệ thống. Nhấn vào <a href='/dang_ki.php'>đây</a> để đăng ký.";
;
        }
    } else {
        $error = "⚠️ Hệ thống chưa có tài khoản nào!";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <link rel="stylesheet" href="assets/style-lo.css">
</head>
<body>
    <div class="login-box">
        <form method="post">
            <h2>Đăng nhập</h2>
            <img src="/assets/whc.webp" alt="Logo" class="logo">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>
        </form>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
