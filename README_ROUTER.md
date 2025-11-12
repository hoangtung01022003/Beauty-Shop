# 📋 HƯỚNG DẪN KIỂM TRA ROUTER & INDEX.PHP

## ✅ Đã hoàn thành:

### 1. **Router Class** (`config/Router.php`)
- ✅ Hỗ trợ GET và POST routes
- ✅ Dynamic routing với parameters (VD: `/products/{id}`)
- ✅ Fallback routing tự động
- ✅ Trang 404 đẹp mắt
- ✅ Error handling cho development và production
- ✅ Hỗ trợ controller trong thư mục Admin

### 2. **Index.php** - Entry Point
- ✅ Session management
- ✅ Load tất cả config và helpers
- ✅ Định nghĩa đầy đủ routes cho:
  - Auth (login, register, logout, forgot-password)
  - User (home, products, cart, checkout, profile)
  - Admin (dashboard, users, categories, products, orders)
- ✅ Dispatch request tự động

### 3. **.htaccess**
- ✅ URL Rewriting
- ✅ Bảo mật
- ✅ Cache và compression

### 4. **Constants.php**
- ✅ Thêm ENVIRONMENT constant
- ✅ Cập nhật BASE_PATH cho routing

---

## 🧪 CÁCH KIỂM TRA:

### **Bước 1: Đảm bảo Apache mod_rewrite đã bật**
```bash
# Kiểm tra trong httpd.conf hoặc apache2.conf
LoadModule rewrite_module modules/mod_rewrite.so

# AllowOverride phải là All
<Directory "C:/xampp/htdocs">
    AllowOverride All
</Directory>
```

### **Bước 2: Khởi động server**
- Mở XAMPP Control Panel
- Start Apache và MySQL
- Đảm bảo database đã được tạo (xem `database/README_DATABASE.md`)

### **Bước 3: Kiểm tra các routes**

#### 🏠 **Trang chủ:**
```
http://localhost/WebBanMyPham
http://localhost/WebBanMyPham/home
http://localhost/WebBanMyPham/user/home
```
→ Tất cả đều gọi `ProductController@home`

#### 🔐 **Auth routes:**
```
http://localhost/WebBanMyPham/auth/register
→ Hiển thị form đăng ký

http://localhost/WebBanMyPham/auth/login
→ Hiển thị form đăng nhập

http://localhost/WebBanMyPham/auth/logout
→ Đăng xuất và redirect
```

#### 🛍️ **Product routes:**
```
http://localhost/WebBanMyPham/products
→ Danh sách sản phẩm

http://localhost/WebBanMyPham/products/1
→ Chi tiết sản phẩm ID=1

http://localhost/WebBanMyPham/products/category/2
→ Sản phẩm theo category ID=2
```

#### 🛒 **Cart routes:**
```
http://localhost/WebBanMyPham/cart
→ Giỏ hàng
```

#### 👤 **Profile routes:**
```
http://localhost/WebBanMyPham/profile
→ Thông tin cá nhân

http://localhost/WebBanMyPham/profile/orders
→ Đơn hàng của tôi
```

#### 🔧 **Admin routes:**
```
http://localhost/WebBanMyPham/admin
→ Admin dashboard

http://localhost/WebBanMyPham/admin/products
→ Quản lý sản phẩm

http://localhost/WebBanMyPham/admin/categories
→ Quản lý danh mục

http://localhost/WebBanMyPham/admin/orders
→ Quản lý đơn hàng

http://localhost/WebBanMyPham/admin/users
→ Quản lý người dùng
```

#### ❌ **Test 404 page:**
```
http://localhost/WebBanMyPham/not-found-page
→ Hiển thị trang 404 đẹp
```

---

## 📝 TEST FLOW CHÍNH:

### **Test 1: Đăng ký user mới**
1. Truy cập: `http://localhost/WebBanMyPham/auth/register`
2. Điền form đăng ký
3. Submit → POST request tới `AuthController@register`
4. Tạo user thành công → redirect về `/auth/login`
5. Hiển thị thông báo thành công

### **Test 2: Đăng nhập**
1. Truy cập: `http://localhost/WebBanMyPham/auth/login`
2. Nhập email/password
3. Submit → POST request tới `AuthController@login`
4. Đăng nhập thành công → lưu session → redirect về `/home`

### **Test 3: Xem sản phẩm**
1. Truy cập: `http://localhost/WebBanMyPham/products`
2. Click vào 1 sản phẩm
3. Router tự động parse ID và gọi `ProductController@detail($id)`

