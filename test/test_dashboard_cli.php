<?php
/**
 * =====================================================
 * TEST DASHBOARD CONTROLLER CLI
 * =====================================================
 * File: test_dashboard_cli.php
 * Mô tả: Test các phương thức thống kế của Dashboard Admin
 * Cách chạy: php test_dashboard_cli.php
 * =====================================================
 */

// Chỉ chạy trong CLI
if (php_sapi_name() !== 'cli') {
    die('File này chỉ chạy được trong CLI mode.');
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Order.php';

echo "\n";
echo "========================================\n";
echo "   TEST DASHBOARD STATISTICS CLI\n";
echo "========================================\n";
echo "\n";

// Khởi tạo models
$productModel = new Product();
$categoryModel = new Category();
$userModel = new User();
$orderModel = new Order();

try {
    // ==========================================
    // 1. THỐNG KÊ TỔNG QUAN
    // ==========================================
    echo "1. THỐNG KÊ TỔNG QUAN\n";
    echo str_repeat("-", 40) . "\n";
    
    $totalProducts = $productModel->countAll();
    $totalCategories = $categoryModel->countAll();
    $totalUsers = $userModel->countAll();
    $totalOrders = $orderModel->countAll();
    
    echo "   ✓ Tổng sản phẩm: " . $totalProducts . "\n";
    echo "   ✓ Tổng danh mục: " . $totalCategories . "\n";
    echo "   ✓ Tổng users: " . $totalUsers . "\n";
    echo "   ✓ Tổng đơn hàng: " . $totalOrders . "\n";
    echo "\n";
    
    // ==========================================
    // 2. THỐNG KÊ DOANH THU
    // ==========================================
    echo "2. THỐNG KÊ DOANH THU\n";
    echo str_repeat("-", 40) . "\n";
    
    $totalRevenue = $orderModel->getTotalRevenue();
    $monthlyRevenue = $orderModel->getMonthlyRevenue();
    
    echo "   ✓ Tổng doanh thu (đã giao): " . number_format($totalRevenue, 0, ',', '.') . " VND\n";
    echo "   ✓ Doanh thu tháng này: " . number_format($monthlyRevenue, 0, ',', '.') . " VND\n";
    echo "\n";
    
    // ==========================================
    // 3. THỐNG KÊ ĐƠN HÀNG THEO TRẠNG THÁI
    // ==========================================
    echo "3. THỐNG KÊ ĐƠN HÀNG THEO TRẠNG THÁI\n";
    echo str_repeat("-", 40) . "\n";
    
    $statuses = ['pending', 'processing', 'delivered', 'cancelled'];
    $statusLabels = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'delivered' => 'Đã giao',
        'cancelled' => 'Đã hủy'
    ];
    
    foreach ($statuses as $status) {
        $count = $orderModel->countByStatus($status);
        echo "   ✓ {$statusLabels[$status]}: {$count} đơn\n";
    }
    echo "\n";
    
    // ==========================================
    // 4. DOANH THU THEO THÁNG (6 THÁNG GẦN NHẤT)
    // ==========================================
    echo "4. DOANH THU THEO THÁNG (6 THÁNG GẦN NHẤT)\n";
    echo str_repeat("-", 40) . "\n";
    
    $monthlyRevenue = $orderModel->getMonthlyRevenue(6);
    foreach ($monthlyRevenue as $month) {
        echo "{$month['month']}: " . number_format($month['revenue'], 0, ',', '.') . " VNĐ ({$month['order_count']} đơn)\n";
    }
    echo "\n";
    
    // ==========================================
    // 5. SẢN PHẨM BÁN CHẠY (TOP 5)
    // ==========================================
    echo "5. SẢN PHẨM BÁN CHẠY (TOP 5)\n";
    echo str_repeat("-", 40) . "\n";
    
    $bestSelling = $productModel->getBestSellingProducts(5);
    if (empty($bestSelling)) {
        echo "   ⚠ Chưa có sản phẩm bán chạy\n";
    } else {
        foreach ($bestSelling as $index => $product) {
            echo sprintf(
                "   %d. %s\n      - Đã bán: %d sản phẩm\n      - Doanh thu: %s VND\n      - Tồn kho: %d\n",
                $index + 1,
                $product['name'],
                $product['total_sold'],
                number_format($product['total_revenue'], 0, ',', '.'),
                $product['stock']
            );
        }
    }
    echo "\n";
    
    // ==========================================
    // 6. SẢN PHẨM SẮP HẾT HÀNG (STOCK < 10)
    // ==========================================
    echo "6. SẢN PHẨM SẮP HẾT HÀNG (STOCK < 10)\n";
    echo str_repeat("-", 40) . "\n";
    
    $lowStock = $productModel->getLowStockProducts(10);
    if (empty($lowStock)) {
        echo "   ✓ Tất cả sản phẩm đều đủ hàng\n";
    } else {
        foreach ($lowStock as $product) {
            $warningIcon = $product['stock'] < 5 ? '🔴' : '🟡';
            echo sprintf(
                "   %s %s - Còn %d sản phẩm\n",
                $warningIcon,
                $product['name'],
                $product['stock']
            );
        }
    }
    echo "\n";
    
    // ==========================================
    // 7. USERS MỚI NHẤT (TOP 5)
    // ==========================================
    echo "7. USERS MỚI NHẤT (TOP 5)\n";
    echo str_repeat("-", 40) . "\n";
    
    $recentUsers = $userModel->getRecent(5);
    
    if (empty($recentUsers)) {
        echo "   ⚠ Chưa có users\n";
    } else {
        foreach ($recentUsers as $user) {
            $roleLabel = $user['role'] === 'admin' ? '[ADMIN]' : '[USER]';
            echo sprintf(
                "   %s %s (%s) - %s\n",
                $roleLabel,
                $user['username'],
                $user['email'],
                date('d/m/Y H:i', strtotime($user['created_at']))
            );
        }
    }
    echo "\n";
    
    // ==========================================
    // 8. ĐƠN HÀNG GẦN ĐÂY (TOP 5)
    // ==========================================
    echo "8. ĐƠN HÀNG GẦN ĐÂY (TOP 5)\n";
    echo str_repeat("-", 40) . "\n";
    
    $recentOrders = $orderModel->getRecent(5);
    
    if (empty($recentOrders)) {
        echo "   ⚠ Chưa có đơn hàng\n";
    } else {
        foreach ($recentOrders as $order) {
            $statusLabels = [
                'pending' => '⏳ Chờ xử lý',
                'processing' => '🔄 Đang xử lý',
                'delivered' => '✅ Đã giao',
                'cancelled' => '❌ Đã hủy'
            ];
            
            echo sprintf(
                "   %s - %s\n      %s - %s VND\n",
                $order['order_code'],
                $statusLabels[$order['status']] ?? $order['status'],
                date('d/m/Y H:i', strtotime($order['created_at'])),
                number_format($order['final_price'], 0, ',', '.')
            );
        }
    }
    echo "\n";
    
    // ==========================================
    // 9. TỔNG KẾT
    // ==========================================
    echo "9. TỔNG KẾT\n";
    echo str_repeat("-", 40) . "\n";
    
    // Tính toán một số chỉ số thêm
    $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    $deliveredCount = $orderModel->countByStatus('delivered');
    $conversionRate = $totalOrders > 0 ? ($deliveredCount / $totalOrders) * 100 : 0;
    
    echo "   ✓ Giá trị đơn hàng trung bình: " . number_format($avgOrderValue, 0, ',', '.') . " VND\n";
    echo "   ✓ Tỷ lệ hoàn thành đơn hàng: " . number_format($conversionRate, 2) . "%\n";
    echo "   ✓ Tổng số users hoạt động: " . $totalUsers . "\n";
    echo "\n";
    
    echo "========================================\n";
    echo "   ✅ TEST HOÀN TẤT!\n";
    echo "========================================\n";
    echo "\n";
    
    // Test 1: Đếm đơn hàng theo trạng thái
    echo "=== TEST 1: Đếm đơn hàng theo trạng thái ===\n";
    $pendingCount = $orderModel->countByStatus('pending');
    $processingCount = $orderModel->countByStatus('processing');
    $completedCount = $orderModel->countByStatus('completed');
    $cancelledCount = $orderModel->countByStatus('cancelled');

    echo "Đơn hàng chờ xử lý (pending): {$pendingCount}\n";
    echo "Đơn hàng đang xử lý (processing): {$processingCount}\n";
    echo "Đơn hàng hoàn thành (completed): {$completedCount}\n";
    echo "Đơn hàng đã hủy (cancelled): {$cancelledCount}\n\n";

    // Test 2: Tổng doanh thu
    echo "=== TEST 2: Tổng doanh thu ===\n";
    $totalRevenue = $orderModel->getTotalRevenue();
    echo "Tổng doanh thu: " . number_format($totalRevenue, 0, ',', '.') . " VNĐ\n\n";

    // Test 3: Doanh thu theo tháng (6 tháng gần nhất)
    echo "=== TEST 3: Doanh thu 6 tháng gần nhất ===\n";
    $monthlyRevenue = $orderModel->getMonthlyRevenue(6);
    foreach ($monthlyRevenue as $month) {
        echo "{$month['month']}: " . number_format($month['revenue'], 0, ',', '.') . " VNĐ ({$month['order_count']} đơn)\n";
    }
    echo "\n";

    // Test 4: Đơn hàng gần đây
    echo "=== TEST 4: 5 Đơn hàng gần đây ===\n";
    $recentOrders = $orderModel->getRecent(5);
    foreach ($recentOrders as $order) {
        echo "#{$order['id']} - {$order['user_name']} - " . 
             number_format($order['total_amount'], 0, ',', '.') . " VNĐ - {$order['status']} - {$order['created_at']}\n";
    }
    echo "\n";

    // Test 5: Sản phẩm bán chạy
    echo "=== TEST 5: Top 5 Sản phẩm bán chạy ===\n";
    $bestSelling = $productModel->getBestSellingProducts(5);
    foreach ($bestSelling as $product) {
        echo "{$product['name']} - Đã bán: {$product['total_sold']} - Doanh thu: " . 
             number_format($product['total_revenue'], 0, ',', '.') . " VNĐ\n";
    }
    echo "\n";

    // Test 6: Sản phẩm sắp hết hàng
    echo "=== TEST 6: Sản phẩm sắp hết hàng (tồn kho < 10) ===\n";
    $lowStock = $productModel->getLowStockProducts(10);
    foreach ($lowStock as $product) {
        echo "{$product['name']} - Còn lại: {$product['stock_quantity']} - Giá: " . 
             number_format($product['price'], 0, ',', '.') . " VNĐ\n";
    }
    echo "\n";

    // Test 7: Đếm người dùng
    echo "=== TEST 7: Tổng số người dùng ===\n";
    $totalUsers = $userModel->countAll();
    echo "Tổng số người dùng: {$totalUsers}\n\n";

    // Test 8: Đếm sản phẩm
    echo "=== TEST 8: Tổng số sản phẩm ===\n";
    $totalProducts = $productModel->countAll();
    echo "Tổng số sản phẩm: {$totalProducts}\n\n";

    // Test 9: Đếm danh mục
    echo "=== TEST 9: Tổng số danh mục ===\n";
    $totalCategories = $categoryModel->countAll();
    echo "Tổng số danh mục: {$totalCategories}\n\n";
    
} catch (Exception $e) {
    echo "\n❌ LỖI: " . $e->getMessage() . "\n";
    echo "Chi tiết: " . $e->getTraceAsString() . "\n";
    exit(1);
}
