<?php
/**
 * =====================================================
 * TEST DASHBOARD - Kiểm tra Dashboard Admin
 * =====================================================
 * File: test_dashboard_admin.php
 * Mô tả: Test file để kiểm tra Dashboard Admin hoạt động
 * =====================================================
 */

// Khởi tạo session
session_start();

// Load các file cần thiết
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/helpers/Helper.php';
require_once __DIR__ . '/helpers/Auth.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Order.php';

echo "=== TEST DASHBOARD ADMIN ===\n\n";

try {
    // 1. Kiểm tra kết nối database
    echo "1. Kiểm tra kết nối database...\n";
    $db = getDB();
    echo "   ✅ Kết nối database thành công\n\n";
    
    // 2. Khởi tạo các Model
    echo "2. Khởi tạo các Model...\n";
    $userModel = new User();
    $productModel = new Product();
    $categoryModel = new Category();
    $orderModel = new Order();
    echo "   ✅ Khởi tạo Model thành công\n\n";
    
    // 3. Test đếm tổng số
    echo "3. Thống kê tổng số:\n";
    $totalProducts = $productModel->countAll();
    $totalCategories = $categoryModel->countAll();
    $totalUsers = $userModel->countAll();
    $totalOrders = $orderModel->countAll();
    
    echo "   - Tổng sản phẩm: " . number_format($totalProducts) . "\n";
    echo "   - Tổng danh mục: " . number_format($totalCategories) . "\n";
    echo "   - Tổng người dùng: " . number_format($totalUsers) . "\n";
    echo "   - Tổng đơn hàng: " . number_format($totalOrders) . "\n\n";
    
    // 4. Test tổng doanh thu
    echo "4. Doanh thu:\n";
    $totalRevenue = $orderModel->getTotalRevenue();
    $monthlyRevenue = $orderModel->getMonthlyRevenue();
    echo "   - Tổng doanh thu: " . number_format($totalRevenue) . " đ\n";
    echo "   - Doanh thu tháng này: " . number_format($monthlyRevenue) . " đ\n\n";
    
    // 5. Test đơn hàng gần đây
    echo "5. Đơn hàng gần đây (top 5):\n";
    $recentOrders = $orderModel->getAll(null, 5);
    if (!empty($recentOrders)) {
        foreach ($recentOrders as $order) {
            echo "   - #{$order['id']} | " . 
                 ($order['order_code'] ?? 'N/A') . " | " . 
                 number_format($order['final_price']) . "đ | " .
                 $order['status'] . "\n";
        }
    } else {
        echo "   ⚠️  Chưa có đơn hàng\n";
    }
    echo "\n";
    
    // 6. Test doanh thu theo tháng
    echo "6. Doanh thu 6 tháng gần đây:\n";
    $sql = "SELECT 
                DATE_FORMAT(created_at, '%m/%Y') as month_label,
                SUM(final_price) as revenue
            FROM orders
            WHERE status = 'delivered'
            AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($monthlyData)) {
        foreach ($monthlyData as $data) {
            echo "   - {$data['month_label']}: " . number_format($data['revenue']) . " đ\n";
        }
    } else {
        echo "   ⚠️  Chưa có dữ liệu doanh thu\n";
    }
    echo "\n";
    
    // 7. Test sản phẩm bán chạy
    echo "7. Sản phẩm bán chạy (top 5):\n";
    $sql = "SELECT 
                p.id,
                p.name,
                p.price,
                COALESCE(SUM(oi.quantity), 0) as total_sold
            FROM products p
            LEFT JOIN order_items oi ON p.id = oi.product_id
            LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'delivered'
            GROUP BY p.id, p.name, p.price
            ORDER BY total_sold DESC
            LIMIT 5";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $bestSelling = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($bestSelling)) {
        foreach ($bestSelling as $product) {
            echo "   - {$product['name']} | " . 
                 "Đã bán: " . number_format($product['total_sold']) . " | " .
                 number_format($product['price']) . "đ\n";
        }
    } else {
        echo "   ⚠️  Chưa có sản phẩm nào được bán\n";
    }
    echo "\n";
    
    // 8. Test người dùng mới
    echo "8. Người dùng mới (top 5):\n";
    $recentUsers = $userModel->getRecent(5);
    if (!empty($recentUsers)) {
        foreach ($recentUsers as $user) {
            echo "   - {$user['username']} | {$user['email']} | " .
                 "{$user['role']} | " .
                 date('d/m/Y', strtotime($user['created_at'])) . "\n";
        }
    } else {
        echo "   ⚠️  Chưa có người dùng mới\n";
    }
    echo "\n";
    
    // 9. Test thống kê đơn hàng theo trạng thái
    echo "9. Thống kê đơn hàng theo trạng thái:\n";
    $statuses = ['pending', 'processing', 'delivered', 'cancelled'];
    foreach ($statuses as $status) {
        $count = $orderModel->countByStatus($status);
        $statusText = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy'
        ];
        echo "   - {$statusText[$status]}: " . number_format($count) . "\n";
    }
    echo "\n";
    
    // 10. Test sản phẩm sắp hết hàng
    echo "10. Sản phẩm sắp hết hàng (stock < 10):\n";
    $sql = "SELECT id, name, stock, price
            FROM products
            WHERE stock < 10 AND status = 'active'
            ORDER BY stock ASC
            LIMIT 5";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $lowStock = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($lowStock)) {
        foreach ($lowStock as $product) {
            echo "   - {$product['name']} | " .
                 "Còn: {$product['stock']} | " .
                 number_format($product['price']) . "đ\n";
        }
    } else {
        echo "   ✅ Tất cả sản phẩm đều đủ hàng\n";
    }
    echo "\n";
    
    echo "==============================================\n";
    echo "✅ TEST HOÀN THÀNH - Dashboard hoạt động tốt!\n";
    echo "==============================================\n\n";
    
    echo "📌 Để xem Dashboard, truy cập:\n";
    echo "   URL: http://localhost/WebBanMyPham/admin/dashboard\n";
    echo "   (Cần đăng nhập với tài khoản admin)\n\n";
    
} catch (Exception $e) {
    echo "\n❌ LỖI: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
