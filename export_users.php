<?php
session_start();
if (!isset($_SESSION['username'])) die("Bạn chưa đăng nhập");

require 'vendor/autoload.php'; // Đường dẫn tới autoload của Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$xmlFile = __DIR__ . '/users/user.xml';
$infoFile = __DIR__ . '/users/user_informations.xml';

if (!file_exists($xmlFile) || !file_exists($infoFile)) die("Thiếu file XML");

$users = simplexml_load_file($xmlFile);
$infos = simplexml_load_file($infoFile);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Danh sách user');

// Thêm cột IP gần đây
$sheet->fromArray(['STT', 'Username', 'Banned', 'Role', 'IP gần đây', 'Họ tên', 'Ngày sinh', 'Email'], null, 'A1');

$row = 2;
$stt = 1;

foreach ($users->user as $user) {
    $username = (string)$user->username;
    if ($username === 'admin') continue;

    $banned = (string)$user->banned === 'yes' ? 'Có' : 'Không';
    $role = match ((string)$user->role) {
        'yes' => 'Admin cấp 1',
        'role2' => 'Admin cấp 2',
        default => 'User thường',
    };

    // IP lấy từ trường <ip> trong user.xml
    $ip = isset($user->ip) ? (string)$user->ip : 'Không có';

    // Lấy info tương ứng
    $name = $dob = $email = 'Không có';
    foreach ($infos->user as $info) {
        if ((string)$info->username === $username) {
            $name = (string)$info->name;
            $dob = (string)$info->date_of_birth;
            $email = (string)$info->email;
            break;
        }
    }

    $sheet->fromArray([$stt++, $username, $banned, $role, $ip, $name, $dob, $email], null, "A{$row}");
    $row++;
}

// Xuất file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="User_lists.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
