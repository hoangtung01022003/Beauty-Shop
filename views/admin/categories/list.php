<?php
/**
 * =====================================================
 * ADMIN CATEGORIES LIST - Danh sách danh mục
 * =====================================================
 * File: views/admin/categories/list.php
 * Mô tả: Quản lý danh sách danh mục cho admin
 * =====================================================
 */

if (!isLoggedIn() || !isAdmin()) {
    redirect(base_url('auth/login'));
    exit;
}

$categories = $categories ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalCategories = $totalCategories ?? 0;
$search = $search ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Danh Mục - Beauty Shop Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
        <li><a href="<?= base_url('admin/categories') ?>" class="active"><i class="fas fa-tags"></i> Danh mục</a></li>
        <li><a href="<?= base_url('admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Đơn hàng</a></li>
        <li><a href="<?= base_url('admin/users') ?>"><i class="fas fa-users"></i> Người dùng</a></li>
        <li><a href="<?= base_url('home') ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Xem website</a></li>
        <li><a href="<?= base_url('auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
    </ul>
</div>

<div class="main-content">
    
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1"><i class="fas fa-tags"></i> Quản Lý Danh Mục</h4>
                <p class="text-muted mb-0">Tổng số: <?= $totalCategories ?> danh mục</p>
            </div>
            <div>
                <a href="<?= base_url('admin/categories/add') ?>" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Thêm danh mục mới
                </a>
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    
    <div class="content-card mb-4">
        <form method="GET" action="<?= base_url('admin/categories') ?>" class="row g-3">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Tìm theo tên danh mục..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
            <div class="col-md-3">
                <a href="<?= base_url('admin/categories') ?>" class="btn btn-secondary w-100">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Danh Sách Danh Mục</h5>
        </div>
        
        <?php if (!empty($categories)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <!-- <th width="100">Hình ảnh</th> -->
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th width="120" class="text-center">Số sản phẩm</th>
                            <th width="150" class="text-center">Ngày tạo</th>
                            <th width="180" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <!-- <td>
                                    <?php if (!empty($category['image'])): ?>
                                        <img src="<?= base_url($category['image']) ?>" 
                                            alt="<?= htmlspecialchars($category['name']) ?>"
                                            class="img-thumbnail"
                                            style="width: 80px; height: 80px; object-fit: cover;"
                                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
                                    <?php else: ?>
                                        <img src="<?= base_url('public/images/placeholder.png') ?>" 
                                            alt="No image"
                                            class="img-thumbnail"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    <?php endif; ?>
                                </td> -->
                                <td>
                                    <strong><?= htmlspecialchars($category['name']) ?></strong>
                                </td>
                                <td>
                                    <?php if (!empty($category['description'])): ?>
                                        <small class="text-muted">
                                            <?= htmlspecialchars(substr($category['description'], 0, 100)) ?>
                                            <?= strlen($category['description']) > 100 ? '...' : '' ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted fst-italic">Chưa có mô tả</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        <?= $category['product_count'] ?? 0 ?> sản phẩm
                                    </span>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        <i class="far fa-calendar"></i>
                                        <?= date('d/m/Y', strtotime($category['created_at'])) ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/categories/edit/' . $category['id']) ?>" 
                                       class="btn btn-sm btn-outline-warning"
                                       title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')"
                                            title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                                <a class="page-link" href="<?= base_url('admin/categories?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>">
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
                                <a class="page-link" href="<?= base_url('admin/categories?page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('admin/categories?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-tags fa-3x mb-3"></i>
                <p>Không có danh mục nào</p>
                <a href="<?= base_url('admin/categories/add') ?>" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle"></i> Thêm danh mục đầu tiên
                </a>
            </div>
        <?php endif; ?>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(categoryId, categoryName) {
    if (confirm('Bạn có chắc chắn muốn xóa danh mục "' + categoryName + '"?\n\nLưu ý: Các sản phẩm thuộc danh mục này sẽ không bị xóa!')) {
        fetch('<?= base_url('admin/categories/delete/') ?>' + categoryId, {
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
            alert('Có lỗi xảy ra khi xóa danh mục');
        });
    }
}
</script>

</body>
</html>

