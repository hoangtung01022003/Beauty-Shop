<?php
/**
 * =====================================================
 * ADMIN DASHBOARD - Trang chủ admin
 * =====================================================
 * File: views/admin/dashboard/index.php
 * Mô tả: Dashboard admin với thống kê, biểu đồ và bảng
 * =====================================================
 */

// Kiểm tra admin - sử dụng hàm helper thay vì class Auth
if (!isLoggedIn() || !isAdmin()) {
    redirect(base_url('auth/login'));
    exit;
}

$user = getUser(); // Lấy thông tin user từ session
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard Admin' ?> - Beauty Shop</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('public/css/admin.css') ?>">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }
        
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
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
        
        .top-bar {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        
        .stats-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stats-card.primary .icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stats-card.success .icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .stats-card.warning .icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .stats-card.danger .icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .stats-card h3 {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stats-card p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
        }
        
        .stats-card .trend {
            font-size: 12px;
            margin-top: 10px;
        }
        
        .stats-card .trend.up {
            color: var(--success-color);
        }
        
        .stats-card .trend.down {
            color: var(--danger-color);
        }
        
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .chart-container h5 {
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .table-container h5 {
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            border-top: none;
            border-bottom: 2px solid #e5e7eb;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 14px;
            padding: 12px;
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
        }
        
        .badge {
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
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
        <li><a href="<?= base_url('admin/dashboard') ?>" class="active">
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
        <li><a href="<?= base_url('admin/users') ?>">
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
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h4 class="mb-0">Dashboard</h4>
            <small class="text-muted">Chào mừng trở lại, <?= htmlspecialchars($user['username']) ?>!</small>
        </div>
        <div>
            <span class="text-muted"><i class="far fa-calendar"></i> <?= date('d/m/Y') ?></span>
        </div>
    </div>
    
    <!-- Alert Messages -->
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
    <div class="row mb-4">
        <!-- Card 1: Tổng sản phẩm -->
        <div class="col-xl-3 col-md-6 mb-4">
            <?php include __DIR__ . '/cards/stats-card.php'; 
            renderStatsCard([
                'title' => 'Tổng Sản Phẩm',
                'value' => number_format($totalProducts ?? 0),
                'icon' => 'fa-box',
                'color' => 'primary',
                'trend' => '+5%',
                'trendDirection' => 'up'
            ]);
            ?>
        </div>
        
        <!-- Card 2: Tổng người dùng -->
        <div class="col-xl-3 col-md-6 mb-4">
            <?php 
            renderStatsCard([
                'title' => 'Tổng Người Dùng',
                'value' => number_format($totalUsers ?? 0),
                'icon' => 'fa-users',
                'color' => 'success',
                'trend' => '+12%',
                'trendDirection' => 'up'
            ]);
            ?>
        </div>
        
        <!-- Card 3: Tổng đơn hàng -->
        <div class="col-xl-3 col-md-6 mb-4">
            <?php 
            renderStatsCard([
                'title' => 'Tổng Đơn Hàng',
                'value' => number_format($totalOrders ?? 0),
                'icon' => 'fa-shopping-cart',
                'color' => 'warning',
                'trend' => '+8%',
                'trendDirection' => 'up'
            ]);
            ?>
        </div>
        
        <!-- Card 4: Tổng doanh thu -->
        <div class="col-xl-3 col-md-6 mb-4">
            <?php 
            renderStatsCard([
                'title' => 'Tổng Doanh Thu',
                'value' => number_format($totalRevenue ?? 0) . ' đ',
                'icon' => 'fa-dollar-sign',
                'color' => 'danger',
                'trend' => '+15%',
                'trendDirection' => 'up'
            ]);
            ?>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row mb-4">
        <!-- Chart: Doanh thu theo tháng -->
        <div class="col-lg-8 mb-4">
            <div class="chart-container">
                <h5><i class="fas fa-chart-line"></i> Doanh Thu 6 Tháng Gần Đây</h5>
                <?php include __DIR__ . '/cards/chart.php'; ?>
            </div>
        </div>
        
        <!-- Order Stats -->
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <h5><i class="fas fa-chart-pie"></i> Trạng Thái Đơn Hàng</h5>
                <canvas id="orderStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Tables -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="table-container">
                <h5><i class="fas fa-shopping-bag"></i> Đơn Hàng Gần Đây</h5>
                <?php include __DIR__ . '/widgets/recent-orders.php'; ?>
            </div>
        </div>
        
        <!-- Best Selling Products -->
        <div class="col-lg-4 mb-4">
            <div class="table-container">
                <h5><i class="fas fa-fire"></i> Sản Phẩm Bán Chạy</h5>
                <?php include __DIR__ . '/widgets/top-products.php'; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Users & Low Stock -->
    <div class="row">
        <!-- Recent Users -->
        <div class="col-lg-6 mb-4">
            <div class="table-container">
                <h5><i class="fas fa-user-plus"></i> Người Dùng Mới</h5>
                <?php include __DIR__ . '/widgets/recent-users.php'; ?>
            </div>
        </div>
        
        <!-- Low Stock Products -->
        <div class="col-lg-6 mb-4">
            <div class="table-container">
                <h5><i class="fas fa-exclamation-triangle text-warning"></i> Sản Phẩm Sắp Hết Hàng</h5>
                <?php include __DIR__ . '/widgets/low-stock.php'; ?>
            </div>
        </div>
    </div>
    
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js - Order Status -->
<script>
// Order Status Pie Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Chờ xử lý', 'Đang xử lý', 'Đã giao', 'Đã hủy'],
        datasets: [{
            data: [
                <?= $orderStats['pending'] ?? 0 ?>,
                <?= $orderStats['processing'] ?? 0 ?>,
                <?= $orderStats['delivered'] ?? 0 ?>,
                <?= $orderStats['cancelled'] ?? 0 ?>
            ],
            backgroundColor: [
                '#f59e0b',
                '#3b82f6',
                '#10b981',
                '#ef4444'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>

