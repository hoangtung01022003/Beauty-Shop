# 🛍️ WEBSITE BÁN MỸ PHẨM - PHP MVC

## 📋 THÔNG TIN DỰ ÁN
- **Tên dự án**: Beauty Shop - Website bán mỹ phẩm
- **Công nghệ**: PHP MVC + MySQL
- **Phiên bản**: 1.0.0
- **Ngày tạo**: 11/11/2025

---

## 🏗️ KIẾN TRÚC MVC

### Flow xử lý request:
```
[User Request] 
    ↓
index.php (Router) - Nhận request, phân tích URL
    ↓
[Controller] - Xử lý logic nghiệp vụ
    ↓
[Model] - Truy vấn database ← → [MySQL Database]
    ↓
[View] - Render HTML
    ↓
[HTTP Response]
```

---

## 📁 CẤU TRÚC THƯ MỤC

```
beauty-shop/
├── config/              # Cấu hình hệ thống
├── controllers/         # Controllers xử lý logic
│   └── Admin/          # Controllers admin
├── models/             # Models tương tác database
├── views/              # Views hiển thị giao diện
│   ├── layouts/        # Layout chung (header, footer, nav, sidebar)
│   ├── auth/           # Giao diện đăng nhập/đăng ký
│   ├── user/           # Giao diện người dùng
│   ├── admin/          # Giao diện quản trị
│   └── components/     # Component tái sử dụng
├── helpers/            # Hàm hỗ trợ tiện ích
├── public/             # Tài nguyên public (CSS, JS, Images)
│   ├── css/
│   ├── js/
│   └── images/
├── uploads/            # Thư mục lưu file upload
│   └── products/
├── index.php           # Entry point - Router chính
├── .htaccess           # URL Rewrite
└── README.md           # Tài liệu dự án
```

---

## ✅ TRẠNG THÁI: GIAI ĐOẠN 1 - HOÀN THÀNH

### ✓ Đã tạo cấu trúc thư mục hoàn chỉnh
### ✓ Đã tạo tất cả file cơ bản theo kiến trúc MVC
### ✓ Sẵn sàng cho giai đoạn tiếp theo

---

## 📝 GHI CHÚ
- Dự án tuân theo mô hình MVC chuẩn
- Tách biệt rõ ràng giữa User và Admin
- Component hóa để tái sử dụng code

