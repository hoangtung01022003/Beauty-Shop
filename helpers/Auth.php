<?php
/**
 * =====================================================
 * AUTH HELPER - Hỗ trợ xác thực và phân quyền
 * =====================================================
 * File: helpers/Auth.php
 * Mô tả: Các hàm kiểm tra đăng nhập, phân quyền
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

/**
 * Khởi động session nếu chưa có
 * @return void
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Kiểm tra người dùng đã đăng nhập chưa
 * @return bool
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Lấy thông tin user từ session
 * @return array|null - Trả về mảng thông tin user hoặc null
 */
function getUser() {
    startSession();
    
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? 'user',
        'avatar' => $_SESSION['avatar'] ?? null
    ];
}

/**
 * Lấy thông tin user hiện tại (alias của getUser)
 * @return array|null
 */
function getCurrentUser() {
    return getUser();
}

/**
 * Lấy ID của user đang đăng nhập
 * @return int|null
 */
function getUserId() {
    startSession();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Kiểm tra user có phải admin không
 * @return bool
 */
function isAdmin() {
    startSession();
    
    if (!isLoggedIn()) {
        return false;
    }
    
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Đăng nhập user (lưu thông tin vào session)
 * @param array $user - Mảng thông tin user từ database
 * @return void
 */
function login($user) {
    startSession();
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['avatar'] = $user['avatar'] ?? null;
    $_SESSION['logged_in_at'] = time();
    
    // Regenerate session ID để bảo mật
    session_regenerate_id(true);
}

/**
 * Đăng xuất user (xóa session)
 * @return void
 */
function logout() {
    startSession();
    
    // Xóa tất cả session variables
    $_SESSION = [];
    
    // Xóa session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Hủy session
    session_destroy();
}

/**
 * Bắt buộc phải đăng nhập mới truy cập được
 * Nếu chưa đăng nhập → chuyển đến trang login
 * @param string $redirectUrl - URL chuyển hướng sau khi login thành công
 * @return void
 */
function requireLogin($redirectUrl = null) {
    if (!isLoggedIn()) {
        // Lưu URL hiện tại để redirect sau khi login
        if ($redirectUrl === null) {
            $redirectUrl = $_SERVER['REQUEST_URI'] ?? '/';
        }
        
        startSession();
        $_SESSION['redirect_after_login'] = $redirectUrl;
        
        // Chuyển đến trang login
        redirect(base_url('index.php?page=login'));
    }
}

/**
 * Bắt buộc phải là admin mới truy cập được
 * Nếu không phải admin → chuyển về trang chủ
 * @return void
 */
function requireAdmin() {
    requireLogin(); // Phải đăng nhập trước
    
    if (!isAdmin()) {
        // Không phải admin → chuyển về trang chủ
        setFlash('error', 'Bạn không có quyền truy cập trang này!');
        redirect(base_url());
    }
}

/**
 * Kiểm tra user có quyền truy cập resource không
 * @param int $userId - ID của user cần kiểm tra
 * @return bool
 */
function canAccess($userId) {
    $currentUser = getUser();
    
    if (!$currentUser) {
        return false;
    }
    
    // Admin có thể truy cập tất cả
    if (isAdmin()) {
        return true;
    }
    
    // User chỉ truy cập được resource của mình
    return $currentUser['id'] == $userId;
}

/**
 * Lấy URL redirect sau khi login
 * @param string $default - URL mặc định nếu không có redirect
 * @return string
 */
function getRedirectAfterLogin($default = '/') {
    startSession();
    
    $redirect = $_SESSION['redirect_after_login'] ?? $default;
    
    // Xóa redirect URL khỏi session
    unset($_SESSION['redirect_after_login']);
    
    return $redirect;
}

/**
 * Hash mật khẩu
 * @param string $password - Mật khẩu cần hash
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify mật khẩu
 * @param string $password - Mật khẩu nhập vào
 * @param string $hash - Hash từ database
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Tạo token ngẫu nhiên (cho reset password, verify email...)
 * @param int $length - Độ dài token
 * @return string
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Kiểm tra session có hết hạn không (timeout sau 2 giờ)
 * @param int $timeout - Thời gian timeout (giây)
 * @return bool
 */
function isSessionExpired($timeout = 7200) {
    startSession();
    
    if (!isset($_SESSION['logged_in_at'])) {
        return true;
    }
    
    $elapsed = time() - $_SESSION['logged_in_at'];
    
    if ($elapsed > $timeout) {
        logout();
        return true;
    }
    
    // Cập nhật thời gian
    $_SESSION['logged_in_at'] = time();
    
    return false;
}

