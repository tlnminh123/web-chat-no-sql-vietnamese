<?php 
session_start();

// Đường dẫn file XML chứa thông tin user
$xml_file = "D:/windows/xampp/htdocs/users/user.xml";

// Hàm kiểm tra quyền admin
function isAdmin($xml_file, $username) {
    if (!file_exists($xml_file)) return false;
    $xml = simplexml_load_file($xml_file);
    foreach ($xml->user as $user) {
        if ((string)$user->username === $username) {
            return ((string)$user->role === "yes");
        }
    }
    return false;
}

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['username']) || !isAdmin($xml_file, $_SESSION['username'])) {
    header('HTTP/1.1 403 Forbidden');
    include 'forbidden.php';
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
$message = '';

// Xoá file nếu có yêu cầu
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    $deletePath = $uploadDir . $fileToDelete;
    if (file_exists($deletePath)) {
        unlink($deletePath);
        $message = "Đã xoá file: " . htmlspecialchars($fileToDelete);
    } else {
        $message = "File không tồn tại hoặc đã bị xoá.";
    }
}

// Xử lý upload
if (isset($_POST['uploadBtn'])) {
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
        $fileName = basename($_FILES['fileUpload']['name']);
        $fileSize = $_FILES['fileUpload']['size'];
        $fileType = $_FILES['fileUpload']['type'];

        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowedTypes = ['text/plain', 'image/jpeg', 'image/png'];

        if ($fileSize > $maxFileSize) {
            $message = "File quá lớn, tối đa 5MB.";
        } elseif (!in_array($fileType, $allowedTypes)) {
            $message = "Loại file không được phép. Chỉ cho phép txt, jpg, png.";
        } else {
            $destPath = $uploadDir . $fileName;
            if (file_exists($destPath)) {
                $message = "File đã tồn tại trên server.";
            } else {
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $message = "Upload file thành công: " . htmlspecialchars($fileName);
                } else {
                    $message = "Có lỗi xảy ra khi tải file lên.";
                }
            }
        }
    } else {
        $message = "Không có file nào được tải lên hoặc có lỗi trong quá trình upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thử Nghiệm</title>
    <link rel="stylesheet" href="styles.css">
	<link rel="icon" type="image/x-icon" href="Empty.ico" />
    <style>
        .box-centered { margin: 20px auto; width: 80%; max-width: 800px; }
        .message.success { color: green; }
        .message { color: red; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
		
		.back-button-container {
    text-align: center;
    margin-top: 20px;
}

a.back-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007BFF;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: background-color 0.2s ease;
}
a.back-button:hover {
    background-color: #0056b3;
}


    </style>
</head>
<body>

    <div class="box-centered">
        <h1>Quản lý Upload</h1>
        <div class="back-button-container">
    <a href="/chat/index.php" class="back-button">← Quay lại</a>
</div>
        <?php 
        if ($message !== '') {
            $class = strpos($message, 'thành công') !== false || strpos($message, 'Đã xoá') !== false ? 'success' : '';
            echo '<p class="message ' . $class . '">' . htmlspecialchars($message) . '</p>';
        }
        ?>
    </div>

    <!-- Danh sách file -->
    <div class="box-centered">
        <h2>Danh sách file trong thư mục uploads</h2>
        <?php
        if (is_dir($uploadDir)) {
            $allFiles = array_diff(scandir($uploadDir), ['.', '..']);
            $filesOnly = array_filter($allFiles, function($file) use ($uploadDir) {
                return is_file($uploadDir . $file);
            });
            sort($filesOnly);

            if (count($filesOnly) > 0) {
                echo "<table>";
                echo "<tr><th>Tên file</th><th>Dung lượng (KB)</th><th>Ngày upload</th><th>Hành động</th></tr>";
                foreach ($filesOnly as $fileName) {
                    $filePath = $uploadDir . $fileName;
                    $fileSizeKB = round(filesize($filePath) / 1024, 2);
                    $uploadTime = date("d/m/Y H:i", filemtime($filePath));
                    $encoded = urlencode($fileName);
                    echo "<tr>
                        <td><a href=\"uploads/$encoded\" download>$fileName</a></td>
                        <td>{$fileSizeKB} KB</td>
                        <td>$uploadTime</td>
                        <td><a href=\"?delete=$encoded\" onclick=\"return confirm('Bạn có chắc muốn xoá file này không?');\">Xoá</a></td>
                    </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Không có file nào trong thư mục uploads.</p>";
            }
        } else {
            echo "<p>Chưa có thư mục uploads.</p>";
        }
        ?>
    </div>

    <!-- Form upload -->
    <div class="box-centered">
        <h2>Upload file mới</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="fileUpload">Chọn file (txt, jpg, png; tối đa 5MB):</label>
            <input type="file" name="fileUpload" id="fileUpload" required>
            <button type="submit" name="uploadBtn">Upload</button>
        </form>
    </div>

</body>
</html>
