# ✅ GIAI ĐOẠN 1 - CHUẨN BỊ CƠ BẢN - CHECKLIST

## 🎯 MỤC TIÊU
Tạo cấu trúc thư mục và file hoàn chỉnh theo mô hình MVC cho website bán mỹ phẩm

---

## 📋 CÔNG VIỆC CHI TIẾT

### 1️⃣ Phân tích Flow Kiến trúc MVC
- [x] Hiểu rõ luồng xử lý: Request → Router → Controller → Model → Database → View → Response
- [x] Nắm được vai trò từng thành phần: Config, Controller, Model, View, Helper, Public

### 2️⃣ Tạo Cấu trúc Thư mục Chính
- [x] `config/` - Thư mục cấu hình
- [x] `controllers/` - Thư mục controllers
- [x] `controllers/Admin/` - Controllers admin
- [x] `models/` - Thư mục models
- [x] `views/` - Thư mục views
- [x] `helpers/` - Thư mục helpers
- [x] `public/` - Thư mục public
- [x] `uploads/` - Thư mục upload

### 3️⃣ Tạo File Config
- [x] `config/database.php` - Cấu hình kết nối MySQL
- [x] `config/constants.php` - Hằng số dự án

### 4️⃣ Tạo Controllers
**Controllers User:**
- [x] `controllers/AuthController.php` - Xử lý đăng ký/đăng nhập
- [x] `controllers/ProductController.php` - Xử lý sản phẩm
- [x] `controllers/CartController.php` - Xử lý giỏ hàng
- [x] `controllers/OrderController.php` - Xử lý đơn hàng

**Controllers Admin:**
- [x] `controllers/Admin/DashboardController.php` - Dashboard admin
- [x] `controllers/Admin/ProductAdminController.php` - Quản lý sản phẩm
- [x] `controllers/Admin/UserAdminController.php` - Quản lý người dùng
- [x] `controllers/Admin/CategoryAdminController.php` - Quản lý danh mục
- [x] `controllers/Admin/OrderAdminController.php` - Quản lý đơn hàng

### 5️⃣ Tạo Models
- [x] `models/User.php` - Model người dùng
- [x] `models/Product.php` - Model sản phẩm
- [x] `models/Category.php` - Model danh mục
- [x] `models/Cart.php` - Model giỏ hàng
- [x] `models/Order.php` - Model đơn hàng
- [x] `models/OrderItem.php` - Model chi tiết đơn hàng

### 6️⃣ Tạo Helpers
- [x] `helpers/Auth.php` - Hỗ trợ kiểm tra đăng nhập
- [x] `helpers/Validator.php` - Hỗ trợ validate dữ liệu
- [x] `helpers/FileUpload.php` - Hỗ trợ upload file
- [x] `helpers/Helper.php` - Hàm tiện ích chung

### 7️⃣ Tạo Views - Layouts
- [x] `views/layouts/header.php` - Header chung
- [x] `views/layouts/footer.php` - Footer chung
- [x] `views/layouts/nav.php` - Navigation
- [x] `views/layouts/sidebar.php` - Sidebar admin

### 8️⃣ Tạo Views - Auth
- [x] `views/auth/register.php` - Trang đăng ký
- [x] `views/auth/login.php` - Trang đăng nhập
- [x] `views/auth/forgot-password.php` - Quên mật khẩu

### 9️⃣ Tạo Views - User
- [x] `views/user/home.php` - Trang chủ
- [x] `views/user/products/list.php` - Danh sách sản phẩm
- [x] `views/user/products/detail.php` - Chi tiết sản phẩm
- [x] `views/user/cart/view.php` - Giỏ hàng
- [x] `views/user/checkout/checkout.php` - Thanh toán
- [x] `views/user/checkout/success.php` - Thanh toán thành công
- [x] `views/user/profile/my-orders.php` - Đơn hàng của tôi

### 🔟 Tạo Views - Admin Dashboard
- [x] `views/admin/dashboard/index.php` - Trang chủ admin
- [x] `views/admin/dashboard/cards/stats-card.php` - Card thống kê
- [x] `views/admin/dashboard/cards/chart.php` - Biểu đồ
- [x] `views/admin/dashboard/widgets/recent-orders.php` - Đơn hàng gần đây

