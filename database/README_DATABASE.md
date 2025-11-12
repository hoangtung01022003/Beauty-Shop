# 📊 HƯỚNG DẪN CÀI ĐẶT DATABASE

## 🎯 MỤC TIÊU
Tạo database `beauty_shop` và import dữ liệu mẫu

---

## 📋 CÁC BƯỚC THỰC HIỆN

### **Cách 1: Sử dụng phpMyAdmin (Khuyến nghị)**

#### Bước 1: Mở phpMyAdmin
- Truy cập: `http://localhost/phpmyadmin`
- Đăng nhập (mặc định XAMPP: username=`root`, password=rỗng)

#### Bước 2: Tạo Database
1. Click tab **"SQL"** ở menu trên
2. Paste toàn bộ nội dung file `schema.sql` vào ô SQL
3. Click nút **"Go"** (hoặc nhấn Ctrl+Enter)
4. Chờ thông báo thành công

#### Bước 3: Import dữ liệu mẫu
1. Chọn database `beauty_shop` ở sidebar bên trái
2. Click tab **"SQL"**
3. Paste toàn bộ nội dung file `seed_data.sql`
4. Click nút **"Go"**
5. Kiểm tra kết quả thống kê

---

### **Cách 2: Sử dụng MySQL Command Line**

```bash
# Bước 1: Mở Command Prompt (CMD)
# Bước 2: Di chuyển đến thư mục MySQL bin (XAMPP)
cd C:\xampp\mysql\bin

# Bước 3: Đăng nhập MySQL
mysql -u root -p
# (Nhấn Enter nếu không có password)

# Bước 4: Chạy file schema.sql
source D:/code_khach_hang/web_ban_my_pham/WebBanMyPham/database/schema.sql

# Bước 5: Chạy file seed_data.sql
source D:/code_khach_hang/web_ban_my_pham/WebBanMyPham/database/seed_data.sql

# Bước 6: Kiểm tra
USE beauty_shop;
SHOW TABLES;
SELECT COUNT(*) FROM products;
```

---

## ✅ KIỂM TRA SAU KHI IMPORT

### Kiểm tra các bảng đã tạo:
```sql
USE beauty_shop;
SHOW TABLES;
```

**Kết quả mong đợi:**
```
+------------------------+
| Tables_in_beauty_shop  |
+------------------------+
| categories             |
| order_items            |
| orders                 |
| products               |
| users                  |
+------------------------+
```

### Kiểm tra dữ liệu mẫu:
```sql
SELECT COUNT(*) as total_users FROM users;           -- 5 users
SELECT COUNT(*) as total_categories FROM categories; -- 6 categories
SELECT COUNT(*) as total_products FROM products;     -- 20 products
SELECT COUNT(*) as total_orders FROM orders;         -- 5 orders
```

---

## 🔐 THÔNG TIN ĐĂNG NHẬP MẪU

### Tài khoản Admin:
- **Username:** `admin`
- **Email:** `admin@beautyshop.com`
- **Password:** `123456`
- **Role:** `admin`

### Tài khoản User:
- **Username:** `user1`
- **Email:** `user1@gmail.com`
- **Password:** `123456`
- **Role:** `user`

---

## 🧪 TEST KẾT NỐI DATABASE

Chạy lệnh sau để test kết nối:

```bash
cd D:\code_khach_hang\web_ban_my_pham\WebBanMyPham
php config/database.php
```

**Kết quả mong đợi:**
```
=== KIỂM TRA KẾT NỐI DATABASE ===

✅ Kết nối database thành công!
📊 Database: beauty_shop
🌐 Host: localhost
👤 User: root
```

---

## 🐛 XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi: "Unknown database 'beauty_shop'"
**Nguyên nhân:** Chưa chạy file `schema.sql`
**Giải pháp:** Chạy lại file `schema.sql` trong phpMyAdmin

### Lỗi: "Access denied for user 'root'@'localhost'"
**Nguyên nhân:** Sai username hoặc password
**Giải pháp:** Kiểm tra lại file `config/database.php`, đảm bảo:
- `DB_USER = 'root'`
- `DB_PASS = ''` (rỗng với XAMPP mặc định)

### Lỗi: "SQLSTATE[HY000] [2002] No connection"
**Nguyên nhân:** MySQL chưa chạy
**Giải pháp:** Mở XAMPP Control Panel → Start MySQL

---

## 📝 GHI CHÚ

- Database sử dụng charset `utf8mb4` để hỗ trợ tiếng Việt
- Mật khẩu mẫu đã được mã hóa bằng `password_hash()`
- Dữ liệu mẫu có thể chỉnh sửa trong file `seed_data.sql`
