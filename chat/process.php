<?php
session_start();
header("Content-Type: application/json");

$username = $_SESSION["username"] ?? null;
if (!$username) {
    echo json_encode(["status" => "unauthorized"]);
    exit;
}

$userFile = __DIR__ . "/../users/user.xml";
$msgFile = __DIR__ . "/../messages.xml";

if (!file_exists($userFile) || !file_exists($msgFile)) {
    echo json_encode(["status" => "file_error"]);
    exit;
}

$userXml = simplexml_load_file($userFile);
$chatXml = simplexml_load_file($msgFile);

// Kiểm tra ban
foreach ($userXml->user as $u) {
    if ((string)$u->username === $username && (string)$u->banned === "yes") {
        echo json_encode(["status" => "banned"]);
        exit;
    }
}

// Gửi tin nhắn
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] === "send") {
    $text = trim($_POST['message']);
    if ($text !== "") {
        // Lấy IP public thật
        function getUserIP() {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
        $ip = getUserIP();

        // Ghi tin nhắn vào messages.xml
        $m = $chatXml->addChild("message");
        $m->addChild("user", htmlspecialchars($username));
        $m->addChild("time", date("H:i:s d-m-Y"));
        $m->addChild("text", htmlspecialchars($text));
        $m->addChild("deleted", "no");
        $m->addChild("ip", htmlspecialchars($ip));
        $chatXml->asXML($msgFile);

        // Ghi IP vào user.xml (dưới tên trường <ip>)
        foreach ($userXml->user as $u) {
            if ((string)$u->username === $username) {
                if (isset($u->ip)) {
                    $u->ip = $ip; // cập nhật nếu đã có
                } else {
                    $u->addChild("ip", $ip); // thêm nếu chưa có
                }
                $userXml->asXML($userFile);
                break;
            }
        }

        echo json_encode(["status" => "ok"]);
        exit;
    }
}



// Lấy tin nhắn
if ($_GET['action'] === "fetch") {
    $result = [];
    foreach ($chatXml->message as $m) {
        $result[] = [
            "user" => (string)$m->user,
            "text" => (string)$m->text,
            "time" => (string)$m->time,
            "deleted" => (string)$m->deleted
        ];
    }
    echo json_encode(["status" => "ok", "messages" => $result]);
    exit;
}

echo json_encode(["status" => "unknown"]);
