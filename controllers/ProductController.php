<?php
/**
 * =====================================================
 * PRODUCT CONTROLLER - User
 * =====================================================
 * File: controllers/ProductController.php
 * Mô tả: Xử lý các action sản phẩm cho user
 * =====================================================
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class ProductController extends BaseController
{
    private $productModel;
    private $categoryModel;
    
    public function __construct()
    {
        // Không cần gọi parent::__construct() vì BaseController không có constructor
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    /**
     * Trang chủ - Home page
     */
    public function home()
    {
        try {
            // Lấy danh mục
            $categories = $this->categoryModel->getAll('active');
            
            // Lấy sản phẩm mới nhất
            $latestProducts = $this->productModel->getLatest(8);
            
            // Lấy sản phẩm bán chạy
            $bestSellingProducts = $this->productModel->getBestSelling(8);
            
            $data = [
                'pageTitle' => 'Trang chủ - Mỹ Phẩm Chính Hãng',
                'categories' => $categories,
                'latestProducts' => $latestProducts,
                'bestSellingProducts' => $bestSellingProducts
            ];
            
            $this->view('user/home', $data);
            
        } catch (Exception $e) {
            error_log("Error in ProductController::home(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi tải trang chủ');
            $this->view('user/home', [
                'categories' => [],
                'latestProducts' => [],
                'bestSellingProducts' => []
            ]);
        }
    }
    
    /**
     * Danh sách sản phẩm
     */
    public function index()
    {
        try {
            // Lấy tham số
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
            
            $perPage = 12;
            $offset = ($page - 1) * $perPage;
            
            // Lấy sản phẩm
            if ($categoryId) {
                $products = $this->productModel->getByCategory($categoryId, $perPage, $offset);
                $totalProducts = $this->productModel->countByCategory($categoryId);
            } else {
                $products = $this->productModel->getAll($perPage, $offset);
                $totalProducts = $this->productModel->countAll();
            }
            
            // Tính tổng số trang
            $totalPages = ceil($totalProducts / $perPage);
            
            // Lấy danh mục kèm số lượng sản phẩm
            $categories = $this->categoryModel->getAllWithProductCount();
            
            $data = [
                'pageTitle' => 'Danh sách sản phẩm',
                'products' => $products,
                'categories' => $categories,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalProducts' => $totalProducts,
                'selectedCategory' => $categoryId
            ];
            
            $this->view('user/products/list', $data);
            
        } catch (Exception $e) {
            error_log("Error in ProductController::index(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url());
        }
    }
    
    /**
     * Chi tiết sản phẩm
     */
    public function detail($id)
    {
        try {
            // Lấy thông tin sản phẩm
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                $this->setFlashMessage('error', 'Không tìm thấy sản phẩm');
                redirect(base_url('products'));
                return;
            }
            
            // Lấy sản phẩm liên quan
            $relatedProducts = $this->productModel->getRelated($id, 4);
            
            $data = [
                'pageTitle' => $product['name'],
                'product' => $product,
                'relatedProducts' => $relatedProducts
            ];
            
            $this->view('user/products/detail', $data);
            
        } catch (Exception $e) {
            error_log("Error in ProductController::detail(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url('products'));
        }
    }
    
    /**
     * Tìm kiếm sản phẩm
     */
    public function search()
    {
        try {
            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
            
            if (empty($keyword)) {
                redirect(base_url('products'));
                return;
            }
            
            // Phân trang
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 12;
            $offset = ($page - 1) * $perPage;
            
            // Tìm kiếm
            $products = $this->productModel->search($keyword, $perPage, $offset);
            $totalProducts = count($this->productModel->search($keyword));
            $totalPages = ceil($totalProducts / $perPage);
            
            // Lấy danh mục
            $categories = $this->categoryModel->getAllWithProductCount();
            
            $data = [
                'pageTitle' => 'Tìm kiếm: ' . $keyword,
                'products' => $products,
                'categories' => $categories,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalProducts' => $totalProducts,
                'keyword' => $keyword,
                'selectedCategory' => null
            ];
            
            $this->view('user/products/list', $data);
            
        } catch (Exception $e) {
            error_log("Error in ProductController::search(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi tìm kiếm');
            redirect(base_url('products'));
        }
    }
    
    /**
     * Lọc sản phẩm theo danh mục
     */
    public function category($categoryId)
    {
        try {
            // Kiểm tra danh mục tồn tại
            $category = $this->categoryModel->getById($categoryId);
            
            if (!$category) {
                $this->setFlashMessage('error', 'Không tìm thấy danh mục');
                redirect(base_url('products'));
                return;
            }
            
            // Redirect về products với category param
            redirect(base_url('products?category=' . $categoryId));
            
        } catch (Exception $e) {
            error_log("Error in ProductController::category(): " . $e->getMessage());
            redirect(base_url('products'));
        }
    }
}

