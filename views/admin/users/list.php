<?php
/**
 * =====================================================
 * ADMIN - DANH SÁCH NGƯỜI DÙNG
 * =====================================================
 * File: views/admin/users/list.php
 * Mô tả: Hiển thị danh sách người dùng với tìm kiếm và phân trang
 * =====================================================
 */

// Kiểm tra admin
if (!isLoggedIn() || !isAdmin()) {
    redirect(base_url('auth/login'));
    exit;
}

$currentUser = getUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Quản Lý Người Dùng' ?> - Beauty Shop Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('public/css/admin.css') ?>">
    
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
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
        }
        
        .stat-icon.primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .stat-icon.success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .stat-icon.danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        
        .table th {
            border-top: none;
            border-bottom: 2px solid #e5e7eb;
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
            padding: 12px;
        }
        
        .table td {
            padding: 12px;
            vertical-align: middle;
        }
        
        .badge {
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding-left: 40px;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
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
                <h4 class="mb-1"><i class="fas fa-users"></i> Quản Lý Người Dùng</h4>
                <p class="text-muted mb-0">Quản lý tài khoản người dùng trong hệ thống</p>
            </div>
            <div>
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-primary">
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
    
    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="text-muted small">Tổng người dùng</div>
                <h4 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h4>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-crown"></i>
            </div>
            <div>
                <div class="text-muted small">Admin</div>
                <h4 class="mb-0"><?= number_format($stats['total_admin'] ?? 0) ?></h4>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <div class="text-muted small">User</div>
                <h4 class="mb-0"><?= number_format($stats['total_user'] ?? 0) ?></h4>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <div class="text-muted small">Mới hôm nay</div>
                <h4 class="mb-0"><?= number_format($stats['today_new'] ?? 0) ?></h4>
            </div>
        </div>
    </div>
    
    <!-- Search & Filter -->
    <div class="content-card mb-4">
        <form method="GET" action="<?= base_url('admin/users') ?>" class="row g-3">
            <div class="col-md-5">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" class="form-control" 
                           placeholder="Tìm theo tên, email, số điện thoại..." 
                           value="<?= htmlspecialchars($keyword ?? '') ?>">
                </div>
            </div>
            
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin" <?= ($selectedRole ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= ($selectedRole ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Danh Sách Người Dùng (<?= number_format($totalUsers ?? 0) ?>)</h5>
        </div>
        
        <?php if (empty($users)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-user-slash fa-3x mb-3"></i>
                <p>Không tìm thấy người dùng nào</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Người dùng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th width="100">Vai trò</th>
                            <th width="150">Ngày tạo</th>
                            <th width="150" class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2">
                                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                                            <?php if ($user['id'] == $currentUser['id']): ?>
                                                <span class="badge bg-info ms-1">Bạn</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-envelope"></i>
                                        <?= htmlspecialchars($user['email']) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($user['phone'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i>
                                            <?= htmlspecialchars($user['phone']) ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-crown"></i> Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user"></i> User
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-calendar"></i>
                                        <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                    </small>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <?php if ($user['id'] != $currentUser['id']): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('admin/users?page=' . ($currentPage - 1) . (!empty($keyword) ? '&keyword=' . urlencode($keyword) : '') . (!empty($selectedRole) ? '&role=' . $selectedRole : '')) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Page Numbers -->
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('admin/users?page=' . $i . (!empty($keyword) ? '&keyword=' . urlencode($keyword) : '') . (!empty($selectedRole) ? '&role=' . $selectedRole : '')) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <!-- Next -->
                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('admin/users?page=' . ($currentPage + 1) . (!empty($keyword) ? '&keyword=' . urlencode($keyword) : '') . (!empty($selectedRole) ? '&role=' . $selectedRole : '')) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
</div>

<!-- Delete Confirmation Script -->
<script>
function confirmDelete(userId, username) {
    if (confirm('Bạn có chắc chắn muốn xóa người dùng "' + username + '"?\n\nHành động này không thể hoàn tác!')) {
        // Sử dụng fetch API để gửi request
        fetch('<?= base_url('admin/users/delete/') ?>' + userId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'confirm_delete=1'
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.text();
            }
        })
        .then(data => {
            if (data) {
                console.log('Response:', data);
            }
            // Reload page để cập nhật danh sách
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa người dùng');
        });
    }
}
</script>

</body>
</html>

