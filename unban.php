<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"]);
    $dob = trim($_POST["dob"]);
    $username = strtolower(trim($_POST["username"]));
    $regret = trim($_POST["regret"]);

    $user_file = __DIR__ . "/users/user.xml";
    $info_file = __DIR__ . "/users/user_informations.xml";

    if (!file_exists($user_file) || !file_exists($info_file)) {
        $error = "❌ Không tìm thấy dữ liệu hệ thống.";
    } else {
        $users_data = simplexml_load_file($user_file);
        $info_data = simplexml_load_file($info_file);

        $userNode = null;
        $infoNode = null;

        // Tìm user
        foreach ($users_data->user as $u) {
            if ((string)$u->username === $username) {
                $userNode = $u;
                break;
            }
        }

        // Tìm thông tin cá nhân
        foreach ($info_data->user as $info) {
            if ((string)$info->username === $username) {
                $infoNode = $info;
                break;
            }
        }

        if (!$userNode || !$infoNode) {
            $error = "❗ Không tìm thấy tài khoản hoặc thông tin cá nhân.";
        } elseif ((string)$userNode->banned !== "yes") {
            $error = "✅ Tài khoản này không bị ban.";
        } else {
            // So sánh thông tin
            $matchInfo = (
                strtolower($fullname) === strtolower((string)$infoNode->name) &&
                $dob === (string)$infoNode->date_of_birth
            );

            if (!$matchInfo) {
                $error = "⚠️ Thông tin cá nhân không đúng. Bạn đang cố tình giả mạo?";
            } else {
                // Kiểm tra độ thành khẩn
                $regretLower = strtolower($regret);
                $honestWords = ['xin lỗi', 'hứa', 'không tái phạm', 'nhận lỗi', 'rút kinh nghiệm'];
                $isHonest = false;
                foreach ($honestWords as $word) {
                    if (strpos($regretLower, $word) !== false) {
                        $isHonest = true;
                        break;
                    }
                }

                if (!$isHonest) {
                    $error = "⚠️ Lời hối cải chưa đủ chân thành. Không đủ điều kiện gỡ ban!";
                } else {
                    $userNode->banned = "no";
                    $users_data->asXML($user_file);
                    $success = "✅ Bạn đã được gỡ ban nhờ lời hối cải chân thành!";
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đơn xin gỡ ban</title>
    <link rel="stylesheet" href="/assets/style-lo.css">
	<link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }
        .unban-box {
            width: 400px;
            margin: auto;
            margin-top: 40px;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        .unban-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .unban-box input,
        .unban-box textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .unban-box button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 4px;
        }
        .unban-box .notice {
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
    <div class="unban-box">
        <h2>Đơn xin gỡ ban</h2>

        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>

        <form method="post">
            <label for="fullname">Họ và tên:</label>
            <input type="text" id="fullname" name="fullname" placeholder="Nguyễn Văn A" required>

            <label for="dob">Ngày tháng năm sinh:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" placeholder="vd: admin" required>

            <label for="regret">Lời hối cải:</label>
            <textarea id="regret" name="regret" rows="5" placeholder="Viết lời hối cải chân thành tại đây..." required></textarea>

            <div class="notice">
                ⚠️ <strong>Admin sẽ không cần mật khẩu tài khoản của bạn.</strong><br>
                ❤️ Nếu hối cải tốt, bạn sẽ sớm được gỡ ban!
            </div>

            <button type="submit">Gửi đơn</button>
        </form>
    </div>
</body>
</html>
