# Real-time Web Chat (No SQL, PHP + XML)

Một hệ thống chat real-time xây dựng hoàn toàn bằng PHP, AJAX và lưu trữ bằng XML – không cần database. Giao diện hiện đại, hỗ trợ phân quyền admin.

## 🔥 Tính năng
- 🧑‍💬 Gửi tin nhắn
- 🎨 Đổi giao diện (theme)
- 🔐 Phân quyền Admin cấp 1 / cấp 2
- 🚫 Kiểm tra trạng thái tài khoản (ban / unban)
- 📁 Lưu trữ bằng XML, không cần MySQL

## 👤 Tài khoản dùng thử (Demo Account)

Dưới đây là danh sách các tài khoản được công bố công khai nhằm mục đích kiểm thử hệ thống. Người dùng có thể sử dụng các tài khoản này để đăng nhập vào hệ thống và thử nghiệm các chức năng.

| Loại tài khoản                  | Tên đăng nhập | Mật khẩu     | Quyền hạn                                           |
|---------------------------------|----------------|--------------|----------------------------------------------------|
| 👑 Quản trị viên (Admin cấp 1) | admin           | a            | Toàn quyền (ban user, đổi mật khẩu, chỉnh sửa...)  |
| 👤 Người dùng thường            | user1          | a            | Chat bình thường                                   |

> 📝 **Lưu ý:** Các tài khoản demo có thể bị reset bất kỳ lúc nào. Không nên sử dụng cho mục đích lưu trữ thông tin quan trọng.

---

### 💬 Mở góp ý & sửa lỗi

Nếu bạn gặp lỗi khi dùng tài khoản trên, vui lòng tạo issue hoặc liên hệ qua GitHub. Dự án này luôn chào đón sự đóng góp!



## 🚀 Cài đặt

```bash
git clone https://github.com/tlnminh123/web-chat-no-sql-vietnamese.git
cd web-chat-no-sql-vietnamese
composer install
