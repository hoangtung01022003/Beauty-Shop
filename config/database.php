<?php
/**
 * =====================================================
 * DATABASE CONNECTION - Kết nối cơ sở dữ liệu
 * =====================================================
 * File: config/database.php
 * Mô tả: Kết nối MySQL sử dụng PDO
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

// Cấu hình kết nối Database
define('DB_HOST', 'localhost');      // Máy chủ database
define('DB_USER', 'root');           // Tên người dùng
define('DB_PASS', '');               // Mật khẩu (mặc định XAMPP là rỗng)
define('DB_NAME', 'beauty_shop');    // Tên database
define('DB_CHARSET', 'utf8mb4');     // Bộ mã ký tự

/**
 * Hàm kết nối database sử dụng PDO
 * @return PDO|null
 */
function getDB()
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,    // Bật chế độ exception
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Fetch dạng array
                PDO::ATTR_EMULATE_PREPARES => false,                     // Tắt emulate prepared statements
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET  // Set charset
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            // Xử lý lỗi kết nối
            die("❌ LỖI KẾT NỐI DATABASE: " . $e->getMessage());
        }
    }

    return $pdo;
}

/**
 * Kiểm tra kết nối database
 * @return bool
 */
function testConnection()
{
    try {
        $pdo = getDB();
        if ($pdo) {
            echo "✅ Kết nối database thành công!\n";
            echo "📊 Database: " . DB_NAME . "\n";
            echo "🌐 Host: " . DB_HOST . "\n";
            echo "👤 User: " . DB_USER . "\n";
            return true;
        }
    } catch (Exception $e) {
        echo "❌ Kết nối thất bại: " . $e->getMessage() . "\n";
        return false;
    }
    return false;
}

// Nếu chạy file trực tiếp, test kết nối
if (php_sapi_name() === 'cli' && basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    echo "=== KIỂM TRA KẾT NỐI DATABASE ===\n\n";
    testConnection();
}

