<?php
/**
 * =====================================================
 * HEADER LAYOUT - User
 * =====================================================
 * File: views/layouts/header.php
 * Mô tả: Header chung cho trang user
 * =====================================================
 */

// Lấy thông tin user nếu đã đăng nhập
$currentUser = null;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../models/User.php';
    $userModel = new User();
    $currentUser = $userModel->getById($_SESSION['user_id']);
}

$pageTitle = $pageTitle ?? 'Mỹ Phẩm Chính Hãng';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= $pageTitle ?> - Shop Mỹ Phẩm</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('public/images/favicon.png') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('public/css/style.css') ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    
<!-- Top Bar -->
<div class="top-bar bg-dark text-white py-2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small>
                    <i class="bi bi-telephone"></i> Hotline: 0123-456-789 | 
                    <i class="bi bi-envelope"></i> Email: contact@cosmetic.vn
                </small>
            </div>
            <div class="col-md-6 text-end">
                <?php if ($currentUser): ?>
                    <small>
                        <i class="bi bi-person-circle"></i> Xin chào, <strong><?= htmlspecialchars($currentUser['username']) ?></strong> | 
                        <a href="<?= base_url('profile') ?>" class="text-white text-decoration-none">Tài khoản</a> | 
                        <a href="<?= base_url('auth/logout') ?>" class="text-white text-decoration-none">Đăng xuất</a>
                    </small>
                <?php else: ?>
                    <small>
                        <a href="<?= base_url('auth/login') ?>" class="text-white text-decoration-none">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </a> | 
                        <a href="<?= base_url('auth/register') ?>" class="text-white text-decoration-none">
                            <i class="bi bi-person-plus"></i> Đăng ký
                        </a>
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<nav class="navbar navbar-expand-xl navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="<?= base_url() ?>">
            <i class="bi bi-star-fill text-primary"></i>
            <span class="text-primary">Beauty</span> Shop
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url() ?>">
                        <i class="bi bi-house-door"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('products') ?>">
                        <i class="bi bi-grid"></i> Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('products?category=1') ?>">
                        <i class="bi bi-palette"></i> Trang điểm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('products?category=2') ?>">
                        <i class="bi bi-droplet"></i> Chăm sóc da
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-info-circle"></i> Giới thiệu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-telephone"></i> Liên hệ
                    </a>
                </li>
            </ul>
            
            <!-- Cart & User Actions -->
            <div class="d-flex align-items-center gap-3">
                <!-- Search -->
                <form action="<?= base_url('products/search') ?>" method="GET" class="d-none d-lg-block">
                    <div class="input-group input-group-sm">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm sản phẩm..." style="width: 200px;">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Cart -->
                <a href="<?= base_url('cart') ?>" class="btn btn-outline-primary position-relative">
                    <i class="bi bi-cart3"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                        <?php
                        $cartCount = 0;
                        if (isset($_SESSION['cart'])) {
                            $cartCount = count($_SESSION['cart']);
                        }
                        echo $cartCount;
                        ?>
                    </span>
                </a>
                
                <!-- User Dropdown -->
                <?php if ($currentUser): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('profile') ?>">
                                    <i class="bi bi-person"></i> Tài khoản
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('profile/orders') ?>">
                                    <i class="bi bi-bag"></i> Đơn hàng của tôi
                                </a>
                            </li>
                            <?php if ($currentUser['role'] === 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('admin') ?>">
                                        <i class="bi bi-speedometer2"></i> Admin Panel
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>" 
                                   onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?')">
                                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php if (isset($_SESSION['flash'])): ?>
    <div class="container mt-3">
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                <?php if ($type === 'success'): ?>
                    <i class="bi bi-check-circle"></i>
                <?php elseif ($type === 'error'): ?>
                    <i class="bi bi-exclamation-triangle"></i>
                <?php elseif ($type === 'warning'): ?>
                    <i class="bi bi-exclamation-circle"></i>
                <?php else: ?>
                    <i class="bi bi-info-circle"></i>
                <?php endif; ?>
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<style>
body {
    font-family: 'Roboto', sans-serif;
}

.top-bar {
    font-size: 0.85rem;
}

.navbar-brand {
    font-size: 1.5rem;
}

.navbar-nav .nav-link {
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #0d6efd !important;
    transform: translateY(-2px);
}
</style>
</body>
</html>

