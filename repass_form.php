<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"]);
    $dob = trim($_POST["dob"]);
    $username = strtolower(trim($_POST["username"]));
    $old_password_hint = trim($_POST["old_password_hint"]);
    $admin_message = trim($_POST["admin_message"]);

    $user_file = __DIR__ . "/users/user.xml";
    $info_file = __DIR__ . "/users/user_informations.xml";

    if (!file_exists($user_file) || !file_exists($info_file)) {
        $error = "❌ Không tìm thấy dữ liệu người dùng.";
    } else {
        $usersXml = simplexml_load_file($user_file);
        $infosXml = simplexml_load_file($info_file);

        $userMatch = null;
        $infoMatch = null;

        // Tìm tài khoản
        foreach ($usersXml->user as $user) {
            if ((string)$user->username === $username) {
                $userMatch = $user;
                break;
            }
        }

        // Tìm thông tin tài khoản
        foreach ($infosXml->user as $info) {
            if ((string)$info->username === $username) {
                $infoMatch = $info;
                break;
            }
        }

        if (!$userMatch || !$infoMatch) {
            $error = "❗ Không tìm thấy tài khoản hoặc thông tin tương ứng.";
        } else {
            $correctPassword = (
                $old_password_hint === (string)$userMatch->password ||
                $old_password_hint === (string)$userMatch->old_password
            );

            $correctInfo = (
                strtolower($fullname) === strtolower((string)$infoMatch->name) &&
                $dob === (string)$infoMatch->date_of_birth
            );

            if ($correctPassword && $correctInfo) {
                $userMatch->password = 'a';
                $usersXml->asXML($user_file);
                $success = "✅ Chứng minh thành công! Mật khẩu đã được đặt lại về 'a'.";
            } else {
                // Nếu sai → lưu đơn
                $save_dir = __DIR__ . "/form/repass";
                if (!is_dir($save_dir)) mkdir($save_dir, 0777, true);

                $filename = $save_dir . "/repass_" . $username . "_" . time() . ".txt";
                $content  = "Họ và tên: $fullname\n";
                $content .= "Ngày sinh: $dob\n";
                $content .= "Tên tài khoản: $username\n";
                $content .= "Mật khẩu nhớ gần đây: $old_password_hint\n";
                $content .= "Lời nhắn gửi Admin:\n$admin_message\n";

                file_put_contents($filename, $content);
                $success = "📩 Yêu cầu đã được gửi, admin sẽ duyệt sớm!";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Yêu cầu đổi mật khẩu</title>
    <link rel="stylesheet" href="/assets/style-lo.css">
	<link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }
        .reset-box {
            width: 400px;
            margin: auto;
            margin-top: 40px;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        .reset-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .reset-box input,
        .reset-box textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .reset-box button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 4px;
        }
        .reset-box .notice {
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
    </style>
</head>
<body>
    <div class="reset-box">
        <h2>Yêu cầu đổi mật khẩu</h2>

        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>

        <form method="post">
            <label for="fullname">Họ và tên:</label>
            <input type="text" id="fullname" name="fullname" placeholder="VD: Nguyễn Văn A" required>

            <label for="dob">Ngày tháng năm sinh:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="username">Tên tài khoản:</label>
            <input type="text" id="username" name="username" placeholder="VD: admin" required>

            <label for="old_password_hint">Mật khẩu nhớ gần đây:</label>
            <input type="text" id="old_password_hint" name="old_password_hint" placeholder="abc123 hoặc đại loại..." required>

            <label for="admin_message">Lời nhắn gửi Admin:</label>
            <textarea id="admin_message" name="admin_message" rows="4" placeholder="Viết lời nhắn gửi admin tại đây..." required></textarea>

            <div class="notice">
                ⚠️ <strong>Admin sẽ sớm duyệt đơn của bạn.</strong><br>
                🔁 Nếu được duyệt, mật khẩu sẽ được đặt lại về mặc định là <strong>'a'</strong>.<br>
                🚫 Nếu không được duyệt, bạn nên <strong>đăng ký tài khoản mới</strong>.
            </div>

            <button type="submit">Gửi yêu cầu</button>
        </form>
    </div>
</body>
</html>