### 1️⃣1️⃣ Tạo Views - Admin Products
- [x] `views/admin/products/list.php` - Danh sách sản phẩm
- [x] `views/admin/products/add.php` - Thêm sản phẩm
- [x] `views/admin/products/edit.php` - Sửa sản phẩm
- [x] `views/admin/products/_form.php` - Form tái sử dụng

### 1️⃣2️⃣ Tạo Views - Admin Categories
- [x] `views/admin/categories/list.php` - Danh sách danh mục
- [x] `views/admin/categories/add.php` - Thêm danh mục
- [x] `views/admin/categories/edit.php` - Sửa danh mục
- [x] `views/admin/categories/_form.php` - Form tái sử dụng

### 1️⃣3️⃣ Tạo Views - Admin Users
- [x] `views/admin/users/list.php` - Danh sách người dùng
- [x] `views/admin/users/edit.php` - Sửa người dùng
- [x] `views/admin/users/_form.php` - Form tái sử dụng

### 1️⃣4️⃣ Tạo Views - Admin Orders
- [x] `views/admin/orders/list.php` - Danh sách đơn hàng
- [x] `views/admin/orders/detail.php` - Chi tiết đơn hàng

### 1️⃣5️⃣ Tạo Views - Components
- [x] `views/components/alert.php` - Component thông báo
- [x] `views/components/pagination.php` - Component phân trang
- [x] `views/components/product-card.php` - Component card sản phẩm

### 1️⃣6️⃣ Tạo Public Assets
**CSS:**
- [x] `public/css/style.css` - CSS tùy chỉnh user
- [x] `public/css/admin.css` - CSS tùy chỉnh admin

**JavaScript:**
- [x] `public/js/main.js` - JS chung
- [x] `public/js/cart.js` - JS giỏ hàng

**Images:**
- [x] `public/images/products/` - Thư mục hình sản phẩm

### 1️⃣7️⃣ Tạo Uploads
- [x] `uploads/products/` - Thư mục lưu upload sản phẩm

### 1️⃣8️⃣ Tạo File Gốc
- [x] `index.php` - Entry point (Router chính)
- [x] `.htaccess` - URL Rewrite
- [x] `README.md` - Tài liệu dự án

---

## ✅ CHECKLIST KIỂM TRA CUỐI CÙNG

### Kiểm tra cấu trúc thư mục:
- [ ] Mở thư mục `d:\code_khach_hang\web_ban_my_pham\WebBanMyPham`
- [ ] Kiểm tra có đầy đủ 8 thư mục chính: config, controllers, models, views, helpers, public, uploads, và các file gốc
- [ ] Kiểm tra thư mục `controllers/Admin/` có 5 file controller
- [ ] Kiểm tra thư mục `models/` có 6 file model
- [ ] Kiểm tra thư mục `views/` có đầy đủ các thư mục con: layouts, auth, user, admin, components
- [ ] Kiểm tra thư mục `helpers/` có 4 file helper
- [ ] Kiểm tra thư mục `public/` có css, js, images
- [ ] Kiểm tra thư mục `uploads/products/` đã tồn tại

### Kiểm tra file:
- [ ] File `index.php` đã tồn tại
- [ ] File `.htaccess` đã tồn tại
- [ ] File `README.md` đã có nội dung
- [ ] File `CHECKLIST.md` đã có nội dung

---

## 🎉 KẾT QUẢ GIAI ĐOẠN 1

**Tổng số thư mục đã tạo:** ~30+ thư mục  
**Tổng số file đã tạo:** ~60+ file  
**Trạng thái:** ✅ HOÀN THÀNH

---

## 🚀 BƯỚC TIẾP THEO

Sau khi kiểm tra checklist xong, bạn có thể:
1. ✅ Xác nhận cấu trúc đã đúng
2. 📢 Thông báo cho tôi: "Giai đoạn 1 OK, chuyển sang Giai đoạn 2"
3. 🔄 Tôi sẽ bắt đầu coding các file cơ bản (Router, Database Connection, Base Classes...)

---

**📅 Ngày hoàn thành:** 11/11/2025  
**👨‍💻 Người thực hiện:** GitHub Copilot

