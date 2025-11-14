<?php
/**
 * =====================================================
 * DASHBOARD ADMIN CONTROLLER
 * =====================================================
 * File: controllers/Admin/DashboardController.php
 * Mô tả: Quản lý trang dashboard admin với thống kê
 * Ngày tạo: 12/11/2025
 * =====================================================
 */

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../helpers/Auth.php';

class DashboardController extends BaseController {
    
    private $productModel;
    private $categoryModel;
    private $userModel;
    private $orderModel;
    
    public function __construct() {
        // Kiểm tra admin
        requireAdmin();
        
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->userModel = new User();
        $this->orderModel = new Order();
    }
    
    /**
     * Trang dashboard chính
     * - Lấy các số liệu thống kê
     * - Doanh thu theo tháng (6 tháng)
     * - Sản phẩm bán chạy (top 5)
     * - Users mới nhất (top 5)
     * - Orders gần đây (top 5)
     */
    public function index() {
        try {
            // 1. Tổng sản phẩm
            $totalProducts = $this->productModel->countAll();
            
            // 2. Tổng danh mục
            $totalCategories = $this->categoryModel->countAll();
            
            // 3. Tổng users
            $totalUsers = $this->userModel->countAll();
            
            // 4. Tổng orders
            $totalOrders = $this->orderModel->countAll();
            
            // 5. Tổng doanh thu (chỉ đơn hàng hoàn thành)
            $totalRevenue = $this->orderModel->getTotalRevenue();
            
            // 6. Orders gần đây (top 5)
            $recentOrders = $this->orderModel->getAll(null, 5);
            
            // 7. Doanh thu theo tháng (6 tháng gần nhất)
            $monthlyRevenue = $this->getMonthlyRevenue(6);
            
            // 8. Sản phẩm bán chạy (top 5)
            $bestSellingProducts = $this->getBestSellingProducts(5);
            
            // 9. Users mới nhất (top 5)
            $recentUsers = $this->userModel->getRecent(5);
            
            // 10. Thống kê đơn hàng theo trạng thái
            $orderStats = [
                'pending' => $this->orderModel->countByStatus('pending'),
                'processing' => $this->orderModel->countByStatus('processing'),
                'delivered' => $this->orderModel->countByStatus('delivered'),
                'cancelled' => $this->orderModel->countByStatus('cancelled')
            ];
            
            // 11. Sản phẩm sắp hết hàng
            $lowStockProducts = $this->getLowStockProducts(5);
            
            // 12. Doanh thu tháng hiện tại
            $monthlyRevenueNow = $this->orderModel->getMonthlyRevenue();
            
            // Load view với dữ liệu
            $data = [
                'pageTitle' => 'Dashboard Admin',
                'totalProducts' => $totalProducts,
                'totalCategories' => $totalCategories,
                'totalUsers' => $totalUsers,
                'totalOrders' => $totalOrders,
                'totalRevenue' => $totalRevenue,
                'monthlyRevenueNow' => $monthlyRevenueNow,
                'recentOrders' => $recentOrders,
                'monthlyRevenue' => $monthlyRevenue,
                'bestSellingProducts' => $bestSellingProducts,
                'recentUsers' => $recentUsers,
                'orderStats' => $orderStats,
                'lowStockProducts' => $lowStockProducts
            ];
            
            $this->view('admin/dashboard/index', $data);
            
        } catch (Exception $e) {
            error_log("Error in DashboardController::index(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            echo "Dashboard Error: " . $e->getMessage();
        }
    }
    
    /**
     * Lấy doanh thu theo tháng (6 tháng gần nhất)
     * @param int $months - Số tháng cần lấy
     * @return array - ['labels' => [...], 'data' => [...]]
     */
    private function getMonthlyRevenue($months = 6) {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        DATE_FORMAT(created_at, '%m/%Y') as month_label,
                        SUM(final_price) as revenue
                    FROM orders
                    WHERE status = 'delivered'
                    AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL :months MONTH)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY month ASC";
            
            $db = getDB(); // Sử dụng getDB() thay vì $this->orderModel->db
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':months', (int)$months, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Chuẩn bị dữ liệu cho chart
            $labels = [];
            $data = [];
            
            foreach ($results as $row) {
                $labels[] = $row['month_label'];
                $data[] = (float)$row['revenue'];
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
            
        } catch (Exception $e) {
            error_log("Error in getMonthlyRevenue(): " . $e->getMessage());
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }
    
    /**
     * Lấy sản phẩm bán chạy (top N)
     * @param int $limit
     * @return array
     */
    private function getBestSellingProducts($limit = 5) {
        try {
            $sql = "SELECT 
                        p.id,
                        p.name,
                        p.image,
                        p.price,
                        p.stock,
                        COALESCE(SUM(oi.quantity), 0) as total_sold,
                        COALESCE(SUM(oi.quantity * oi.price), 0) as total_revenue
                    FROM products p
                    LEFT JOIN order_items oi ON p.id = oi.product_id
                    LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'delivered'
                    GROUP BY p.id, p.name, p.image, p.price, p.stock
                    ORDER BY total_sold DESC
                    LIMIT :limit";
            
            $db = getDB(); // Sử dụng getDB() thay vì $this->productModel->db
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in getBestSellingProducts(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy sản phẩm sắp hết hàng (stock < 10)
     * @param int $limit
     * @return array
     */
    private function getLowStockProducts($limit = 5) {
        try {
            $sql = "SELECT 
                        id,
                        name,
                        image,
                        price,
                        stock,
                        category_id
                    FROM products
                    WHERE stock < 10 AND status = 'active'
                    ORDER BY stock ASC
                    LIMIT :limit";
            
            $db = getDB(); // Sử dụng getDB() thay vì $this->productModel->db
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in getLowStockProducts(): " . $e->getMessage());
            return [];
        }
    }
}

