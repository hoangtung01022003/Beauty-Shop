<?php
/**
 * =====================================================
 * TEST ORDER MODEL - CLI
 * =====================================================
 * File: test_order_model_cli.php
 * Mô tả: Test Order Model qua CLI
 * =====================================================
 */

session_start();

// Load dependencies
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/User.php';

echo "=== TEST ORDER MODEL ===\n\n";

try {
    $orderModel = new Order();
    $productModel = new Product();
    $userModel = new User();
    
    echo "✅ Order Model initialized successfully\n\n";
    
    // Lấy user để test (user_id = 1)
    $testUser = $userModel->find(1);
    if (!$testUser) {
        echo "⚠️  No user found. Creating test user...\n";
        exit;
    }
    
    echo "Using test user:\n";
    echo "  - ID: {$testUser['id']}\n";
    echo "  - Username: {$testUser['username']}\n\n";
    
    // Test 1: Generate Order Code
    echo "--- TEST 1: Generate Order Code ---\n";
    $reflection = new ReflectionClass($orderModel);
    $method = $reflection->getMethod('generateOrderCode');
    $method->setAccessible(true);
    $orderCode = $method->invoke($orderModel);
    echo "✅ Generated order code: {$orderCode}\n\n";
    
    // Test 2: Create Order
    echo "--- TEST 2: Create Order ---\n";
    $orderData = [
        'total_amount' => 500000,
        'shipping_address' => '123 Nguyễn Văn Linh, Q7, TP.HCM',
        'payment_method' => 'cod',
        'notes' => 'Giao giờ hành chính'
    ];
    
    $orderId = $orderModel->createOrder($testUser['id'], $orderData);
    
    if ($orderId) {
        echo "✅ Created order successfully\n";
        echo "  - Order ID: {$orderId}\n";
    } else {
        echo "❌ Failed to create order\n";
    }
    echo "\n";
    
    // Test 3: Add Items to Order
    echo "--- TEST 3: Add Items to Order ---\n";
    $products = $productModel->getAll(3); // Lấy 3 sản phẩm
    
    if (!empty($products)) {
        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'product_id' => $product['id'],
                'quantity' => 2,
                'price' => $product['price']
            ];
        }
        
        $itemsAdded = $orderModel->addItems($orderId, $items);
        
        if ($itemsAdded) {
            echo "✅ Added " . count($items) . " items to order\n";
        } else {
            echo "❌ Failed to add items\n";
        }
    } else {
        echo "⚠️  No products available to add\n";
    }
    echo "\n";
    
    // Test 4: Get Order By ID
    echo "--- TEST 4: Get Order By ID ---\n";
    $order = $orderModel->getById($orderId);
    
    if ($order) {
        echo "✅ Retrieved order:\n";
        echo "  - Order Code: {$order['order_code']}\n";
        echo "  - Total: " . number_format($order['total_amount'], 0, ',', '.') . "đ\n";
        echo "  - Status: {$order['status']}\n";
        echo "  - Items count: " . count($order['items']) . "\n";
    } else {
        echo "❌ Failed to retrieve order\n";
    }
    echo "\n";
    
    // Test 5: Get Orders By User
    echo "--- TEST 5: Get Orders By User ---\n";
    $userOrders = $orderModel->getByUser($testUser['id'], 5);
    echo "✅ Found " . count($userOrders) . " orders for user\n";
    
    foreach ($userOrders as $o) {
        echo "  - {$o['order_code']}: " . number_format($o['total_amount'], 0, ',', '.') . "đ ({$o['status']})\n";
    }
    echo "\n";
    
    // Test 6: Count Orders
    echo "--- TEST 6: Count Orders ---\n";
    $totalOrders = $orderModel->countAll();
    $pendingOrders = $orderModel->countByStatus('pending');
    $completedOrders = $orderModel->countByStatus('completed');
    
    echo "✅ Order statistics:\n";
    echo "  - Total orders: {$totalOrders}\n";
    echo "  - Pending: {$pendingOrders}\n";
    echo "  - Completed: {$completedOrders}\n\n";
    
    // Test 7: Update Status
    echo "--- TEST 7: Update Order Status ---\n";
    $updated = $orderModel->updateStatus($orderId, 'processing');
    
    if ($updated) {
        echo "✅ Updated status to 'processing'\n";
        $updatedOrder = $orderModel->getById($orderId);
        echo "  - Current status: {$updatedOrder['status']}\n";
    } else {
        echo "❌ Failed to update status\n";
    }
    echo "\n";
    
    // Test 8: Get Total Revenue
    echo "--- TEST 8: Get Total Revenue ---\n";
    $totalRevenue = $orderModel->getTotalRevenue('delivered');
    $allRevenue = $orderModel->getTotalRevenue(null);
    $monthlyRevenue = $orderModel->getMonthlyRevenue();
    
    echo "✅ Revenue statistics:\n";
    echo "  - Delivered orders: " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
    echo "  - All orders: " . number_format($allRevenue, 0, ',', '.') . "đ\n";
    echo "  - This month: " . number_format($monthlyRevenue, 0, ',', '.') . "đ\n\n";
    
    // Test 9: Get All Orders (Admin)
    echo "--- TEST 9: Get All Orders ---\n";
    $allOrders = $orderModel->getAll(null, 5);
    echo "✅ Retrieved " . count($allOrders) . " orders (admin view)\n";
    
    foreach ($allOrders as $o) {
        echo "  - {$o['order_code']} by {$o['username']}: " . number_format($o['total_amount'], 0, ',', '.') . "đ\n";
    }
    echo "\n";
    
    // Test 10: Search Orders
    echo "--- TEST 10: Search Orders ---\n";
    $searchResults = $orderModel->search('ORD', 5);
    echo "✅ Search results for 'ORD': " . count($searchResults) . " orders\n";
    
    foreach ($searchResults as $o) {
        echo "  - {$o['order_code']}\n";
    }
    echo "\n";
    
    // Test 11: Get Recent Orders
    echo "--- TEST 11: Get Recent Orders ---\n";
    $recentOrders = $orderModel->getRecent(3);
    echo "✅ Recent orders:\n";
    
    foreach ($recentOrders as $o) {
        echo "  - {$o['order_code']}: " . date('d/m/Y H:i', strtotime($o['created_at'])) . "\n";
    }
    echo "\n";
    
    echo "\n=== ALL TESTS COMPLETED ✅ ===\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
