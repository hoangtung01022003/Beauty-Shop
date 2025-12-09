<?php
/**
 * =====================================================
 * TEST PRODUCT DELETE WITH ORDERS - Debug script
 * =====================================================
 */

// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include các file cần thiết
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/OrderItem.php';
require_once __DIR__ . '/helpers/Helper.php';

echo "=== TEST PRODUCT DELETE WITH ORDERS ===\n";

try {
    // Khởi tạo models
    $productModel = new Product();
    $categoryModel = new Category();
    $orderModel = new Order();
    $orderItemModel = new OrderItem();
    $db = getDB();

    // Test 1: Tạo sản phẩm test
    echo "1. Creating test product...\n";
    
    $categories = $categoryModel->getAll(1);
    $categoryId = $categories[0]['id'] ?? 1;
    
    $testData = [
        'name' => 'Test Product with Orders ' . date('Y-m-d H:i:s'),
        'description' => 'Test product that will have orders',
        'price' => 200000,
        'category_id' => $categoryId,
        'stock' => 100,
        'status' => 'active'
    ];
    
    $productId = $productModel->create($testData);
    
    if ($productId) {
        echo "✓ Test product created with ID: $productId\n";
    } else {
        echo "✗ Failed to create test product\n";
        exit;
    }

    // Test 2: Tạo order với product này
    echo "\n2. Creating test order with this product...\n";
    
    // Kiểm tra có user nào không
    $stmt = $db->query("SELECT id FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userId = $user['id'] ?? 1;
    
    // Tạo order
    $orderData = [
        'total_amount' => 200000,
        'shipping_address' => 'Test Address',
        'payment_method' => 'cash',
        'notes' => 'Test order for delete testing'
    ];
    
    $orderId = $orderModel->createOrder($userId, $orderData);
    
    if ($orderId) {
        echo "✓ Test order created with ID: $orderId\n";
        
        // Thêm product vào order_items
        $orderItemData = [
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => 2,
            'price' => 200000
        ];
        
        $itemAdded = $orderItemModel->add($orderId, $productId, 2, 200000);
        
        if ($itemAdded) {
            echo "✓ Product added to order_items\n";
        } else {
            echo "✗ Failed to add product to order_items\n";
        }
    } else {
        echo "✗ Failed to create test order\n";
    }

    // Test 3: Kiểm tra foreign key constraint
    echo "\n3. Checking product references...\n";
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM order_items WHERE product_id = ?");
    $stmt->execute([$productId]);
    $references = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "Product references in order_items: $references\n";

    // Test 4: Test delete function (should fail)
    echo "\n4. Testing delete function (should fail)...\n";
    
    $deleteResult = $productModel->delete($productId);
    echo "Direct model delete result: " . ($deleteResult ? 'SUCCESS' : 'FAILED') . "\n";
    
    if (!$deleteResult) {
        // Check database error
        $errorInfo = $db->errorInfo();
        if ($errorInfo[0] !== '00000') {
            echo "Database error: " . $errorInfo[2] . "\n";
            
            if (strpos($errorInfo[2], 'foreign key constraint') !== false ||
                strpos($errorInfo[2], 'FOREIGN KEY') !== false) {
                echo "✓ Foreign key constraint working correctly - prevents deletion\n";
            }
        }
    }

    // Test 5: Cleanup - xóa order_items trước, sau đó xóa order và product
    echo "\n5. Cleaning up test data...\n";
    
    if ($orderId) {
        // Xóa order_items
        $orderItemModel->deleteByOrderId($orderId);
        echo "✓ Order items deleted\n";
        
        // Xóa order
        $orderModel->delete($orderId);
        echo "✓ Order deleted\n";
    }
    
    // Bây giờ xóa product
    $deleteResult = $productModel->delete($productId);
    if ($deleteResult) {
        echo "✓ Product deleted successfully after removing references\n";
    } else {
        echo "✗ Product deletion still failed\n";
    }

} catch (Exception $e) {
    echo "✗ Exception occurred: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>