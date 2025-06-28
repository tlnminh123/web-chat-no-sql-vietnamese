<?php
session_start();

if (!isset($_SESSION["username"])) {
    die("⛔ Bạn chưa đăng nhập.");
}

$username = strtolower($_SESSION["username"]);
$info_file = __DIR__ . "/users/user_informations.xml";

if (!file_exists($info_file)) {
    die("❌ Không tìm thấy user_informations.xml.");
}

$info_data = simplexml_load_file($info_file);
$user_info = null;

foreach ($info_data->user as $user) {
    if ((string)$user->username === $username) {
        $user_info = $user;
        break;
    }
}

if (!$user_info) {
    die("⚠️ Không có thông tin cá nhân. Có thể bạn chưa từng cập nhật.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân</title>
	<link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <style>
        body {
            font-family: Arial;
            background-color: #f2f2f2;
            padding: 30px;
        }
        .profile-box {
            background: white;
            width: 400px;
            margin: auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px #aaa;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 15px;
        }
        .info label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .info span {
            display: block;
            color: #333;
        }
        .edit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="profile-box">
        <h2>👤 Hồ sơ cá nhân</h2>

        <div class="info">
            <label>Họ và tên:</label>
            <span><?= htmlspecialchars($user_info->name) ?></span>
        </div>

        <div class="info">
            <label>Ngày sinh:</label>
            <span><?= htmlspecialchars($user_info->date_of_birth) ?></span>
        </div>

        <div class="info">
            <label>Email:</label>
            <span><?= htmlspecialchars($user_info->email) ?></span>
        </div>

        <form method="get" action="updates.php">
            <button type="submit" class="edit-btn">✏️ Sửa thông tin</button>
        </form>
    </div>
</body>
</html>
