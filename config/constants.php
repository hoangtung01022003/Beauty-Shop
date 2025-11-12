<?php
/**
 * =====================================================
 * CONSTANTS - Hằng số dự án
 * =====================================================
 * File: config/constants.php
 * Mô tả: Định nghĩa các hằng số chung cho dự án
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

// Môi trường (development hoặc production)
define('ENVIRONMENT', 'development'); // Đổi thành 'production' khi deploy

// Đường dẫn gốc của dự án
define('BASE_PATH', '/');

// URL gốc của website
define('BASE_URL', 'http://localhost:8000');

// Đường dẫn các thư mục
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('PRODUCT_IMAGE_PATH', UPLOAD_PATH . '/products');

// URL các thư mục public
define('CSS_URL', BASE_URL . '/public/css');
define('JS_URL', BASE_URL . '/public/js');
define('IMAGE_URL', BASE_URL . '/public/images');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Cấu hình upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);

// Cấu hình phân trang
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Vai trò người dùng
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

// Trạng thái đơn hàng
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_CONFIRMED', 'confirmed');
define('ORDER_STATUS_SHIPPED', 'shipped');
define('ORDER_STATUS_DELIVERED', 'delivered');
define('ORDER_STATUS_CANCELLED', 'cancelled');

// Trạng thái sản phẩm
define('PRODUCT_STATUS_ACTIVE', 'active');
define('PRODUCT_STATUS_INACTIVE', 'inactive');

// Múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Bật hiển thị lỗi (tắt khi production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

