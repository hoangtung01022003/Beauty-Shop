<?php
/**
 * =====================================================
 * TEST PRODUCT DELETE - Debug script
 * =====================================================
 */

// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include các file cần thiết
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/helpers/Helper.php';

echo "=== TEST PRODUCT DELETE DEBUG ===\n";

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

    // Test 2: Kiểm tra cấu trúc bảng products
    echo "\n2. Checking products table structure...\n";
    $stmt = $db->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']}: {$column['Type']} " . 
             ($column['Null'] === 'YES' ? '(nullable)' : '(not null)') . 
             ($column['Default'] ? " default: {$column['Default']}" : '') . "\n";
    }

    // Test 3: Kiểm tra foreign key constraints
    echo "\n3. Checking foreign key constraints...\n";
    $stmt = $db->query("SELECT 
                            CONSTRAINT_NAME,
                            TABLE_NAME,
                            COLUMN_NAME,
                            REFERENCED_TABLE_NAME,
                            REFERENCED_COLUMN_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                        AND (TABLE_NAME = 'products' OR REFERENCED_TABLE_NAME = 'products')");
    
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($constraints)) {
        echo "Foreign key constraints:\n";
        foreach ($constraints as $constraint) {
            echo "  - {$constraint['TABLE_NAME']}.{$constraint['COLUMN_NAME']} -> {$constraint['REFERENCED_TABLE_NAME']}.{$constraint['REFERENCED_COLUMN_NAME']}\n";
        }
    } else {
        echo "No foreign key constraints found\n";
    }

    // Test 4: Tạo product test
    echo "\n4. Creating test product...\n";
    $productModel = new Product();
    $categoryModel = new Category();
    
    // Lấy category đầu tiên
    $categories = $categoryModel->getAll(1);
    if (empty($categories)) {
        echo "✗ No categories found. Creating test category...\n";
        $categoryId = $categoryModel->create([
            'name' => 'Test Category for Delete',
            'description' => 'Test category',
            'status' => 'active'
        ]);
        if (!$categoryId) {
            echo "✗ Failed to create test category\n";
            exit;
        }
        echo "✓ Created test category with ID: $categoryId\n";
    } else {
        $categoryId = $categories[0]['id'];
        echo "✓ Using existing category ID: $categoryId\n";
    }
    
    // Tạo sản phẩm test
    $testData = [
        'name' => 'Test Product for Delete ' . date('Y-m-d H:i:s'),
        'description' => 'Test product description',
        'price' => 100000,
        'category_id' => $categoryId,
        'stock' => 10,
        'status' => 'active'
    ];
    
    echo "Creating product with data: " . json_encode($testData) . "\n";
    
    $productId = $productModel->create($testData);
    
    if ($productId) {
        echo "✓ Test product created with ID: $productId\n";
        
        // Verify product exists
        $createdProduct = $productModel->getById($productId);
        if ($createdProduct) {
            echo "✓ Product verified in database:\n";
            echo "  Name: {$createdProduct['name']}\n";
            echo "  Price: {$createdProduct['price']}\n";
            echo "  Category: {$createdProduct['category_name']}\n";
        }
        
        // Test 5: Kiểm tra product có trong order items không
        echo "\n5. Checking if product is referenced in order_items...\n";
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM order_items WHERE product_id = ?");
        $stmt->execute([$productId]);
        $orderItemsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo "Product references in order_items: $orderItemsCount\n";
        
        // Test 6: Test delete function
        echo "\n6. Testing delete function...\n";
        
        // Test exists method first
        $exists = $productModel->exists($productId);
        echo "Product exists check: " . ($exists ? 'true' : 'false') . "\n";
        
        if ($exists) {
            echo "Attempting to delete product ID: $productId\n";
            $deleteResult = $productModel->delete($productId);
            
            echo "Delete result: " . ($deleteResult ? 'SUCCESS' : 'FAILED') . "\n";
            
            if ($deleteResult) {
                // Verify deletion
                $deletedProduct = $productModel->getById($productId);
                if (!$deletedProduct) {
                    echo "✓ Product successfully deleted from database\n";
                } else {
                    echo "✗ Product still exists in database after delete\n";
                }
            } else {
                // Check for specific database errors
                $errorInfo = $db->errorInfo();
                if ($errorInfo[0] !== '00000') {
                    echo "Database error: " . $errorInfo[2] . "\n";
                }
                
                // Check if it's a foreign key constraint error
                if (strpos($errorInfo[2], 'foreign key constraint') !== false ||
                    strpos($errorInfo[2], 'FOREIGN KEY') !== false) {
                    echo "✗ Foreign key constraint prevents deletion\n";
                    
                    // Find which tables reference this product
                    echo "Checking references...\n";
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM order_items WHERE product_id = ?");
                    $stmt->execute([$productId]);
                    $orderReferences = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    if ($orderReferences > 0) {
                        echo "  - Found $orderReferences references in order_items\n";
                    }
                }
            }
        }
        
    } else {
        echo "✗ Failed to create test product\n";
        
        // Check for database errors
        $errorInfo = $db->errorInfo();
        if ($errorInfo[0] !== '00000') {
            echo "Database error: " . $errorInfo[2] . "\n";
        }
    }

} catch (Exception $e) {
    echo "✗ Exception occurred: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>