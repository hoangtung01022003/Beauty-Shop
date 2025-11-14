<?php
/**
 * =====================================================
 * ADMIN - SỬA THÔNG TIN NGƯỜI DÙNG
 * =====================================================
 * File: views/admin/users/edit.php
 * Mô tả: Form sửa thông tin người dùng
 * =====================================================
 */

// Kiểm tra admin
if (!isLoggedIn() || !isAdmin()) {
    redirect(base_url('auth/login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sửa Người Dùng' ?> - Beauty Shop Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-brand h4 {
            color: white;
            font-weight: bold;
            margin: 0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            padding-left: 30px;
        }
        
        .sidebar-menu li a i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }
        
        .page-header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 10px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .user-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 32px;
            margin: 0 auto 20px;
        }
        
        .info-box {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fas fa-spa"></i> Beauty Shop</h4>
        <small>Admin Panel</small>
    </div>
    
    <ul class="sidebar-menu">
        <li><a href="<?= base_url('admin/dashboard') ?>">
            <i class="fas fa-home"></i> Dashboard
        </a></li>
        <li><a href="<?= base_url('admin/products') ?>">
            <i class="fas fa-box"></i> Sản phẩm
        </a></li>
        <li><a href="<?= base_url('admin/categories') ?>">
            <i class="fas fa-tags"></i> Danh mục
        </a></li>
        <li><a href="<?= base_url('admin/orders') ?>">
            <i class="fas fa-shopping-cart"></i> Đơn hàng
        </a></li>
        <li><a href="<?= base_url('admin/users') ?>" class="active">
            <i class="fas fa-users"></i> Người dùng
        </a></li>
        <li><a href="<?= base_url('home') ?>" target="_blank">
            <i class="fas fa-external-link-alt"></i> Xem website
        </a></li>
        <li><a href="<?= base_url('auth/logout') ?>">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1"><i class="fas fa-user-edit"></i> Sửa Thông Tin Người Dùng</h4>
                <p class="text-muted mb-0">Cập nhật thông tin tài khoản người dùng</p>
            </div>
            <div>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php 
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>
    
    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                
                <!-- User Avatar -->
                <div class="text-center mb-4">
                    <div class="user-avatar-large">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                    <h5><?= htmlspecialchars($user['username']) ?></h5>
                    <small class="text-muted">ID: <?= $user['id'] ?> | Đăng ký: <?= date('d/m/Y', strtotime($user['created_at'])) ?></small>
                </div>
                
                <!-- Info Box -->
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Lưu ý:</strong> Để trống mật khẩu nếu không muốn thay đổi. Thay đổi vai trò admin cần thận trọng.
                </div>
                
                <!-- Edit Form -->
                <form method="POST" action="<?= base_url('admin/users/edit/' . $user['id']) ?>">
                    
                    <div class="row">
                        <!-- Username -->
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Tên đăng nhập <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?= htmlspecialchars($user['username']) ?>"
                                   required>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Role -->
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">
                                <i class="fas fa-user-tag"></i> Vai trò <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        
                        <!-- Phone -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i> Số điện thoại
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                   placeholder="Nhập số điện thoại">
                        </div>
                    </div>
                    
                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Địa chỉ
                        </label>
                        <textarea class="form-control" 
                                  id="address" 
                                  name="address" 
                                  rows="3"
                                  placeholder="Nhập địa chỉ"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Password Section -->
                    <h6 class="mb-3"><i class="fas fa-key"></i> Đổi Mật Khẩu (Tùy chọn)</h6>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Chú ý:</strong> Chỉ nhập mật khẩu mới nếu bạn muốn thay đổi. Để trống nếu giữ nguyên mật khẩu hiện tại.
                    </div>
                    
                    <div class="row">
                        <!-- New Password -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Mật khẩu mới
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)">
                            <small class="text-muted">Để trống nếu không muốn đổi mật khẩu</small>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock"></i> Xác nhận mật khẩu
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    form.addEventListener('submit', function(e) {
        // Validate password confirmation if password is entered
        if (password.value !== '' || confirmPassword.value !== '') {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                confirmPassword.focus();
                return false;
            }
            
            if (password.value.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                password.focus();
                return false;
            }
        }
    });
});
</script>

</body>
</html>

