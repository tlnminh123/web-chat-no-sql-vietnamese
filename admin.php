<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: ../login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<script src="assets/admin.js"></script>
<link rel="stylesheet" href="assets/style-ad.css">
<link rel="icon" href="assets/Empty.ico">
</head>
<body>
<div class="admin-container">
  <aside class="sidebar"><h2>🔧 Admin panel</h2>
    <ul class="tab-links">
      <li><button data-tab="users" class="active">👥 Quản lý user</button></li>
      <li><button data-tab="forms">📂 Duyệt đơn</button></li>
      <li><button data-tab="register">➕ Tạo tài khoản</button></li>
      <li><button data-tab="userinfo">📑 Thông tin người dùng</button></li>
     <li><a href="../chat/index.php" class="btn-back">🔙 Quay lại chat</a></li>
    </ul>
  </aside>
  <main class="tab-content">
    <section id="users" class="tab active"><h3>👥 Quản lý người dùng</h3><div id="user-list"><em>Đang tải...</em></div></section>

    <section id="forms" class="tab"><h3>📂 Duyệt đơn</h3>
      <div class="form-tabs">
        <button class="form-tab-btn active" data-form="unban">Gỡ Ban</button>
        <button class="form-tab-btn" data-form="repass">Đổi mật khẩu</button>
        <button class="form-tab-btn" data-form="dk">Đăng ký</button>
      </div>
      <div id="form-list"><em>Đang tải...</em></div>
      <div id="form-content"></div>
    </section>
    <section id="register" class="tab"><h3>➕ Tạo tài khoản</h3>
      <form id="register-form"><input name="username" placeholder="username" required><input name="password" placeholder="password" required><select name="role">
        <option value="no">User</option><option value="role2">Admin 2</option><option value="yes">Admin 1</option>
      </select><button type="submit">Tạo</button></form>
      <div id="register-msg"></div>
    </section>
    <section id="userinfo" class="tab">
		 <button class="export-btn" onclick="window.location.href='export_users.php'" style="margin-bottom: 10px;">📤 Xuất Excel</button>
      <div id="userinfo-list"><em>Đang tải...</em></div>
    </section>
  </main>
</div>
</body>
</html>
