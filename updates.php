<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION["username"])) {
    die("⛔ Bạn chưa đăng nhập.");
}

$username = strtolower($_SESSION["username"]);
$user_file = __DIR__ . "/users/user.xml";
$info_file = __DIR__ . "/users/user_informations.xml";

// Kiểm tra tồn tại file
if (!file_exists($user_file)) die("❌ Không tìm thấy user.xml.");
if (!file_exists($info_file)) file_put_contents($info_file, "<users></users>");

$users_data = simplexml_load_file($user_file);
$info_data = simplexml_load_file($info_file);
$target_user = null;

// Kiểm tra trạng thái cập nhật trong user.xml
foreach ($users_data->user as $user) {
    if ((string)$user->username === $username) {
        if ((string)$user->imformation_updates === "yes") {
            header("Location: /chat/index.php");
            exit();
        }
        $target_user = $user;
        break;
    }
}

if (!$target_user) die("❌ Không tìm thấy thông tin người dùng.");

// Xử lý khi submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $dob = trim($_POST["dob"]);
    $email = trim($_POST["email"]);

    // Tìm user trong user_informations.xml
    $found = false;
    foreach ($info_data->user as $info_user) {
        if ((string)$info_user->username === $username) {
            $info_user->name = $name;
            $info_user->date_of_birth = $dob;
            $info_user->email = $email;
            $found = true;
            break;
        }
    }

    // Nếu không tìm thấy thì thêm mới (dự phòng)
    if (!$found) {
        $new_info = $info_data->addChild("user");
        $new_info->addChild("username", $username);
        $new_info->addChild("name", $name);
        $new_info->addChild("date_of_birth", $dob);
        $new_info->addChild("email", $email);
    }

    // Lưu lại file
    $info_data->asXML($info_file);

    // Cập nhật trạng thái trong user.xml
    $target_user->imformation_updates = "yes";
    $users_data->asXML($user_file);

    // Chuyển hướng
    header("Location: /chat/index.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin</title>
	<link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 30px;
        }
        .form-box {
            width: 400px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 8px #ccc;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 20px;
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .note {
            font-size: 0.9em;
            color: #555;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>📝 Cập nhật thông tin</h2>
        <form method="post">
            <label for="name">Họ và tên:</label>
            <input type="text" name="name" id="name" required>

            <label for="dob">Ngày sinh:</label>
            <input type="date" name="dob" id="dob" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <button type="submit">Cập nhật</button>
        </form>
        <div class="note">Thông tin này sẽ được gửi cho admin và dùng để xác minh tài khoản.</div>
    </div>
</body>
</html>
