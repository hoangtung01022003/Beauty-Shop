-- =====================================================
-- WEBSITE BÁN MỸ PHẨM - DATABASE SCHEMA
-- Ngày tạo: 11/11/2025
-- Phiên bản: 1.0.0
-- =====================================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS beauty_shop
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE beauty_shop;

-- =====================================================
-- 1. BẢNG USERS - Quản lý người dùng
-- =====================================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL COMMENT 'Tên đăng nhập',
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu mã hóa (bcrypt/password_hash)',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Email',
    role ENUM('admin', 'user') DEFAULT 'user' COMMENT 'Phân quyền: admin hoặc user',
    avatar VARCHAR(255) NULL COMMENT 'Đường dẫn ảnh đại diện',
    phone VARCHAR(20) NULL COMMENT 'Số điện thoại',
    address TEXT NULL COMMENT 'Địa chỉ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý người dùng';

-- =====================================================
-- 2. BẢNG CATEGORIES - Danh mục sản phẩm
-- =====================================================
DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL COMMENT 'Tên danh mục',
    description TEXT NULL COMMENT 'Mô tả danh mục',
    image VARCHAR(255) NULL COMMENT 'Hình ảnh danh mục',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',
    
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng danh mục sản phẩm';

-- =====================================================
-- 3. BẢNG PRODUCTS - Sản phẩm
-- =====================================================
DROP TABLE IF EXISTS products;
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL COMMENT 'Tên sản phẩm',
    description LONGTEXT NULL COMMENT 'Mô tả chi tiết sản phẩm',
    price DECIMAL(10,2) NOT NULL COMMENT 'Giá bán',
    cost_price DECIMAL(10,2) NULL COMMENT 'Giá vốn',
    image VARCHAR(255) NULL COMMENT 'Hình ảnh chính',
    gallery JSON NULL COMMENT 'Thư viện ảnh (JSON array)',
    category_id INT NOT NULL COMMENT 'ID danh mục',
    stock INT DEFAULT 0 COMMENT 'Số lượng tồn kho',
    sold INT DEFAULT 0 COMMENT 'Số lượng đã bán',
    rating DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Đánh giá trung bình (0-5)',
    status ENUM('active', 'inactive') DEFAULT 'active' COMMENT 'Trạng thái sản phẩm',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    INDEX idx_name (name),
    INDEX idx_category_id (category_id),
    INDEX idx_price (price),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng sản phẩm';

-- =====================================================
-- 4. BẢNG ORDERS - Đơn hàng
-- =====================================================
DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL COMMENT 'ID người dùng',
    order_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Mã đơn hàng (VD: ORD-2025-001)',
    total_price DECIMAL(12,2) NOT NULL COMMENT 'Tổng tiền',
    discount DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Giảm giá',
    final_price DECIMAL(12,2) NOT NULL COMMENT 'Tổng tiền sau giảm giá',
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending' COMMENT 'Trạng thái đơn hàng',
    payment_method VARCHAR(50) DEFAULT 'cash' COMMENT 'Phương thức thanh toán (cash, bank, momo, ...)',
    shipping_address TEXT NULL COMMENT 'Địa chỉ giao hàng',
    notes TEXT NULL COMMENT 'Ghi chú đơn hàng',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_order_code (order_code),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đơn hàng';

-- =====================================================
-- 5. BẢNG ORDER_ITEMS - Chi tiết đơn hàng
-- =====================================================
DROP TABLE IF EXISTS order_items;
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL COMMENT 'ID đơn hàng',
    product_id INT NOT NULL COMMENT 'ID sản phẩm',
    quantity INT NOT NULL COMMENT 'Số lượng',
    price DECIMAL(10,2) NOT NULL COMMENT 'Giá tại thời điểm mua',
    subtotal DECIMAL(12,2) NOT NULL COMMENT 'Thành tiền (quantity × price)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng chi tiết đơn hàng';

-- =====================================================
-- KẾT THÚC SCHEMA
-- =====================================================