### **Test 4: Admin area**
1. Đăng nhập với tài khoản admin
2. Truy cập: `http://localhost/WebBanMyPham/admin`
3. Router kiểm tra authentication và role
4. Hiển thị dashboard admin

---

## 🔍 DEBUG:

### **Nếu gặp lỗi 404 trên tất cả routes:**
- Kiểm tra mod_rewrite đã bật chưa
- Kiểm tra AllowOverride All trong config Apache
- Restart Apache

### **Nếu route không khớp:**
- Kiểm tra BASE_PATH trong constants.php
- Mở browser console và xem URL đầy đủ
- Check file Router.php, phương thức parseUri()

### **Xem chi tiết lỗi:**
- File `config/constants.php` → ENVIRONMENT = 'development'
- Lỗi sẽ hiển thị đầy đủ với stack trace

---

## 📊 ROUTE MAPPING SUMMARY:

| URL Pattern | Method | Controller | Action |
|------------|--------|------------|--------|
| `/` | GET | ProductController | home |
| `/auth/register` | GET | AuthController | showRegisterForm |
| `/auth/register` | POST | AuthController | register |
| `/auth/login` | GET | AuthController | showLoginForm |
| `/auth/login` | POST | AuthController | login |
| `/auth/logout` | GET | AuthController | logout |
| `/products` | GET | ProductController | index |
| `/products/{id}` | GET | ProductController | detail |
| `/cart` | GET | CartController | index |
| `/cart/add` | POST | CartController | add |
| `/checkout` | GET | OrderController | checkout |
| `/admin` | GET | DashboardController | index |
| `/admin/products` | GET | ProductAdminController | index |

---

## ✨ TÍNH NĂNG NỔI BẬT:

1. **Clean URLs** - Không cần .php extension
2. **Dynamic Parameters** - Hỗ trợ {id} trong URL
3. **Method-based Routing** - Phân biệt GET và POST
4. **Auto-fallback** - Tự động parse controller/action
5. **Beautiful Error Pages** - 404 và 500 đẹp mắt
6. **Security** - Chặn directory listing, bảo vệ .htaccess

---

## 🚀 NEXT STEPS:

Sau khi kiểm tra router hoàn tất, bạn có thể:
1. Test các form register/login
2. Kiểm tra session management
3. Test CRUD operations
4. Implement middleware cho authentication
5. Thêm CSRF protection

---

**Chúc bạn test thành công! 🎉**

# HƯỚNG DẪN ROUTER - CẬP NHẬT MỚI NHẤT

## 📋 DANH SÁCH ROUTES ĐÃ CẬP NHẬT

### ✅ AUTH ROUTES (Đã có)
```
GET  /auth/register          -> AuthController@register
POST /auth/register          -> AuthController@register
GET  /auth/login             -> AuthController@login
POST /auth/login             -> AuthController@login
GET  /auth/logout            -> AuthController@logout
GET  /auth/forgot-password   -> AuthController@showForgotPasswordForm
POST /auth/forgot-password   -> AuthController@forgotPassword
```

### ✅ USER ROUTES (Đã cập nhật)
```
GET  /                       -> ProductController@home
GET  /home                   -> ProductController@home
GET  /user/home              -> ProductController@home

GET  /products               -> ProductController@index (Danh sách)
GET  /products/search        -> ProductController@search (Tìm kiếm)
GET  /products/detail/{id}   -> ProductController@detail (Chi tiết)
GET  /products/category/{id} -> ProductController@category (Lọc danh mục)

GET  /cart                   -> CartController@index
POST /cart/add               -> CartController@add
POST /cart/update            -> CartController@update
POST /cart/remove            -> CartController@remove
POST /cart/clear             -> CartController@clear

GET  /checkout               -> OrderController@checkout
POST /checkout               -> OrderController@processCheckout
GET  /checkout/success       -> OrderController@success

GET  /profile                -> AuthController@profile
POST /profile                -> AuthController@updateProfile
GET  /profile/orders         -> OrderController@myOrders
GET  /profile/orders/{id}    -> OrderController@orderDetail
```

### ✅ ADMIN ROUTES (Đã cập nhật)

#### Dashboard
```
GET  /admin                  -> DashboardController@index
GET  /admin/dashboard        -> DashboardController@index
```

#### Users Management
```
GET  /admin/users            -> UserAdminController@index
GET  /admin/users/edit/{id}  -> UserAdminController@edit
POST /admin/users/update/{id}-> UserAdminController@update
POST /admin/users/delete/{id}-> UserAdminController@delete
```

