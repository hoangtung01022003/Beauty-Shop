<?php
/**
 * =====================================================
 * TEST CART MODEL - CLI
 * =====================================================
 * File: test_cart_model_cli.php
 * Mô tả: Test Cart Model qua CLI
 * =====================================================
 */

// Khởi động session
session_start();

// Load dependencies
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Cart.php';
require_once __DIR__ . '/models/Product.php';

echo "=== TEST CART MODEL ===\n\n";

try {
    // Khởi tạo Cart
    $cart = new Cart();
    $productModel = new Product();
    
    echo "✅ Cart Model initialized successfully\n\n";
    
    // Test 1: Thêm sản phẩm vào giỏ
    echo "--- TEST 1: Add Products to Cart ---\n";
    $result1 = $cart->add(1, 2, 150000, 'Son Dưỡng Môi', 'product1.jpg');
    $result2 = $cart->add(2, 1, 250000, 'Kem Nền', 'product2.jpg');
    $result3 = $cart->add(3, 3, 180000, 'Mascara', 'product3.jpg');
    
    if ($result1 && $result2 && $result3) {
        echo "✅ Added 3 products to cart\n";
    } else {
        echo "❌ Failed to add products\n";
    }
    
    echo "Cart Count: " . $cart->getCount() . " items\n";
    echo "Total Quantity: " . $cart->getTotalQuantity() . " products\n";
    echo "Total: " . number_format($cart->getTotal(), 0, ',', '.') . "đ\n\n";
    
    // Test 2: Cập nhật số lượng
    echo "--- TEST 2: Update Quantity ---\n";
    $updateResult = $cart->update(1, 5); // Tăng product 1 lên 5 cái
    
    if ($updateResult) {
        echo "✅ Updated product ID 1 to quantity 5\n";
        $item = $cart->getItem(1);
        echo "New quantity: " . $item['quantity'] . "\n";
        echo "New subtotal: " . number_format($item['subtotal'], 0, ',', '.') . "đ\n";
    } else {
        echo "❌ Failed to update\n";
    }
    echo "\n";
    
    // Test 3: Kiểm tra has()
    echo "--- TEST 3: Check Product Exists ---\n";
    echo "Has product ID 1: " . ($cart->has(1) ? "Yes ✅" : "No ❌") . "\n";
    echo "Has product ID 999: " . ($cart->has(999) ? "Yes ✅" : "No ❌") . "\n\n";
    
    // Test 4: Get Summary
    echo "--- TEST 4: Cart Summary ---\n";
    $summary = $cart->getSummary();
    print_r($summary);
    echo "\n";
    
    // Test 5: Get All Items
    echo "--- TEST 5: Get All Items ---\n";
    $items = $cart->getItems();
    foreach ($items as $productId => $item) {
        echo "Product ID {$productId}:\n";
        echo "  - Name: {$item['name']}\n";
        echo "  - Quantity: {$item['quantity']}\n";
        echo "  - Price: " . number_format($item['price'], 0, ',', '.') . "đ\n";
        echo "  - Subtotal: " . number_format($item['subtotal'], 0, ',', '.') . "đ\n\n";
    }
    
    // Test 6: Remove Item
    echo "--- TEST 6: Remove Item ---\n";
    $removeResult = $cart->remove(2);
    if ($removeResult) {
        echo "✅ Removed product ID 2\n";
        echo "New count: " . $cart->getCount() . " items\n\n";
    } else {
        echo "❌ Failed to remove\n\n";
    }
    
    // Test 7: isEmpty()
    echo "--- TEST 7: Check Empty ---\n";
    echo "Is cart empty: " . ($cart->isEmpty() ? "Yes" : "No") . "\n\n";
    
    // Test 8: Validate Cart (với Product Model)
    echo "--- TEST 8: Validate Cart ---\n";
    // Thêm 1 sản phẩm thật từ DB (nếu có)
    $products = $productModel->getAll(1);
    if (!empty($products)) {
        $realProduct = $products[0];
        $cart->add(
            $realProduct['id'], 
            1, 
            $realProduct['price'],
            $realProduct['name'],
            $realProduct['image']
        );
        echo "✅ Added real product from DB: {$realProduct['name']}\n";
        
        // Validate
        $validation = $cart->validate($productModel);
        if ($validation['valid']) {
            echo "✅ Cart is valid!\n";
        } else {
            echo "❌ Cart has errors:\n";
            print_r($validation['errors']);
        }
    } else {
        echo "⚠️  No products in database to test validation\n";
    }
    echo "\n";
    
    // Test 9: Export Cart
    echo "--- TEST 9: Export Cart ---\n";
    $exportedCart = $cart->export();
    echo "Exported cart data:\n";
    print_r($exportedCart);
    echo "\n";
    
    // Test 10: Clear Cart
    echo "--- TEST 10: Clear Cart ---\n";
    $cart->clear();
    echo "Cart cleared\n";
    echo "Is empty: " . ($cart->isEmpty() ? "Yes ✅" : "No ❌") . "\n";
    echo "Count: " . $cart->getCount() . "\n\n";
    
    // Test 11: Sync Cart (merge)
    echo "--- TEST 11: Sync Cart ---\n";
    $cart->add(1, 2, 100000, 'Product A', 'a.jpg');
    echo "Current cart: 1 item (Product A, qty: 2)\n";
    
    $externalCart = [
        1 => ['product_id' => 1, 'quantity' => 3, 'price' => 100000, 'name' => 'Product A', 'image' => 'a.jpg', 'subtotal' => 300000],
        5 => ['product_id' => 5, 'quantity' => 1, 'price' => 200000, 'name' => 'Product B', 'image' => 'b.jpg', 'subtotal' => 200000]
    ];
    
    $cart->sync($externalCart);
    echo "After sync: " . $cart->getCount() . " items\n";
    echo "Product 1 quantity: " . $cart->getItem(1)['quantity'] . " (should be 5)\n";
    echo "Product 5 exists: " . ($cart->has(5) ? "Yes ✅" : "No ❌") . "\n\n";
    
    // Test 12: Debug Output
    echo "--- TEST 12: Debug Output ---\n";
    $cart->debug();
    
    echo "\n=== ALL TESTS COMPLETED ✅ ===\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
