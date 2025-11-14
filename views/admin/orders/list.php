<?php
/**
 * =====================================================
 * ADMIN - DANH SÁCH ĐỠN HÀNG
 * =====================================================
 */

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
    <title><?= $pageTitle ?? 'Quản Lý Đơn Hàng' ?> - Beauty Shop Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('public/css/admin.css') ?>">
    
    <style>
        body { background-color: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 260px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 0; overflow-y: auto; z-index: 1000; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar-brand { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-brand h4 { color: white; font-weight: bold; margin: 0; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 12px 20px; color: rgba(255,255,255,0.9); text-decoration: none; transition: all 0.3s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: rgba(255,255,255,0.1); color: white; padding-left: 30px; }
        .sidebar-menu li a i { width: 20px; margin-right: 10px; }
        .main-content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        .page-header { background: white; padding: 20px 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .stats-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; align-items: center; }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 15px; }
        .stat-icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .stat-icon.info { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
        .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .stat-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .content-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table th { border-top: none; border-bottom: 2px solid #e5e7eb; color: #1f2937; font-weight: 600; font-size: 14px; padding: 12px; }
        .table td { padding: 12px; vertical-align: middle; }
        .badge { padding: 5px 12px; font-size: 12px; font-weight: 500; }
        .search-box { position: relative; }
        .search-box input { padding-left: 40px; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fas fa-spa"></i> Beauty Shop</h4>
        <small>Admin Panel</small>
    </div>
    
    <ul class="sidebar-menu">
        <li><a href="<?= base_url('admin/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="<?= base_url('admin/products') ?>"><i class="fas fa-box"></i> Sản phẩm</a></li>
        <li><a href="<?= base_url('admin/categories') ?>"><i class="fas fa-tags"></i> Danh mục</a></li>
        <li><a href="<?= base_url('admin/orders') ?>" class="active"><i class="fas fa-shopping-cart"></i> Đơn hàng</a></li>
        <li><a href="<?= base_url('admin/users') ?>"><i class="fas fa-users"></i> Người dùng</a></li>
        <li><a href="<?= base_url('home') ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Xem website</a></li>
        <li><a href="<?= base_url('auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
    </ul>
</div>

<div class="main-content">
    
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1"><i class="fas fa-shopping-cart"></i> Quản Lý Đơn Hàng</h4>
                <p class="text-muted mb-0">Quản lý và xử lý đơn hàng của khách hàng</p>
            </div>
            <div>
                <a href="<?= base_url('admin/orders/export') ?>" class="btn btn-success">
                    <i class="fas fa-download"></i> Export CSV
                </a>
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>
    
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-shopping-cart"></i></div>
            <div>
                <div class="text-muted small">Tổng đơn hàng</div>
                <h4 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h4>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
            <div>
                <div class="text-muted small">Chờ xử lý</div>
                <h4 class="mb-0"><?= number_format($stats['pending'] ?? 0) ?></h4>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon info"><i class="fas fa-check-double"></i></div>
            <div>
                <div class="text-muted small">Đã xác nhận</div>
                <h4 class="mb-0"><?= number_format($stats['confirmed'] ?? 0) ?></h4>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-shipping-fast"></i></div>
            <div>
                <div class="text-muted small">Đang giao</div>
                <h4 class="mb-0"><?= number_format($stats['shipped'] ?? 0) ?></h4>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="text-muted small">Đã giao</div>
                <h4 class="mb-0"><?= number_format($stats['delivered'] ?? 0) ?></h4>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon danger"><i class="fas fa-times-circle"></i></div>
            <div>
                <div class="text-muted small">Đã hủy</div>
                <h4 class="mb-0"><?= number_format($stats['cancelled'] ?? 0) ?></h4>
            </div>
        </div>
    </div>
    
    <div class="content-card mb-4">
        <form method="GET" action="<?= base_url('admin/orders') ?>" class="row g-3">
            <div class="col-md-4">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" class="form-control" 
                           placeholder="Tìm mã đơn, tên KH, email..." 
                           value="<?= htmlspecialchars($keyword ?? '') ?>">
                </div>
            </div>
            
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" <?= ($selectedStatus ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="confirmed" <?= ($selectedStatus ?? '') === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="shipped" <?= ($selectedStatus ?? '') === 'shipped' ? 'selected' : '' ?>>Đang giao</option>
                    <option value="delivered" <?= ($selectedStatus ?? '') === 'delivered' ? 'selected' : '' ?>>Đã giao</option>
                    <option value="cancelled" <?= ($selectedStatus ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" 
                       placeholder="Từ ngày" 
                       value="<?= htmlspecialchars($dateFrom ?? '') ?>">
            </div>
            
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" 
                       placeholder="Đến ngày" 
                       value="<?= htmlspecialchars($dateTo ?? '') ?>">
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </div>
        </form>
    </div>
    
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Danh Sách Đơn Hàng (<?= number_format($totalOrders ?? 0) ?>)</h5>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Không có đơn hàng nào</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Số lượng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th width="150" class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= $order['id'] ?></td>
                                <td>
                                    <strong class="text-primary"><?= htmlspecialchars($order['order_code']) ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($order['username'] ?? 'N/A') ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($order['email'] ?? '') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $order['item_count'] ?? 0 ?> sản phẩm</span>
                                </td>
                                <td>
                                    <strong class="text-success"><?= formatPrice($order['final_price']) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusText = [
                                        'pending' => 'Chờ xử lý',
                                        'confirmed' => 'Đã xác nhận',
                                        'shipped' => 'Đang giao',
                                        'delivered' => 'Đã giao',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    $class = $statusClass[$order['status']] ?? 'secondary';
                                    $text = $statusText[$order['status']] ?? $order['status'];
                                    ?>
                                    <span class="badge bg-<?= $class ?>"><?= $text ?></span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-calendar"></i>
                                        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                    </small>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url('admin/orders/detail/' . $order['id']) ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <a href="<?= base_url('admin/orders/edit/' . $order['id']) ?>" 
                                           class="btn btn-sm btn-outline-warning"
                                           title="Sửa đơn hàng">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($order['status'] === 'cancelled'): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete(<?= $order['id'] ?>, '<?= htmlspecialchars($order['order_code']) ?>')"
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
            
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('admin/orders?page=' . ($currentPage - 1) . (!empty($keyword) ? '&keyword=' . urlencode($keyword) : '') . (!empty($selectedStatus) ? '&status=' . $selectedStatus : '')) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('admin/orders?page=' . $i . (!empty($keyword) ? '&keyword=' . urlencode($keyword) : '') . (!empty($selectedStatus) ? '&status=' . $selectedStatus : '')) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('admin/orders?page=' . ($currentPage + 1) . (!empty($keyword) ? '&keyword=' . urlencode($keyword) : '') . (!empty($selectedStatus) ? '&status=' . $selectedStatus : '')) ?>">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(orderId, orderCode) {
    if (confirm('Bạn có chắc chắn muốn xóa đơn hàng "' + orderCode + '"?\n\nChỉ có thể xóa đơn hàng đã hủy!')) {
        fetch('<?= base_url('admin/orders/delete/') ?>' + orderId, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
            if (data) console.log('Response:', data);
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa đơn hàng');
        });
    }
}
</script>

</body>
</html>

