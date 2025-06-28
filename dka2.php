<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$xml_file = __DIR__ . "/users/user.xml";
$info_file = __DIR__ . "/users/user_informations.xml";

// Hàm lấy role
function getUserRole(string $username, SimpleXMLElement $xml): string {
    foreach ($xml->user as $user) {
        if ((string)$user->username === $username) {
            return (string)$user->role;
        }
    }
    return 'no';
}

if (!file_exists($xml_file)) {
    die("File user XML không tồn tại");
}

$usersXml = simplexml_load_file($xml_file);

// Chỉ admin cấp 1 hoặc 2 được vào
$userRole = getUserRole($_SESSION['username'], $usersXml);
if ($userRole !== 'yes' && $userRole !== 'role2') {
    header("Location: /chat/index.php");
    exit();
}

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['register_username'] ?? '');
    $password = trim($_POST['register_password'] ?? '');
    $role = $_POST['register_role'] ?? 'no';

    if ($username === '' || $password === '') {
        $error = "Tên đăng nhập và mật khẩu không được để trống.";
    } else {
        foreach ($usersXml->user as $user) {
            if ((string)$user->username === $username) {
                $error = "Tên đăng nhập đã tồn tại.";
                break;
            }
        }

        if (!isset($error)) {
            // Thêm vào user.xml
            $newUser = $usersXml->addChild('user');
            $newUser->addChild('username', $username);
            $newUser->addChild('password',$password);
            $newUser->addChild('banned', 'no');
            $newUser->addChild('role', $role);
			$newUser->addChild('imformation_updates', 'no');
            $usersXml->asXML($xml_file);

            // Thêm vào user_informations.xml
            if (!file_exists($info_file)) {
                $infoXml = new SimpleXMLElement("<users></users>");
            } else {
                $infoXml = simplexml_load_file($info_file);
            }

            $nextStt = count($infoXml->user) + 1;

            $newInfo = $infoXml->addChild('user');
            $newInfo->addChild('stt', $nextStt);
            $newInfo->addChild('username', $username);
            $newInfo->addChild('name', 'no');
            $newInfo->addChild('date_of_birth', 'no');
            $newInfo->addChild('email', 'no');
            $newInfo->addChild('information_updates', 'no');

            $infoXml->asXML($info_file);

            $success = "Tạo tài khoản '$username' thành công!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Đăng ký tài khoản mới</title>
    <link rel="icon" type="image/x-icon" href="assets/Empty.ico" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-box {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px 30px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            width: 320px;
            box-sizing: border-box;
        }
        .register-box h2 {
            margin-top: 0;
            margin-bottom: 15px;
            font-weight: 600;
            color: #333;
            text-align: center;
        }
        .register-box input[type="text"],
        .register-box input[type="password"],
        .register-box select {
            width: 100%;
            padding: 9px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .register-box button {
            width: 100%;
            padding: 10px;
            background-color: #2980b9;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
        }
        .register-box button:hover {
            background-color: #1f6391;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .message.error {
            color: #c0392b;
        }
        .message.success {
            color: #27ae60;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .register-box img.logo {
            display: block;
            margin: 0 auto 20px auto;
            width: 180px;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
            cursor: default;
        }
        .register-box img.logo:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0,0,0,0.25);
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Đăng ký tài khoản mới</h2>
        <img src="/assets/whc.webp" alt="Logo" class="logo">
        <?php if (isset($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="register_username" placeholder="Tên đăng nhập" required />
            <input type="password" name="register_password" placeholder="Mật khẩu" required />
            <select name="register_role" aria-label="Chọn quyền">
                <option value="no" selected>User thường</option>
                <option value="role2">Admin cấp 2 (không khuyến khích)</option>
            </select>
            <button type="submit">Tạo tài khoản</button>
        </form>
    </div>

    <button onclick="history.back()" style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 160px;
        height: 120px;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        user-select: none;
    ">
        🔙 Quay lại
    </button>
</body>
</html>
