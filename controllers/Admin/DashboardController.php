<?php
/**
 * =====================================================
 * DASHBOARD ADMIN CONTROLLER
 * =====================================================
 * File: controllers/Admin/DashboardController.php
 * Mô tả: Quản lý trang dashboard admin
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
        parent::__construct();
        
        // Kiểm tra admin
        requireAdmin();
        
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->userModel = new User();
        $this->orderModel = new Order();
    }
    
    /**
     * Trang dashboard chính
     */
    public function index() {
        try {
            // Lấy thống kê tổng quan
            $stats = $this->getStatistics();
            
            // Lấy đơn hàng gần đây
            $recentOrders = $this->orderModel->getRecent(10);
            
            // Lấy sản phẩm bán chạy
            $topProducts = $this->productModel->getBestSelling(5);
            
            // Lấy sản phẩm sắp hết hàng
            $lowStockProducts = $this->productModel->getLowStock(5);
            
            // Load view
            $data = [
                'stats' => $stats,
                'recentOrders' => $recentOrders,
                'topProducts' => $topProducts,
                'lowStockProducts' => $lowStockProducts,
                'pageTitle' => 'Dashboard'
            ];
            
            $this->view('admin/dashboard/index', $data);
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            echo "Dashboard Error: " . $e->getMessage();
        }
    }
    
    /**
     * Lấy thống kê tổng quan
     */
    private function getStatistics() {
        try {
            $stats = [];
            
            // Tổng số sản phẩm
            $stats['total_products'] = $this->productModel->count();
            
            // Tổng số danh mục
            $stats['total_categories'] = $this->categoryModel->count();
            
            // Tổng số người dùng
            $stats['total_users'] = $this->userModel->count();
            
            // Tổng số đơn hàng
            $stats['total_orders'] = $this->orderModel->count();
            
            // Đơn hàng pending
            $stats['pending_orders'] = $this->orderModel->countByStatus('pending');
            
            // Tổng doanh thu
            $stats['total_revenue'] = $this->orderModel->getTotalRevenue();
            
            // Doanh thu tháng này
            $stats['monthly_revenue'] = $this->orderModel->getMonthlyRevenue();
            
            // Sản phẩm sắp hết hàng
            $stats['low_stock_count'] = $this->productModel->countLowStock();
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error in getStatistics(): " . $e->getMessage());
            return [];
        }
    }
}

