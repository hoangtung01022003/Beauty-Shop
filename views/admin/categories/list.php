<?php
/**
 * =====================================================
 * ADMIN CATEGORIES LIST - Danh sách danh mục
 * =====================================================
 * File: views/admin/categories/list.php
 * Mô tả: Quản lý danh sách danh mục cho admin
 * =====================================================
 */

$categories = $categories ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalCategories = $totalCategories ?? 0;
$search = $search ?? '';
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Quản lý danh mục</h1>
                <p class="text-muted">Tổng số: <?= $totalCategories ?> danh mục</p>
            </div>
            <a href="<?= base_url('admin/categories/add') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm danh mục mới
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?= base_url('admin/categories') ?>" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Tìm theo tên danh mục..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                        <a href="<?= base_url('admin/categories') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="card">
            <div class="card-body">
                <?php if (!empty($categories)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">ID</th>
                                    <th width="100">Hình ảnh</th>
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
                                        <td>
                                            <?php if (!empty($category['image'])): ?>
                                                <img src="<?= base_url($category['image']) ?>" 
                                                     alt="<?= htmlspecialchars($category['name']) ?>"
                                                     class="img-thumbnail"
                                                     style="width: 80px; height: 80px; object-fit: cover;"
                                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+DQogICAgPHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2Y4ZjlmYSIvPg0KICAgIDx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM2Yzc1N2QiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPk5vIEltYWdlPC90ZXh0Pg0KPC9zdmc+'">
                                            <?php else: ?>
                                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+DQogICAgPHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2Y4ZjlmYSIvPg0KICAgIDx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM2Yzc1N2QiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPk5vIEltYWdlPC90ZXh0Pg0KPC9zdmc+" 
                                                     class="img-thumbnail" 
                                                     style="width: 80px; height: 80px;">
                                            <?php endif; ?>
                                        </td>
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
                                                <small class="text-muted">Chưa có mô tả</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $productCount = $category['product_count'] ?? 0;
                                            ?>
                                            <span class="badge bg-<?= $productCount > 0 ? 'success' : 'secondary' ?> fs-6">
                                                <?= $productCount ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($category['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('products?category=' . $category['id']) ?>" 
                                                   class="btn btn-info"
                                                   title="Xem sản phẩm"
                                                   target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/categories/edit/' . $category['id']) ?>" 
                                                   class="btn btn-warning"
                                                   title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger"
                                                        title="Xóa"
                                                        onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>', <?= $productCount ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Categories pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                        <i class="bi bi-chevron-left"></i> Trước
                                    </a>
                                </li>

                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                for ($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                        Sau <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-folder display-1 text-muted"></i>
                        <h4 class="mt-3">Chưa có danh mục nào</h4>
                        <p class="text-muted">Hãy thêm danh mục đầu tiên để bắt đầu phân loại sản phẩm</p>
                        <a href="<?= base_url('admin/categories/add') ?>" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Thêm danh mục đầu tiên
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa danh mục <strong id="categoryName"></strong>?</p>
                <p id="productWarning" class="text-warning" style="display: none;">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Danh mục này có <strong id="productCount"></strong> sản phẩm!
                </p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(categoryId, categoryName, productCount) {
    document.getElementById('categoryName').textContent = categoryName;
    document.getElementById('deleteForm').action = '<?= base_url('admin/categories/delete/') ?>' + categoryId;
    
    // Hiện cảnh báo nếu có sản phẩm
    if (productCount > 0) {
        document.getElementById('productCount').textContent = productCount;
        document.getElementById('productWarning').style.display = 'block';
    } else {
        document.getElementById('productWarning').style.display = 'none';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

