<?php
/**
 * =====================================================
 * USER ADMIN CONTROLLER
 * =====================================================
 * File: controllers/Admin/UserAdminController.php
 * Mô tả: Quản lý người dùng cho admin
 * Ngày tạo: 12/11/2025
 * =====================================================
 */

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../helpers/Validator.php';

class UserAdminController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        // Kiểm tra quyền admin
        requireAdmin();

        $this->userModel = new User();
    }

    /**
     * Danh sách người dùng - có phân trang
     */
    public function index()
    {
        try {
            // Lấy tham số phân trang và tìm kiếm
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            // Tìm kiếm
            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
            $role = isset($_GET['role']) ? $_GET['role'] : '';

            // Lấy danh sách users
            if (!empty($keyword)) {
                $users = $this->userModel->search($keyword, $perPage, $offset);
                $totalUsers = count($this->userModel->search($keyword));
            } elseif (!empty($role)) {
                $users = $this->userModel->getUsersByRole($role, $perPage, $offset);
                $totalUsers = $this->userModel->countAll($role);
            } else {
                $users = $this->userModel->getAll($perPage, $offset);
                $totalUsers = $this->userModel->countAll();
            }

            // Tính tổng số trang
            $totalPages = ceil($totalUsers / $perPage);

            // Lấy thống kê
            $stats = $this->userModel->getStats();

            $data = [
                'pageTitle' => 'Quản Lý Người Dùng',
                'users' => $users,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalUsers' => $totalUsers,
                'keyword' => $keyword,
                'selectedRole' => $role,
                'stats' => $stats
            ];

            $this->view('admin/users/list', $data);

        } catch (Exception $e) {
            error_log("Error in UserAdminController::index(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect(base_url('admin/dashboard'));
        }
    }

    /**
     * Hiển thị form sửa thông tin user
     */
    public function edit($id)
    {
        try {
            // Xử lý form submit
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleUpdate($id);
                return;
            }

            // Lấy thông tin user
            $user = $this->userModel->findById($id);

            if (!$user) {
                $this->setFlashMessage('error', 'Không tìm thấy người dùng');
                redirect(base_url('admin/users'));
                return;
            }

            // Không cho sửa thông tin chính mình (tránh tự hạ quyền)
            $currentUser = getUser();
            if ($currentUser['id'] == $id) {
                $this->setFlashMessage('warning', 'Không thể sửa thông tin của chính bạn từ trang này');
                redirect(base_url('admin/users'));
                return;
            }

            $data = [
                'pageTitle' => 'Sửa Thông Tin Người Dùng',
                'user' => $user
            ];

            $this->view('admin/users/edit', $data);

        } catch (Exception $e) {
            error_log("Error in UserAdminController::edit(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url('admin/users'));
        }
    }

    /**
     * Xử lý cập nhật thông tin user
     */
    private function handleUpdate($id)
    {
        try {
            // Kiểm tra user tồn tại
            $user = $this->userModel->findById($id);

            if (!$user) {
                $this->setFlashMessage('error', 'Không tìm thấy người dùng');
                redirect(base_url('admin/users'));
                return;
            }

            // Validate dữ liệu
            $errors = [];

            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? 'user');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $newPassword = trim($_POST['password'] ?? '');

            // Validate username
            if (empty($username)) {
                $errors[] = 'Tên đăng nhập không được để trống';
            } elseif ($this->userModel->usernameExists($username, $id)) {
                $errors[] = 'Tên đăng nhập đã tồn tại';
            }

            // Validate email
            if (empty($email)) {
                $errors[] = 'Email không được để trống';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            } elseif ($this->userModel->emailExists($email, $id)) {
                $errors[] = 'Email đã tồn tại';
            }

            // Validate role
            if (!in_array($role, ['admin', 'user'])) {
                $errors[] = 'Vai trò không hợp lệ';
            }

            if (!empty($errors)) {
                $this->setFlashMessage('error', implode('<br>', $errors));
                redirect(base_url('admin/users/edit/' . $id));
                return;
            }

            // Chuẩn bị dữ liệu cập nhật
            $updateData = [
                'username' => $username,
                'email' => $email,
                'role' => $role,
                'phone' => $phone,
                'address' => $address
            ];

            // Cập nhật password nếu có
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $this->setFlashMessage('error', 'Mật khẩu phải có ít nhất 6 ký tự');
                    redirect(base_url('admin/users/edit/' . $id));
                    return;
                }
                $updateData['password'] = $newPassword; // Model sẽ tự hash
            }

            // Cập nhật vào database
            $result = $this->userModel->update($id, $updateData);

            if ($result) {
                $this->setFlashMessage('success', 'Cập nhật thông tin người dùng thành công');
            } else {
                $this->setFlashMessage('error', 'Có lỗi khi cập nhật thông tin');
            }

            redirect(base_url('admin/users'));

        } catch (Exception $e) {
            error_log("Error in UserAdminController::handleUpdate(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect(base_url('admin/users/edit/' . $id));
        }
    }

    /**
     * Xóa người dùng
     */
    public function delete($id)
    {
        // Debug logging
        error_log("=== DELETE USER CALLED ===");
        error_log("User ID: " . $id);
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Current User: " . json_encode(getUser()));
        
        try {
            // Chỉ nhận POST request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log("ERROR: Not POST method");
                $this->setFlashMessage('error', 'Invalid request method');
                redirect(base_url('admin/users'));
                return;
            }

            // Kiểm tra user tồn tại
            $user = $this->userModel->findById($id);
            
            error_log("User found: " . ($user ? 'Yes' : 'No'));

            if (!$user) {
                error_log("ERROR: User not found");
                $this->setFlashMessage('error', 'Không tìm thấy người dùng');
                redirect(base_url('admin/users'));
                return;
            }

            // Không cho xóa chính mình
            $currentUser = getUser();
            if ($currentUser['id'] == $id) {
                error_log("ERROR: Cannot delete self");
                $this->setFlashMessage('error', 'Không thể xóa tài khoản của chính bạn');
                redirect(base_url('admin/users'));
                return;
            }

            // Không cho xóa admin cuối cùng
            if ($user['role'] === 'admin') {
                $adminCount = $this->userModel->countAll('admin');
                error_log("Admin count: " . $adminCount);
                if ($adminCount <= 1) {
                    error_log("ERROR: Cannot delete last admin");
                    $this->setFlashMessage('error', 'Không thể xóa admin cuối cùng trong hệ thống');
                    redirect(base_url('admin/users'));
                    return;
                }
            }

            // Xóa user
            error_log("Attempting to delete user ID: " . $id);
            $result = $this->userModel->delete($id);
            error_log("Delete result: " . ($result ? 'Success' : 'Failed'));

            if ($result) {
                $this->setFlashMessage('success', 'Xóa người dùng thành công');
            } else {
                $this->setFlashMessage('error', 'Có lỗi khi xóa người dùng');
            }

            redirect(base_url('admin/users'));

        } catch (Exception $e) {
            error_log("EXCEPTION in delete: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect(base_url('admin/users'));
        }
    }

    /**
     * Cập nhật trạng thái user (nếu cần)
     */
    public function updateStatus($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->setFlashMessage('error', 'Invalid request method');
                redirect(base_url('admin/users'));
                return;
            }

            $status = $_POST['status'] ?? '';

            if (!in_array($status, ['active', 'inactive'])) {
                $this->setFlashMessage('error', 'Trạng thái không hợp lệ');
                redirect(base_url('admin/users'));
                return;
            }

            $result = $this->userModel->update($id, ['status' => $status]);

            if ($result) {
                $this->setFlashMessage('success', 'Cập nhật trạng thái thành công');
            } else {
                $this->setFlashMessage('error', 'Có lỗi khi cập nhật trạng thái');
            }

            redirect(base_url('admin/users'));

        } catch (Exception $e) {
            error_log("Error in UserAdminController::updateStatus(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url('admin/users'));
        }
    }
}

