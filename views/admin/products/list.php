<?php
/**
 * =====================================================
 * ADMIN PRODUCTS LIST - Danh sách sản phẩm
 * =====================================================
 * File: views/admin/products/list.php
 * Mô tả: Quản lý danh sách sản phẩm cho admin
 * =====================================================
 */

$products = $products ?? [];
$categories = $categories ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalProducts = $totalProducts ?? 0;
$search = $search ?? '';
$selectedCategory = $selectedCategory ?? null;
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Quản lý sản phẩm</h1>
                <p class="text-muted">Tổng số: <?= $totalProducts ?> sản phẩm</p>
            </div>
            <a href="<?= base_url('admin/products/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm sản phẩm mới
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
                <?= $_SESSION['flash_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php 
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            ?>
        <?php endif; ?>

        <!-- Filters & Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?= base_url('admin/products') ?>" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Tìm theo tên sản phẩm..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Danh mục</label>
                        <select name="category" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                        <a href="<?= base_url('admin/products') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-body">
                <?php if (!empty($products)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">ID</th>
                                    <th width="80">Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th width="120">Giá bán</th>
                                    <th width="100" class="text-center">Tồn kho</th>
                                    <th width="100" class="text-center">Đã bán</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="180" class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= $product['id'] ?></td>
                                        <td>
                                            <img src="<?= base_url($product['image'] ?? 'public/images/placeholder.png') ?>" 
                                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                                 class="img-thumbnail"
                                                 style="width: 60px; height: 60px; object-fit: cover;"
                                                 onerror="this.src='<?= base_url('public/images/placeholder.png') ?>'">
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                                            <?php if (strlen($product['description'] ?? '') > 0): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($product['category_name'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">
                                                <?= number_format($product['price'], 0, ',', '.') ?>đ
                                            </strong>
                                            <?php if (isset($product['cost_price']) && $product['cost_price'] > 0): ?>
                                                <br>
                                                <small class="text-muted">
                                                    Vốn: <?= number_format($product['cost_price'], 0, ',', '.') ?>đ
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $stock = $product['stock'] ?? 0;
                                            $stockClass = $stock > 10 ? 'success' : ($stock > 0 ? 'warning' : 'danger');
                                            ?>
                                            <span class="badge bg-<?= $stockClass ?>">
                                                <?= $stock ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">
                                                <?= $product['sold'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($product['status'] === 'active'): ?>
                                                <span class="badge bg-success">Kích hoạt</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Ẩn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('products/detail/' . $product['id']) ?>" 
                                                   class="btn btn-info"
                                                   title="Xem"
                                                   target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/products/edit/' . $product['id']) ?>" 
                                                   class="btn btn-warning"
                                                   title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger"
                                                        title="Xóa"
                                                        onclick="confirmDelete(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>')">
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
                        <nav aria-label="Products pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>">
                                        <i class="bi bi-chevron-left"></i> Trước
                                    </a>
                                </li>

                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                for ($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>">
                                        Sau <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3">Không tìm thấy sản phẩm nào</h4>
                        <p class="text-muted">Chưa có sản phẩm hoặc không có kết quả phù hợp với bộ lọc</p>
                        <a href="<?= base_url('admin/products/create') ?>" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Thêm sản phẩm đầu tiên
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
                <p>Bạn có chắc chắn muốn xóa sản phẩm <strong id="productName"></strong>?</p>
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
function confirmDelete(productId, productName) {
    document.getElementById('productName').textContent = productName;
    document.getElementById('deleteForm').action = '<?= base_url('admin/products/delete/') ?>' + productId;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

