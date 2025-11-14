<?php
/**
 * =====================================================
 * FIX DATABASE SCHEMA - Thêm field status vào categories
 * =====================================================
 */

// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database config
require_once __DIR__ . '/config/database.php';

echo "=== FIX DATABASE SCHEMA ===\n";

try {
    // Kết nối database
    $db = getDB();
    if (!$db) {
        echo "✗ Không thể kết nối database\n";
        exit;
    }

    echo "✓ Kết nối database thành công\n";

    // Kiểm tra xem field status đã tồn tại chưa
    echo "\n1. Kiểm tra cấu trúc bảng categories hiện tại...\n";
    $stmt = $db->query("DESCRIBE categories");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasStatusField = false;
    echo "Các field hiện tại:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']}: {$column['Type']}\n";
        if ($column['Field'] === 'status') {
            $hasStatusField = true;
        }
    }

    // Thêm field status nếu chưa có
    if (!$hasStatusField) {
        echo "\n2. Thêm field status vào bảng categories...\n";
        $sql = "ALTER TABLE categories 
                ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' 
                COMMENT 'Trạng thái danh mục' AFTER description";
        
        $result = $db->exec($sql);
        if ($result !== false) {
            echo "✓ Đã thêm field status thành công\n";
        } else {
            echo "✗ Lỗi khi thêm field status\n";
            $errorInfo = $db->errorInfo();
            echo "Chi tiết lỗi: " . $errorInfo[2] . "\n";
            exit;
        }
    } else {
        echo "\n2. Field status đã tồn tại, bỏ qua việc thêm\n";
    }

    // Hiển thị cấu trúc bảng sau khi sửa
    echo "\n3. Cấu trúc bảng categories sau khi sửa:\n";
    $stmt = $db->query("DESCRIBE categories");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "  - {$column['Field']}: {$column['Type']} " . 
             ($column['Null'] === 'YES' ? '(nullable)' : '(not null)') . 
             ($column['Default'] ? " default: {$column['Default']}" : '') . "\n";
    }

    echo "\n✓ Database schema đã được sửa thành công!\n";

} catch (Exception $e) {
    echo "✗ Lỗi: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== HOÀN THÀNH ===\n";
?>