<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"]);
    $dob = trim($_POST["dob"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $message = trim($_POST["message"]);

    if ($password !== $confirm_password) {
        $error = "❌ Mật khẩu và xác nhận mật khẩu không khớp!";
    } else {
        $save_dir = __DIR__ . '/form/dk';
        if (!is_dir($save_dir)) {
            mkdir($save_dir, 0777, true);
        }

        $filename = $save_dir . "/dk_" . time() . ".txt";
        $content  = "Họ và tên: $fullname\n";
        $content .= "Ngày sinh: $dob\n";
        $content .= "Email: $email\n";
        $content .= "Mật khẩu đăng ký: $password\n";
        $content .= "Lời nhắn gửi Admin:\n$message\n";

        file_put_contents($filename, $content);
        $success = "📩 Đơn đăng ký đã được gửi! Admin sẽ sớm duyệt.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
        }
        .register-box {
            width: 420px;
            margin: auto;
            margin-top: 40px;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px #bbb;
        }
        .register-box h2 {
            text-align: center;
        }
        .register-box input,
        .register-box textarea {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .register-box button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 4px;
        }
        .register-box .notice {
            font-size: 13px;
            color: #555;
            margin-top: 10px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
        label {
            font-weight: bold;
        }
        .toggle-btn {
            position: absolute;
            right: 10px;
            top: 38px;
            cursor: pointer;
            font-size: 18px;
        }
        .password-group {
            position: relative;
        }
    </style>
    <script>
        function togglePassword(id, btnId) {
            var input = document.getElementById(id);
            var btn = document.getElementById(btnId);
            if (input.type === "password") {
                input.type = "text";
                btn.innerText = "🙈";
            } else {
                input.type = "password";
                btn.innerText = "👁️";
            }
        }
    </script>
</head>
<body>
    <div class="register-box">
        <h2>Đăng ký tài khoản</h2>

        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>

        <form method="post">
            <label for="fullname">Họ và tên:</label>
            <input type="text" id="fullname" name="fullname" placeholder="Nguyễn Văn A" required>

            <label for="dob">Ngày tháng năm sinh:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="email">Email liên lạc:</label>
            <input type="email" id="email" name="email" placeholder="email@example.com" required>

            <label for="password">Mật khẩu:</label>
            <div class="password-group">
                <input type="password" id="password" name="password" required>
                <span id="togglePass1" class="toggle-btn" onclick="togglePassword('password', 'togglePass1')">👁️</span>
            </div>

            <label for="confirm_password">Nhập lại mật khẩu:</label>
            <div class="password-group">
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span id="togglePass2" class="toggle-btn" onclick="togglePassword('confirm_password', 'togglePass2')">👁️</span>
            </div>

            <label for="message">Lời nhắn gửi Admin:</label>
            <textarea id="message" name="message" rows="4" placeholder="Tôi mong được tham gia hệ thống..." required></textarea>

            <div class="notice">
                🕓 <strong>Admin sẽ sớm duyệt đơn của bạn.</strong>
            </div>

            <button type="submit">Gửi đăng ký</button>
        </form>
    </div>
</body>
</html>
