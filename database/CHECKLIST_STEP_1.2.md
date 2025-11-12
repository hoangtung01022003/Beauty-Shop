# ✅ BƯỚC 1.2 - CẤU HÌNH DATABASE - CHECKLIST

## 🎯 MỤC TIÊU
Kết nối MySQL và tạo các bảng trong database beauty_shop

---

## 📋 CÔNG VIỆC CHI TIẾT

### ✅ Bước 1: Kiểm tra XAMPP đã chạy
- [ ] Mở **XAMPP Control Panel**
- [ ] Start **Apache** (nút Start bên cạnh Apache)
- [ ] Start **MySQL** (nút Start bên cạnh MySQL)
- [ ] Kiểm tra Apache và MySQL hiển thị chữ màu xanh

---

### ✅ Bước 2: Tạo Database trong phpMyAdmin

#### 2.1 Mở phpMyAdmin
- [ ] Mở trình duyệt
- [ ] Truy cập: `http://localhost/phpmyadmin`
- [ ] Đăng nhập thành công

#### 2.2 Import file schema.sql
- [ ] Click tab **"SQL"** ở menu trên
- [ ] Mở file `d:\code_khach_hang\web_ban_my_pham\WebBanMyPham\database\schema.sql`
- [ ] Copy toàn bộ nội dung
- [ ] Paste vào ô SQL trong phpMyAdmin
- [ ] Click nút **"Go"** (hoặc nhấn Ctrl+Enter)
- [ ] Thấy thông báo: "Query OK" hoặc "5 rows affected"

#### 2.3 Kiểm tra database đã tạo
- [ ] Refresh phpMyAdmin (F5)
- [ ] Thấy database `beauty_shop` ở sidebar bên trái
- [ ] Click vào database `beauty_shop`
- [ ] Thấy 5 bảng: `categories`, `order_items`, `orders`, `products`, `users`

---

### ✅ Bước 3: Import dữ liệu mẫu

#### 3.1 Import file seed_data.sql
- [ ] Chọn database `beauty_shop` (sidebar trái)
- [ ] Click tab **"SQL"**
- [ ] Mở file `d:\code_khach_hang\web_ban_my_pham\WebBanMyPham\database\seed_data.sql`
- [ ] Copy toàn bộ nội dung
- [ ] Paste vào ô SQL
- [ ] Click nút **"Go"**

#### 3.2 Kiểm tra dữ liệu đã import
- [ ] Click vào bảng `users` → Tab "Browse" → Thấy 5 users
- [ ] Click vào bảng `categories` → Tab "Browse" → Thấy 6 categories
- [ ] Click vào bảng `products` → Tab "Browse" → Thấy 20 products
- [ ] Click vào bảng `orders` → Tab "Browse" → Thấy 5 orders
- [ ] Click vào bảng `order_items` → Tab "Browse" → Thấy 6 items

---

### ✅ Bước 4: Test kết nối Database từ PHP

#### 4.1 Mở Command Prompt (CMD)
- [ ] Nhấn `Windows + R`
- [ ] Gõ `cmd` → Enter

#### 4.2 Chạy lệnh test
```bash
cd /d d:\code_khach_hang\web_ban_my_pham\WebBanMyPham
php config/database.php
```

#### 4.3 Kiểm tra kết quả
- [ ] Thấy dòng chữ: `✅ Kết nối database thành công!`
- [ ] Thấy dòng chữ: `📊 Database: beauty_shop`
- [ ] Thấy dòng chữ: `🌐 Host: localhost`
- [ ] Thấy dòng chữ: `👤 User: root`

---

## 🎉 KẾT QUẢ MONG ĐỢI

### ✅ File đã tạo:
- [x] `config/database.php` - Kết nối PDO
- [x] `config/constants.php` - Hằng số dự án
- [x] `database/schema.sql` - Tạo bảng
- [x] `database/seed_data.sql` - Dữ liệu mẫu
- [x] `database/README_DATABASE.md` - Hướng dẫn

### ✅ Database đã tạo:
- [ ] Database: `beauty_shop`
- [ ] 5 bảng: users, categories, products, orders, order_items
- [ ] Dữ liệu mẫu: 5 users, 6 categories, 20 products, 5 orders

### ✅ Kết nối hoạt động:
- [ ] File `config/database.php` kết nối thành công
- [ ] Test bằng lệnh `php config/database.php` → Thành công

---

## 🔐 THÔNG TIN QUAN TRỌNG

### Tài khoản Admin mẫu:
```
Username: admin
Email: admin@beautyshop.com
Password: 123456
```

### Tài khoản User mẫu:
```
Username: user1
Email: user1@gmail.com
Password: 123456
```

---

## 🐛 XỬ LÝ LỖI

### ❌ Lỗi: "php không được nhận dạng"
**Giải pháp:** Thêm PHP vào PATH
```bash
# Windows: Thêm vào System Environment Variables
C:\xampp\php
```

### ❌ Lỗi: "Unknown database 'beauty_shop'"
**Giải pháp:** Chạy lại file `schema.sql` trong phpMyAdmin

### ❌ Lỗi: "Access denied for user 'root'"
**Giải pháp:** Kiểm tra lại `config/database.php`:
- `DB_USER = 'root'`
- `DB_PASS = ''` (để trống)

### ❌ Lỗi: "No connection could be made"
**Giải pháp:** Start MySQL trong XAMPP Control Panel

---

## 🚀 BƯỚC TIẾP THEO

Sau khi hoàn thành checklist, thông báo cho tôi:

**"Bước 1.2 hoàn thành, database đã kết nối OK"**

Tôi sẽ hướng dẫn bạn:
- ✅ Tạo Base Model
- ✅ Tạo Base Controller
- ✅ Tạo Router (index.php)
- ✅ Test hoạt động MVC cơ bản

---

**📅 Ngày tạo:** 11/11/2025  
**👨‍💻 Người thực hiện:** GitHub Copilot
