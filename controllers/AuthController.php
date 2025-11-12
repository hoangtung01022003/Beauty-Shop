<?php
/**
 * =====================================================
 * AUTH CONTROLLER - Xử lý xác thực
 * =====================================================
 * File: controllers/AuthController.php
 * Mô tả: Xử lý đăng ký, đăng nhập, đăng xuất
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

// Load các file cần thiết
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Validator.php';

class AuthController extends BaseController {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        
        // Khởi tạo session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * ==========================================
     * ĐĂNG KÝ - REGISTER
     * ==========================================
     */
    public function register() {
        // Nếu đã đăng nhập → redirect về trang chủ
        if (isLoggedIn()) {
            $user = getUser();
            if ($user['role'] === 'admin') {
                $this->redirect(base_url('admin/dashboard'));
            } else {
                $this->redirect(base_url('user/home'));
            }
            return;
        }
        
        // GET request → hiển thị form đăng ký
        if ($this->isMethod('GET')) {
            $this->render('auth/register', [
                'title' => 'Đăng ký tài khoản',
                'errors' => [],
                'old' => []
            ]);
            return;
        }
        
        // POST request → xử lý đăng ký
        if ($this->isMethod('POST')) {
            // Lấy dữ liệu từ form
            $username = $this->input('username');
            $email = $this->input('email');
            $password = $this->input('password');
            $confirmPassword = $this->input('confirm_password');
            $phone = $this->input('phone', '');
            $address = $this->input('address', '');
            
            // Lưu lại dữ liệu cũ để hiển thị lại khi có lỗi
            $oldData = [
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'address' => $address
            ];
            
            $errors = [];
            
            // ========================================
            // VALIDATE DỮ LIỆU
            // ========================================
            
            // 1. Kiểm tra required fields
            if (empty($username)) {
                $errors['username'][] = 'Username là bắt buộc';
            }
            if (empty($email)) {
                $errors['email'][] = 'Email là bắt buộc';
            }
            if (empty($password)) {
                $errors['password'][] = 'Password là bắt buộc';
            }
            if (empty($confirmPassword)) {
                $errors['confirm_password'][] = 'Xác nhận password là bắt buộc';
            }
            
            // 2. Validate username (4-20 ký tự, chỉ chữ cái, số, gạch dưới)
            if (!empty($username)) {
                if (strlen($username) < 4) {
                    $errors['username'][] = 'Username phải có ít nhất 4 ký tự';
                } elseif (strlen($username) > 20) {
                    $errors['username'][] = 'Username không được vượt quá 20 ký tự';
                } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                    $errors['username'][] = 'Username chỉ được chứa chữ cái, số và dấu gạch dưới';
                }
            }
            
            // 3. Validate email
            if (!empty($email) && !validateEmail($email)) {
                $errors['email'][] = 'Email không hợp lệ';
            }
            
            // 4. Validate password (tối thiểu 6 ký tự)
            if (!empty($password) && strlen($password) < 6) {
                $errors['password'][] = 'Password phải có ít nhất 6 ký tự';
            }
            
            // 5. Kiểm tra password khớp
            if (!empty($password) && !empty($confirmPassword) && $password !== $confirmPassword) {
                $errors['confirm_password'][] = 'Password xác nhận không khớp';
            }
            
            // 6. Kiểm tra username đã tồn tại
            if (!empty($username) && $this->userModel->usernameExists($username)) {
                $errors['username'][] = 'Username đã được sử dụng';
            }
            
            // 7. Kiểm tra email đã tồn tại
            if (!empty($email) && $this->userModel->emailExists($email)) {
                $errors['email'][] = 'Email đã được sử dụng';
            }
            
            // 8. Validate phone (nếu có)
            if (!empty($phone) && !validatePhone($phone)) {
                $errors['phone'][] = 'Số điện thoại không hợp lệ';
            }
            
            // ========================================
            // XỬ LÝ KẾT QUẢ
            // ========================================
            
            // Nếu có lỗi → quay lại form
            if (!empty($errors)) {
                $this->render('auth/register', [
                    'title' => 'Đăng ký tài khoản',
                    'errors' => $errors,
                    'old' => $oldData
                ]);
                return;
            }
            
            // Tạo user mới
            $userData = [
                'username' => $username,
                'email' => $email,
                'password' => $password, // User model sẽ tự hash
                'role' => 'user',
                'phone' => $phone,
                'address' => $address
            ];
            
            $userId = $this->userModel->create($userData);
            
            if ($userId) {
                // Đăng ký thành công
                $this->setFlashMessage('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
                $this->redirect(base_url('auth/login'));
            } else {
                // Đăng ký thất bại
                $errors['general'][] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
                $this->render('auth/register', [
                    'title' => 'Đăng ký tài khoản',
                    'errors' => $errors,
                    'old' => $oldData
                ]);
            }
        }
    }
    
    /**
     * ==========================================
     * ĐĂNG NHẬP - LOGIN
     * ==========================================
     */
    public function login() {
        // Nếu đã đăng nhập → redirect về trang chủ
        if (isLoggedIn()) {
            $user = getUser();
            if ($user['role'] === 'admin') {
                $this->redirect(base_url('admin/dashboard'));
            } else {
                $this->redirect(base_url('user/home'));
            }
            return;
        }
        
        // GET request → hiển thị form đăng nhập
        if ($this->isMethod('GET')) {
            $this->render('auth/login', [
                'title' => 'Đăng nhập',
                'errors' => [],
                'old' => []
            ]);
            return;
        }
        
        // POST request → xử lý đăng nhập
        if ($this->isMethod('POST')) {
            // Lấy dữ liệu từ form
            $usernameOrEmail = $this->input('username');
            $password = $this->input('password');
            $remember = $this->input('remember', false);
            
            // Lưu lại username để hiển thị lại
            $oldData = [
                'username' => $usernameOrEmail
            ];
            
            $errors = [];
            
            // ========================================
            // VALIDATE DỮ LIỆU
            // ========================================
            
            // 1. Kiểm tra required
            if (empty($usernameOrEmail)) {
                $errors['username'][] = 'Username hoặc Email là bắt buộc';
            }
            if (empty($password)) {
                $errors['password'][] = 'Password là bắt buộc';
            }
            
            // Nếu có lỗi validate → quay lại form
            if (!empty($errors)) {
                $this->render('auth/login', [
                    'title' => 'Đăng nhập',
                    'errors' => $errors,
                    'old' => $oldData
                ]);
                return;
            }
            
            // ========================================
            // XÁC THỰC NGƯỜI DÙNG
            // ========================================
            
            // Tìm user theo username hoặc email
            $user = $this->userModel->authenticate($usernameOrEmail, $password);
            
            // Nếu không tìm thấy hoặc password sai
            if (!$user) {
                $errors['general'][] = 'Username/Email hoặc Password không đúng';
                $this->render('auth/login', [
                    'title' => 'Đăng nhập',
                    'errors' => $errors,
                    'old' => $oldData
                ]);
                return;
            }
            
            // ========================================
            // ĐĂNG NHẬP THÀNH CÔNG
            // ========================================
            
            // Tạo session user
            login($user);
            
            // Xử lý remember me (TODO: implement cookie)
            if ($remember) {
                // Set cookie để remember login (30 ngày)
                // setcookie('remember_token', generateToken(32), time() + (30 * 24 * 60 * 60), '/');
            }
            
            // Redirect theo role
            if ($user['role'] === 'admin') {
                $this->setFlashMessage('success', 'Chào mừng Admin ' . $user['username'] . '!');
                $this->redirect(base_url('admin/dashboard'));
            } else {
                $this->setFlashMessage('success', 'Đăng nhập thành công!');
                $this->redirect(base_url('user/home'));
            }
        }
    }
    
    /**
     * ==========================================
     * ĐĂNG XUẤT - LOGOUT
     * ==========================================
     */
    public function logout() {
        // Kiểm tra đã đăng nhập chưa
        if (!isLoggedIn()) {
            $this->redirect(base_url(''));
            return;
        }
        
        // Đăng xuất
        logout();
        
        // // Xóa cookie remember me (nếu có)
        // if (isset($_COOKIE['remember_token'])) {
        //     setcookie('remember_token', '', time() - 3600, '/');
        // }
        
        // Set flash message
        $this->setFlashMessage('success', 'Đăng xuất thành công!');
        
        // Redirect về trang chủ
        $this->redirect(base_url(''));
    }
    
    /**
     * ==========================================
     * QUÊN MẬT KHẨU - FORGOT PASSWORD
     * ==========================================
     */
    public function forgotPassword() {
        // Nếu đã đăng nhập → redirect
        if (isLoggedIn()) {
            $this->redirect(base_url(''));
            return;
        }
        
        // GET request → hiển thị form
        if ($this->isMethod('GET')) {
            $this->render('auth/forgot-password', [
                'title' => 'Quên mật khẩu',
                'errors' => [],
                'old' => []
            ]);
            return;
        }
        
        // POST request → xử lý
        if ($this->isMethod('POST')) {
            $email = $this->input('email');
            
            $errors = [];
            $oldData = ['email' => $email];
            
            // Validate email
            if (empty($email)) {
                $errors['email'][] = 'Email là bắt buộc';
            } elseif (!validateEmail($email)) {
                $errors['email'][] = 'Email không hợp lệ';
            }
            
            if (!empty($errors)) {
                $this->render('auth/forgot-password', [
                    'title' => 'Quên mật khẩu',
                    'errors' => $errors,
                    'old' => $oldData
                ]);
                return;
            }
            
            // Kiểm tra email có tồn tại không
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                $errors['email'][] = 'Email không tồn tại trong hệ thống';
                $this->render('auth/forgot-password', [
                    'title' => 'Quên mật khẩu',
                    'errors' => $errors,
                    'old' => $oldData
                ]);
                return;
            }
            
            // TODO: Gửi email reset password
            // Hiện tại chỉ hiển thị thông báo placeholder
            
            $this->setFlashMessage('info', 'Vui lòng liên hệ quản trị viên qua email admin@beautyshop.com để được hỗ trợ reset mật khẩu.');
            $this->redirect(base_url('auth/login'));
        }
    }
    
    /**
     * ==========================================
     * KIỂM TRA ĐĂNG NHẬP
     * ==========================================
     */
    public function checkAuth() {
        if (isLoggedIn()) {
            $this->json([
                'authenticated' => true,
                'user' => getUser()
            ]);
        } else {
            $this->json([
                'authenticated' => false
            ]);
        }
    }
}

