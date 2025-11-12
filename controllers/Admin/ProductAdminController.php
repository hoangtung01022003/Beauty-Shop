<?php
/**
 * =====================================================
 * PRODUCT ADMIN CONTROLLER
 * =====================================================
 * File: controllers/Admin/ProductAdminController.php
 * Mô tả: Quản lý sản phẩm (CRUD) cho admin
 * =====================================================
 */

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';
require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../helpers/FileUpload.php';
require_once __DIR__ . '/../../helpers/Validator.php';

class ProductAdminController extends BaseController {
    
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        
        // Kiểm tra admin
        requireAdmin();
        
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    /**
     * Hiển thị danh sách sản phẩm
     */
    public function index() {
        try {
            // Lấy tham số
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
            
            $perPage = 20;
            $offset = ($page - 1) * $perPage;
            
            // Lấy danh sách sản phẩm
            if ($search) {
                $products = $this->productModel->search($search, $perPage, $offset);
                $totalProducts = count($this->productModel->search($search));
            } elseif ($categoryId) {
                $products = $this->productModel->getByCategory($categoryId, $perPage, $offset);
                $totalProducts = $this->productModel->countByCategory($categoryId);
            } else {
                $products = $this->productModel->getAll($perPage, $offset);
                $totalProducts = $this->productModel->countAll();
            }
            
            $totalPages = ceil($totalProducts / $perPage);
            
            // Lấy danh sách danh mục cho filter
            $categories = $this->categoryModel->getAll();
            
            // Load view
            $data = [
                'products' => $products,
                'categories' => $categories,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalProducts' => $totalProducts,
                'search' => $search,
                'selectedCategory' => $categoryId,
                'pageTitle' => 'Quản lý sản phẩm'
            ];
            
            $this->view('admin/products/list', $data);
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect(base_url('admin/dashboard'));
        }
    }
    
    /**
     * Thêm sản phẩm mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý POST - Thêm sản phẩm
            try {
                // Validate dữ liệu
                $errors = $this->validateProductData($_POST);
                
                if (!empty($errors)) {
                    $this->setFlashMessage('error', 'Vui lòng kiểm tra lại dữ liệu nhập vào');
                    $_SESSION['errors'] = $errors;
                    $_SESSION['old_data'] = $_POST;
                    redirect(base_url('admin/products/create'));
                    return;
                }
                
                // Xử lý upload hình ảnh
                $imagePath = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = new FileUpload();
                    $uploadResult = $upload->upload($_FILES['image'], 'products');
                    
                    if ($uploadResult['success']) {
                        $imagePath = $uploadResult['path'];
                    } else {
                        $this->setFlashMessage('error', 'Lỗi upload hình ảnh: ' . $uploadResult['message']);
                        redirect(base_url('admin/products/create'));
                        return;
                    }
                }
                
                // Chuẩn bị dữ liệu
                $data = [
                    'name' => sanitize($_POST['name']),
                    'description' => sanitize($_POST['description']),
                    'price' => (float)$_POST['price'],
                    'cost_price' => !empty($_POST['cost_price']) ? (float)$_POST['cost_price'] : null,
                    'category_id' => (int)$_POST['category_id'],
                    'stock' => (int)$_POST['stock'],
                    'image' => $imagePath,
                    'status' => $_POST['status'] ?? 'active'
                ];
                
                // Tạo sản phẩm
                $productId = $this->productModel->create($data);
                
                if ($productId) {
                    $this->setFlashMessage('success', 'Thêm sản phẩm thành công!');
                    redirect(base_url('admin/products'));
                } else {
                    $this->setFlashMessage('error', 'Không thể thêm sản phẩm');
                    redirect(base_url('admin/products/create'));
                }
                
            } catch (Exception $e) {
                $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
                redirect(base_url('admin/products/create'));
            }
            
        } else {
            // Hiển thị form thêm mới
            $categories = $this->categoryModel->getAll('active');
            
            $data = [
                'categories' => $categories,
                'pageTitle' => 'Thêm sản phẩm mới',
                'action' => 'create',
                'product' => null
            ];
            
            $this->view('admin/products/add', $data);
        }
    }
    
    /**
     * Chỉnh sửa sản phẩm
     */
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý POST - Cập nhật sản phẩm
            try {
                // Kiểm tra sản phẩm tồn tại
                $product = $this->productModel->getById($id);
                if (!$product) {
                    $this->setFlashMessage('error', 'Không tìm thấy sản phẩm');
                    redirect(base_url('admin/products'));
                    return;
                }
                
                // Validate dữ liệu
                $errors = $this->validateProductData($_POST, $id);
                
                if (!empty($errors)) {
                    $this->setFlashMessage('error', 'Vui lòng kiểm tra lại dữ liệu nhập vào');
                    $_SESSION['errors'] = $errors;
                    $_SESSION['old_data'] = $_POST;
                    redirect(base_url('admin/products/edit/' . $id));
                    return;
                }
                
                // Chuẩn bị dữ liệu cập nhật
                $data = [
                    'name' => sanitize($_POST['name']),
                    'description' => sanitize($_POST['description']),
                    'price' => (float)$_POST['price'],
                    'cost_price' => !empty($_POST['cost_price']) ? (float)$_POST['cost_price'] : null,
                    'category_id' => (int)$_POST['category_id'],
                    'stock' => (int)$_POST['stock'],
                    'status' => $_POST['status'] ?? 'active'
                ];
                
                // Xử lý upload hình ảnh mới (nếu có)
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = new FileUpload();
                    $uploadResult = $upload->upload($_FILES['image'], 'products');
                    
                    if ($uploadResult['success']) {
                        // Xóa ảnh cũ
                        if ($product['image'] && file_exists($product['image'])) {
                            @unlink($product['image']);
                        }
                        
                        $data['image'] = $uploadResult['path'];
                    } else {
                        $this->setFlashMessage('warning', 'Lỗi upload hình ảnh mới: ' . $uploadResult['message']);
                    }
                }
                
