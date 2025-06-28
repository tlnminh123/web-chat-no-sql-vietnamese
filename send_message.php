<?php
session_start();
if (!isset($_SESSION["username"])) {
    exit("Bạn chưa đăng nhập!");
}

$username = $_SESSION["username"];
$time = date("H:i:s d/m/Y");

// 📌 Lấy ngày hiện tại và ngày hôm qua
$today = date("d-m-Y");
$yesterday = date("d-m-Y", strtotime("-1 day"));

$filePath = "messages_$today.xml";  // File tin nhắn hôm nay
$oldFile = "messages_$yesterday.xml";  // File hôm qua
$backupDir = "backup/";

// 📌 Nếu file hôm qua tồn tại, chuyển vào thư mục backup
if (file_exists($oldFile)) {
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0777, true);
    }
    rename($oldFile, $backupDir . $oldFile);
}

// 📌 Kiểm tra nếu có dữ liệu POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    $message = htmlspecialchars($_POST["message"]);

    // 📌 Kiểm tra file tin nhắn hôm nay
    if (!file_exists($filePath) || filesize($filePath) == 0) {
        $xml = new SimpleXMLElement("<messages></messages>");
    } else {
        $xml = simplexml_load_file($filePath);
        if (!$xml) {
            $xml = new SimpleXMLElement("<messages></messages>");
        }
    }

    // 📌 Ghi tin nhắn vào XML
    $msg = $xml->addChild("message");
    $msg->addChild("user", $username);
    $msg->addChild("text", $message);
    $msg->addChild("time", $time);
    $msg->addChild("deleted", "no");

    // 📌 Lưu lại file
    $xml->asXML($filePath);
    echo "Tin nhắn đã gửi!";
}
?>
