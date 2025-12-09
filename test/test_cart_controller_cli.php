<?php
/**
 * =====================================================
 * TEST CART CONTROLLER - CLI
 * =====================================================
 * File: test_cart_controller_cli.php
 * Mô tả: Test CartController qua CLI
 * =====================================================
 */

// Khởi động session
session_start();

// Load dependencies
require_once __DIR__ . '/config/constants.php';  // Load constants trước
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Cart.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/controllers/BaseController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/helpers/Helper.php';

echo "=== TEST CART CONTROLLER ===\n\n";

try {
    // Mock $_POST và $_SERVER
    $originalPost = $_POST;
    $originalServer = $_SERVER;
    
    // Khởi tạo
    $controller = new CartController();
    $productModel = new Product();
    
    echo "✅ CartController initialized successfully\n\n";
    
    // Lấy 1 sản phẩm thật từ DB để test
    $products = $productModel->getAll(1);
    
    if (empty($products)) {
        echo "⚠️  No products in database. Please add products first.\n";
        exit;
    }
    
    $testProduct = $products[0];
    echo "Using test product:\n";
    echo "  - ID: {$testProduct['id']}\n";
    echo "  - Name: {$testProduct['name']}\n";
    echo "  - Price: " . number_format($testProduct['price'], 0, ',', '.') . "đ\n";
    echo "  - Stock: {$testProduct['stock']}\n\n";
    
    // Test 1: Add Product (Simulate POST)
    echo "--- TEST 1: Add Product to Cart ---\n";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = [
        'product_id' => $testProduct['id'],
        'quantity' => 2
    ];
    
    // Capture output
    ob_start();
    
    // Simulate add (will redirect, but we catch it)
    try {
        $controller->add();
    } catch (Exception $e) {
        // Expected - redirect will throw header error in CLI
    }
    
    $output = ob_get_clean();
    
    // Check session flash
    if (isset($_SESSION['flash']['success'])) {
        echo "✅ Add success: {$_SESSION['flash']['success']}\n";
        unset($_SESSION['flash']['success']);
    } elseif (isset($_SESSION['flash']['error'])) {
        echo "❌ Add failed: {$_SESSION['flash']['error']}\n";
        unset($_SESSION['flash']['error']);
    }
    
    // Verify cart
    $cart = new Cart();
    echo "Cart count: " . $cart->getCount() . "\n";
    echo "Cart total: " . number_format($cart->getTotal(), 0, ',', '.') . "đ\n\n";
    
    // Test 2: Update Quantity (Simulate AJAX)
    echo "--- TEST 2: Update Quantity (AJAX) ---\n";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    $_POST = [
        'product_id' => $testProduct['id'],
        'quantity' => 5
    ];
    
    ob_start();
    $controller->update();
    $jsonOutput = ob_get_clean();
    
    $response = json_decode($jsonOutput, true);
    if ($response && $response['success']) {
        echo "✅ Update success: {$response['message']}\n";
        echo "  - New quantity: {$response['quantity']}\n";
        echo "  - Item subtotal: " . number_format($response['item_subtotal'], 0, ',', '.') . "đ\n";
        echo "  - Cart total: {$response['cart_total_formatted']}\n";
    } else {
        echo "❌ Update failed: " . ($response['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
    
    // Test 3: Get Cart Count (AJAX)
    echo "--- TEST 3: Get Cart Count ---\n";
    unset($_POST);
    
    ob_start();
    $controller->count();
    $jsonOutput = ob_get_clean();
    
    $response = json_decode($jsonOutput, true);
    if ($response && $response['success']) {
        echo "✅ Count retrieved:\n";
        echo "  - Items count: {$response['count']}\n";
        echo "  - Total quantity: {$response['total_quantity']}\n";
        echo "  - Total: {$response['total_formatted']}\n";
    } else {
        echo "❌ Failed to get count\n";
    }
    echo "\n";
    
    // Test 4: Add Invalid Product
    echo "--- TEST 4: Add Invalid Product ---\n";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = [
        'product_id' => 99999, // Not exists
        'quantity' => 1
    ];
    
    ob_start();
    $controller->add();
    $jsonOutput = ob_get_clean();
    
    $response = json_decode($jsonOutput, true);
    if ($response && !$response['success']) {
        echo "✅ Validation works: {$response['message']}\n";
    } else {
        echo "❌ Should have failed\n";
    }
    echo "\n";
    
    // Test 5: Add Out of Stock
    echo "--- TEST 5: Add Exceeding Stock ---\n";
    $_POST = [
        'product_id' => $testProduct['id'],
        'quantity' => $testProduct['stock'] + 100 // Exceed stock
    ];
    
    ob_start();
    $controller->add();
    $jsonOutput = ob_get_clean();
    
    $response = json_decode($jsonOutput, true);
    if ($response && !$response['success']) {
        echo "✅ Stock validation works: {$response['message']}\n";
    } else {
        echo "❌ Should have detected stock issue\n";
    }
    echo "\n";
    
    // Test 6: Remove Product (AJAX)
    echo "--- TEST 6: Remove Product ---\n";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    $_POST = [
        'product_id' => $testProduct['id']
    ];
    
    ob_start();
    $controller->remove();
    $jsonOutput = ob_get_clean();
    
    $response = json_decode($jsonOutput, true);
    if ($response && $response['success']) {
        echo "✅ Remove success: {$response['message']}\n";
        echo "  - Cart count: {$response['cart_count']}\n";
        echo "  - Is empty: " . ($response['is_empty'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Remove failed\n";
    }
    echo "\n";
    
    // Test 7: Clear Cart
    echo "--- TEST 7: Clear Cart ---\n";
    
    // Add some items first
    $cart->add($testProduct['id'], 1, $testProduct['price'], $testProduct['name'], $testProduct['image']);
    $cart->add(2, 2, 100000, 'Test Product 2', 'test.jpg');
    echo "Added 2 items to cart\n";
    echo "Cart count before clear: " . $cart->getCount() . "\n";
    
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    unset($_POST);
    
    ob_start();
    $controller->clear();
    $jsonOutput = ob_get_clean();
    
    $response = json_decode($jsonOutput, true);
    if ($response && $response['success']) {
        echo "✅ Clear success: {$response['message']}\n";
        echo "  - Cart count: {$response['cart_count']}\n";
        echo "  - Is empty: " . ($response['is_empty'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Clear failed\n";
    }
    echo "\n";
    
    // Restore original values
    $_POST = $originalPost;
    $_SERVER = $originalServer;
    
    echo "\n=== ALL TESTS COMPLETED ✅ ===\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