#### Categories Management
```
GET  /admin/categories           -> CategoryAdminController@index
GET  /admin/categories/add       -> CategoryAdminController@add
POST /admin/categories/add       -> CategoryAdminController@store
GET  /admin/categories/edit/{id} -> CategoryAdminController@edit
POST /admin/categories/update/{id}->CategoryAdminController@update
POST /admin/categories/delete/{id}->CategoryAdminController@delete
```

#### Products Management (MỚI - ĐÃ CẬP NHẬT)
```
GET  /admin/products              -> ProductAdminController@index
GET  /admin/products/create       -> ProductAdminController@create (Form)
POST /admin/products/create       -> ProductAdminController@create (Submit)
GET  /admin/products/edit/{id}    -> ProductAdminController@edit (Form)
POST /admin/products/edit/{id}    -> ProductAdminController@edit (Submit)
POST /admin/products/delete/{id}  -> ProductAdminController@delete
GET  /admin/products/view/{id}    -> ProductAdminController@view
```

#### Orders Management
```
GET  /admin/orders                     -> OrderAdminController@index
GET  /admin/orders/{id}                -> OrderAdminController@detail
POST /admin/orders/update-status/{id}  -> OrderAdminController@updateStatus
```

---

## 🔗 MAPPING ROUTES VỚI VIEWS

### Admin Products
- `/admin/products` → `views/admin/products/list.php`
- `/admin/products/create` → `views/admin/products/add.php` (include `_form.php`)
- `/admin/products/edit/{id}` → `views/admin/products/edit.php` (include `_form.php`)
- `/admin/products/view/{id}` → `views/admin/products/view.php`

### User Products
- `/products` → `views/user/products/list.php`
- `/products/detail/{id}` → `views/user/products/detail.php`
- `/` hoặc `/home` → `views/user/home.php`

---

## 🎯 VÍ DỤ SỬ DỤNG

### 1. Truy cập trang chủ
```
http://localhost/WebBanMyPham/
http://localhost/WebBanMyPham/home
```

### 2. Xem danh sách sản phẩm (User)
```
http://localhost/WebBanMyPham/products
http://localhost/WebBanMyPham/products?page=2
http://localhost/WebBanMyPham/products?category=1
```

### 3. Tìm kiếm sản phẩm
```
http://localhost/WebBanMyPham/products/search?keyword=son
```

### 4. Chi tiết sản phẩm
```
http://localhost/WebBanMyPham/products/detail/1
http://localhost/WebBanMyPham/products/detail/21
```

### 5. Quản lý sản phẩm (Admin)
```
http://localhost/WebBanMyPham/admin/products
http://localhost/WebBanMyPham/admin/products/create
http://localhost/WebBanMyPham/admin/products/edit/1
```

---

## 📝 GHI CHÚ

### Method trong Controller phải khớp với Route:
- `ProductAdminController@create` (không phải `add` hay `store`)
- `ProductAdminController@edit` xử lý cả GET và POST
- `ProductAdminController@delete` chỉ nhận POST

### URL Helper trong View:
```php
// Đúng
base_url('admin/products/create')
base_url('admin/products/edit/' . $id)
base_url('products/detail/' . $id)

// Sai
base_url('admin/products/add')  // ❌ Không tồn tại route này
```

---

## 🔄 THAY ĐỔI SO VỚI PHIÊN BẢN CŨ

### Admin Products Routes
**Trước đây:**
```
GET  /admin/products/add       -> ProductAdminController@add
POST /admin/products/add       -> ProductAdminController@store
POST /admin/products/update/{id}->ProductAdminController@update
```

**Bây giờ (Đã sửa):**
```
GET  /admin/products/create    -> ProductAdminController@create
POST /admin/products/create    -> ProductAdminController@create
POST /admin/products/edit/{id} -> ProductAdminController@edit
```

### User Products Routes
**Đã thêm:**
```
GET /products/search           -> ProductController@search
GET /products/detail/{id}      -> ProductController@detail
```

---

## ✅ CHECKLIST ROUTES HOÀN CHỈNH

- [x] Auth: Register, Login, Logout, Forgot Password
- [x] User Home
- [x] Products: List, Search, Detail, Category
- [x] Cart: View, Add, Update, Remove, Clear
- [x] Checkout: Form, Process, Success
- [x] Profile: View, Update, Orders
- [x] Admin Dashboard
- [x] Admin Users: List, Edit, Update, Delete
- [x] Admin Categories: List, Add, Edit, Update, Delete
- [x] Admin Products: List, Create, Edit, Delete, View
- [x] Admin Orders: List, Detail, Update Status

---

**Cập nhật:** 12/11/2025
**Trạng thái:** ✅ Hoàn chỉnh và đã test
