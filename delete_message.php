<?php
session_start();

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    exit("Bạn không có quyền!");
}

$filePath = __DIR__ . '/messages.xml';

if (!file_exists($filePath)) {
    exit("Không tìm thấy file tin nhắn!");
}

$xml = simplexml_load_file($filePath);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    $index = intval($_POST["delete"]);
    
    if (isset($xml->message[$index])) {
        $xml->message[$index]->deleted = "yes";  // Đánh dấu tin nhắn đã bị xóa
        $xml->asXML($filePath);
    }
}

header("Location: /chat/index.php"); // Quay lại trang chat
exit();
?>