                // Cập nhật sản phẩm
                $success = $this->productModel->update($id, $data);
                
                if ($success) {
                    $this->setFlashMessage('success', 'Cập nhật sản phẩm thành công!');
                    redirect(base_url('admin/products'));
                } else {
                    $this->setFlashMessage('error', 'Không thể cập nhật sản phẩm');
                    redirect(base_url('admin/products/edit/' . $id));
                }
                
            } catch (Exception $e) {
                $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
                redirect(base_url('admin/products/edit/' . $id));
            }
            
        } else {
            // Hiển thị form chỉnh sửa
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                $this->setFlashMessage('error', 'Không tìm thấy sản phẩm');
                redirect(base_url('admin/products'));
                return;
            }
            
            $categories = $this->categoryModel->getAll('active');
            
            $data = [
                'product' => $product,
                'categories' => $categories,
                'pageTitle' => 'Chỉnh sửa sản phẩm: ' . $product['name'],
                'action' => 'edit'
            ];
            
            $this->view('admin/products/edit', $data);
        }
    }
    
    /**
     * Xóa sản phẩm
     */
    public function delete($id) {
        try {
            // Kiểm tra sản phẩm tồn tại
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                $this->setFlashMessage('error', 'Không tìm thấy sản phẩm');
                redirect(base_url('admin/products'));
                return;
            }
            
            // Xóa file hình ảnh
            if ($product['image'] && file_exists($product['image'])) {
                @unlink($product['image']);
            }
            
            // Xóa sản phẩm
            $success = $this->productModel->delete($id);
            
            if ($success) {
                $this->setFlashMessage('success', 'Xóa sản phẩm thành công!');
            } else {
                $this->setFlashMessage('error', 'Không thể xóa sản phẩm');
            }
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
        
        redirect(base_url('admin/products'));
    }
    
    /**
     * Validate dữ liệu sản phẩm
     */
    private function validateProductData($data, $productId = null) {
        $errors = [];
        
        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'Tên sản phẩm phải có ít nhất 3 ký tự';
        } elseif (strlen($data['name']) > 150) {
            $errors['name'] = 'Tên sản phẩm không được quá 150 ký tự';
        }
        
        // Validate price
        if (empty($data['price'])) {
            $errors['price'] = 'Giá bán không được để trống';
        } elseif (!is_numeric($data['price']) || $data['price'] <= 0) {
            $errors['price'] = 'Giá bán phải là số dương';
        }
        
        // Validate cost_price (optional)
        if (!empty($data['cost_price'])) {
            if (!is_numeric($data['cost_price']) || $data['cost_price'] < 0) {
                $errors['cost_price'] = 'Giá vốn phải là số không âm';
            } elseif ($data['cost_price'] > $data['price']) {
                $errors['cost_price'] = 'Giá vốn không được lớn hơn giá bán';
            }
        }
        
        // Validate category
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Vui lòng chọn danh mục';
        } else {
            $category = $this->categoryModel->getById($data['category_id']);
            if (!$category) {
                $errors['category_id'] = 'Danh mục không hợp lệ';
            }
        }
        
        // Validate stock
        if (!isset($data['stock'])) {
            $errors['stock'] = 'Số lượng tồn kho không được để trống';
        } elseif (!is_numeric($data['stock']) || $data['stock'] < 0) {
            $errors['stock'] = 'Số lượng tồn kho phải là số không âm';
        }
        
        // Validate image (chỉ khi tạo mới)
        if ($productId === null && (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE)) {
            $errors['image'] = 'Vui lòng chọn hình ảnh sản phẩm';
        }
        
        return $errors;
    }
    
    /**
     * Xem chi tiết sản phẩm (dành cho admin)
     */
    public function view($id) {
        try {
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                $this->setFlashMessage('error', 'Không tìm thấy sản phẩm');
                redirect(base_url('admin/products'));
                return;
            }
            
            $data = [
                'product' => $product,
                'pageTitle' => 'Chi tiết sản phẩm: ' . $product['name']
            ];
            
            $this->view('admin/products/view', $data);
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect(base_url('admin/products'));
        }
    }
}

