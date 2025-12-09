<?php
/**
 * =====================================================
 * TEST CATEGORY ADD - Debug script
 * =====================================================
 */

// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include các file cần thiết
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/helpers/Helper.php';

echo "=== TEST CATEGORY ADD DEBUG ===\n";

try {
    // Test 1: Kiểm tra kết nối database
    echo "1. Testing database connection...\n";
    $db = getDB();
    if ($db) {
        echo "✓ Database connection OK\n";
    } else {
        echo "✗ Database connection FAILED\n";
        exit;
    }

    // Test 2: Kiểm tra cấu trúc bảng categories
    echo "\n2. Checking categories table structure...\n";
    $stmt = $db->query("DESCRIBE categories");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']}: {$column['Type']} " . 
             ($column['Null'] === 'YES' ? '(nullable)' : '(not null)') . 
             ($column['Default'] ? " default: {$column['Default']}" : '') . "\n";
    }

    // Test 3: Kiểm tra Category model
    echo "\n3. Testing Category model...\n";
    $categoryModel = new Category();
    echo "✓ Category model instantiated\n";

    // Test 4: Test create với data đơn giản
    echo "\n4. Testing category creation...\n";
    $testData = [
        'name' => 'Test Category ' . date('Y-m-d H:i:s'),
        'description' => 'Test description',
        'status' => 'active'
    ];
    
    echo "Test data: " . json_encode($testData) . "\n";
    
    $categoryId = $categoryModel->create($testData);
    
    if ($categoryId) {
        echo "✓ Category created successfully with ID: $categoryId\n";
        
        // Verify the created category
        $createdCategory = $categoryModel->getById($categoryId);
        if ($createdCategory) {
            echo "✓ Category verified in database:\n";
            echo "  Name: {$createdCategory['name']}\n";
            echo "  Description: {$createdCategory['description']}\n";
            echo "  Status: {$createdCategory['status']}\n";
            echo "  Created: {$createdCategory['created_at']}\n";
        }
        
        // Clean up - delete test category
        echo "\n5. Cleaning up test data...\n";
        if ($categoryModel->delete($categoryId)) {
            echo "✓ Test category deleted\n";
        } else {
            echo "✗ Failed to delete test category\n";
        }
        
    } else {
        echo "✗ Category creation FAILED\n";
        
        // Check for PDO errors
        $errorInfo = $db->errorInfo();
        if ($errorInfo[0] !== '00000') {
            echo "Database error: " . $errorInfo[2] . "\n";
        }
    }

    // Test 5: Test isNameExists method
    echo "\n6. Testing isNameExists method...\n";
    $existsResult = $categoryModel->isNameExists('Nonexistent Category Name');
    echo "Name exists result for nonexistent name: " . ($existsResult ? 'true' : 'false') . "\n";

} catch (Exception $e) {
    echo "✗ Exception occurred: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>