<?php
/**
 * =====================================================
 * CATEGORY ADMIN CONTROLLER
 * =====================================================
 * File: controllers/Admin/CategoryAdminController.php
 * Mô tả: Quản lý danh mục (CRUD) cho admin
 * =====================================================
 */

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/Category.php';
require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../helpers/FileUpload.php';

class CategoryAdminController extends BaseController {
    
    private $categoryModel;
    
    public function __construct() {
        // Kiểm tra admin
        requireAdmin();
        
        $this->categoryModel = new Category();
    }
    
    /**
     * Hiển thị danh sách danh mục
     */
    public function index() {
        try {
            // Lấy tham số
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            $perPage = 20;
            $offset = ($page - 1) * $perPage;
            
            // Lấy danh sách danh mục
            if ($search) {
                $categories = $this->categoryModel->search($search, $perPage, $offset);
                $totalCategories = count($this->categoryModel->search($search));
            } else {
                $categories = $this->categoryModel->getAll($perPage, $offset);
                $totalCategories = $this->categoryModel->count();
            }
            
            $totalPages = ceil($totalCategories / $perPage);
            
            // Load view
            $data = [
                'categories' => $categories,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalCategories' => $totalCategories,
                'search' => $search,
                'pageTitle' => 'Quản lý danh mục'
            ];
            
            $this->view('admin/categories/list', $data);
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect(base_url('admin/dashboard'));
        }
    }
    
    /**
     * Thêm danh mục mới
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate dữ liệu
                $errors = $this->validateCategoryData($_POST);
                
                if (!empty($errors)) {
                    $this->setFlashMessage('error', 'Dữ liệu không hợp lệ: ' . implode(', ', $errors));
                    $_SESSION['errors'] = $errors;
                    $_SESSION['old_data'] = $_POST;
                    redirect(base_url('admin/categories/add'));
                    return;
                }
                
                // Xử lý upload hình ảnh (nếu có)
                $imagePath = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = new FileUpload();
                    $uploadResult = $upload->upload($_FILES['image'], 'categories');
                    
                    if ($uploadResult['success']) {
                        $imagePath = $uploadResult['path'];
                    } else {
                        $this->setFlashMessage('error', 'Lỗi upload hình ảnh: ' . $uploadResult['message']);
                        $_SESSION['old_data'] = $_POST;
                        redirect(base_url('admin/categories/add'));
                        return;
                    }
                }
                
                // Chuẩn bị dữ liệu
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
                    'image' => $imagePath,
                    'status' => 'active'
                ];
                
                // Tạo danh mục
                $categoryId = $this->categoryModel->create($data);
                
                if ($categoryId) {
                    $this->setFlashMessage('success', 'Thêm danh mục "' . $data['name'] . '" thành công!');
                    redirect(base_url('admin/categories'));
                } else {
                    $this->setFlashMessage('error', 'Không thể thêm danh mục. Vui lòng kiểm tra dữ liệu và thử lại.');
                    $_SESSION['old_data'] = $_POST;
                    redirect(base_url('admin/categories/add'));
                }
                
            } catch (Exception $e) {
                error_log("Error in CategoryAdminController::add(): " . $e->getMessage());
                $this->setFlashMessage('error', 'Có lỗi hệ thống xảy ra: ' . $e->getMessage());
                $_SESSION['old_data'] = $_POST;
                redirect(base_url('admin/categories/add'));
            }
            
        } else {
            // Hiển thị form thêm mới
            $data = [
                'pageTitle' => 'Thêm danh mục mới',
                'action' => 'add',
                'category' => null
            ];
            
            $this->view('admin/categories/add', $data);
        }
    }
    
    /**
     * Chỉnh sửa danh mục
     */
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý POST - Cập nhật danh mục
            try {
                // Kiểm tra danh mục tồn tại
                $category = $this->categoryModel->getById($id);
                if (!$category) {
                    $this->setFlashMessage('error', 'Không tìm thấy danh mục');
                    redirect(base_url('admin/categories'));
                    return;
                }
                
                // Validate dữ liệu
                $errors = $this->validateCategoryData($_POST, $id);
                
                if (!empty($errors)) {
                    $this->setFlashMessage('error', 'Vui lòng kiểm tra lại dữ liệu nhập vào');
                    $_SESSION['errors'] = $errors;
                    $_SESSION['old_data'] = $_POST;
                    redirect(base_url('admin/categories/edit/' . $id));
                    return;
                }
                
                // Chuẩn bị dữ liệu cập nhật
                $data = [
                    'name' => sanitize($_POST['name']),
                    'description' => sanitize($_POST['description'] ?? '')
                ];
                
                // Xử lý upload hình ảnh mới (nếu có)
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = new FileUpload();
                    $uploadResult = $upload->upload($_FILES['image'], 'categories');
                    
                    if ($uploadResult['success']) {
                        // Xóa ảnh cũ
                        if ($category['image'] && file_exists($category['image'])) {
                            @unlink($category['image']);
                        }
                        
                        $data['image'] = $uploadResult['path'];
                    } else {
                        $this->setFlashMessage('warning', 'Lỗi upload hình ảnh mới: ' . $uploadResult['message']);
                    }
                }
                
