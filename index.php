<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = strtolower($_SESSION["username"]);
$user_file = __DIR__ . "/users/user.xml";

if (!file_exists($user_file)) {
    die("❌ Không tìm thấy file user.xml.");
}

$users_data = simplexml_load_file($user_file);
$found = false;

foreach ($users_data->user as $user) {
    if ((string)$user->username === $username) {
        $found = true;
        $status = trim((string)$user->imformation_updates); // ⚠️ trim để tránh lỗi xuống dòng
        if ($status === "no") {
            // Chưa cập nhật thông tin
            header("Location: updates.php");
            exit();
        } else {
            // Đã cập nhật
            header("Location: /chat/index.php");
            exit();
        }
    }
}

if (!$found) {
    die("❌ Không tìm thấy người dùng trong user.xml.");
}
