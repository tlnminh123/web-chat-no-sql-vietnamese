<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
    <link rel="icon" type="image/x-icon" href="Empty.ico">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Game</h1>
    <div class="folder-list">
        <a href="/chat/index.php">Quay lại</a>
        <?php
        $dirs = array_filter(glob('*'), 'is_dir');
        foreach ($dirs as $dir) {
            if (file_exists("$dir/index.html")) {
                echo "<a href=\"$dir/index.html\">$dir</a>";
            }
        }
        ?>
    </div>
</body>
</html>