                // Cập nhật danh mục
                $success = $this->categoryModel->update($id, $data);
                
                if ($success) {
                    $this->setFlashMessage('success', 'Cập nhật danh mục thành công!');
                    redirect(base_url('admin/categories'));
                } else {
                    $this->setFlashMessage('error', 'Không thể cập nhật danh mục');
                    redirect(base_url('admin/categories/edit/' . $id));
                }
                
            } catch (Exception $e) {
                $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
                redirect(base_url('admin/categories/edit/' . $id));
            }
            
        } else {
            // Hiển thị form chỉnh sửa
            $category = $this->categoryModel->getById($id);
            
            if (!$category) {
                $this->setFlashMessage('error', 'Không tìm thấy danh mục');
                redirect(base_url('admin/categories'));
                return;
            }
            
            $data = [
                'category' => $category,
                'pageTitle' => 'Chỉnh sửa danh mục: ' . $category['name'],
                'action' => 'edit'
            ];
            
            $this->view('admin/categories/edit', $data);
        }
    }
    
    /**
     * Xóa danh mục
     */
    public function delete($id) {
        try {
            // Kiểm tra danh mục tồn tại
            $category = $this->categoryModel->getById($id);
            
            if (!$category) {
                $this->setFlashMessage('error', 'Không tìm thấy danh mục');
                redirect(base_url('admin/categories'));
                return;
            }
            
            // Kiểm tra có sản phẩm nào đang dùng danh mục này không
            $productCount = $this->categoryModel->countProducts($id);
            
            if ($productCount > 0) {
                $this->setFlashMessage('error', 'Không thể xóa danh mục này vì có ' . $productCount . ' sản phẩm đang sử dụng. Vui lòng chuyển các sản phẩm sang danh mục khác trước.');
                redirect(base_url('admin/categories'));
                return;
            }
            
            // Xóa file hình ảnh
            if ($category['image'] && file_exists($category['image'])) {
                @unlink($category['image']);
            }
            
            // Xóa danh mục
            $success = $this->categoryModel->delete($id);
            
            if ($success) {
                $this->setFlashMessage('success', 'Xóa danh mục thành công!');
            } else {
                $this->setFlashMessage('error', 'Không thể xóa danh mục');
            }
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
        
        redirect(base_url('admin/categories'));
    }
    
    /**
     * Validate dữ liệu danh mục
     */
    private function validateCategoryData($data, $categoryId = null) {
        $errors = [];
        
        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Tên danh mục không được để trống';
        } elseif (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Tên danh mục phải có ít nhất 2 ký tự';
        } elseif (strlen(trim($data['name'])) > 100) {
            $errors['name'] = 'Tên danh mục không được quá 100 ký tự';
        } else {
            // Kiểm tra trùng tên
            try {
                $nameExists = $this->categoryModel->isNameExists(trim($data['name']), $categoryId);
                
                if ($nameExists) {
                    $errors['name'] = 'Tên danh mục đã tồn tại';
                }
            } catch (Exception $e) {
                error_log("Error checking name exists: " . $e->getMessage());
                // Không thêm lỗi validation nếu không thể kiểm tra được
            }
        }
        
        // Validate description (optional)
        if (!empty($data['description']) && strlen(trim($data['description'])) > 1000) {
            $errors['description'] = 'Mô tả không được quá 1000 ký tự';
        }
        
        return $errors;
    }
}



