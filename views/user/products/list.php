<?php
/**
 * =====================================================
 * PRODUCTS LIST PAGE - Danh sách sản phẩm
 * =====================================================
 * File: views/user/products/list.php
 * Mô tả: Trang danh sách sản phẩm với filter và phân trang
 * =====================================================
 */

// Lấy dữ liệu từ controller
$products = $products ?? [];
$categories = $categories ?? [];
$totalProducts = $totalProducts ?? 0;
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$selectedCategory = $selectedCategory ?? null;
$keyword = $keyword ?? '';
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <!-- Search Box -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Tìm kiếm</h6>
                        <form action="<?= base_url('products/search') ?>" method="GET">
                            <div class="input-group">
                                <input type="text" 
                                       name="keyword" 
                                       class="form-control" 
                                       placeholder="Tìm sản phẩm..."
                                       value="<?= htmlspecialchars($keyword) ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <!-- Category Filter -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Danh mục</h6>
                        <div class="list-group">
                            <a href="<?= base_url('products') ?>" 
                               class="list-group-item list-group-item-action <?= !$selectedCategory ? 'active' : '' ?>">
                                <i class="bi bi-grid"></i> Tất cả sản phẩm
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="<?= base_url('products?category=' . $category['id']) ?>" 
                                   class="list-group-item list-group-item-action <?= $selectedCategory == $category['id'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                    <span class="badge bg-secondary float-end">
                                        <?= $category['product_count'] ?? 0 ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- Price Range Filter -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Khoảng giá</h6>
                        <form action="<?= base_url('products') ?>" method="GET">
                            <?php if ($selectedCategory): ?>
                                <input type="hidden" name="category" value="<?= $selectedCategory ?>">
                            <?php endif; ?>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_range" value="" id="price_all" checked>
                                <label class="form-check-label" for="price_all">Tất cả</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_range" value="0-200000" id="price1">
                                <label class="form-check-label" for="price1">Dưới 200.000đ</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_range" value="200000-500000" id="price2">
                                <label class="form-check-label" for="price2">200.000đ - 500.000đ</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_range" value="500000-1000000" id="price3">
                                <label class="form-check-label" for="price3">500.000đ - 1.000.000đ</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_range" value="1000000-999999999" id="price4">
                                <label class="form-check-label" for="price4">Trên 1.000.000đ</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-sm mt-3 w-100">
                                <i class="bi bi-check2"></i> Áp dụng
                            </button>
                        </form>
                    </div>

                    <hr>

                    <!-- Reset Filter -->
                    <div class="d-grid">
                        <a href="<?= base_url('products') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Products Area -->
        <div class="col-lg-9">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-2">
                        <?php if ($keyword): ?>
                            Kết quả tìm kiếm: "<?= htmlspecialchars($keyword) ?>"
                        <?php elseif ($selectedCategory): ?>
                            <?php 
                            $catName = '';
                            foreach ($categories as $cat) {
                                if ($cat['id'] == $selectedCategory) {
                                    $catName = $cat['name'];
                                    break;
                                }
                            }
                            ?>
                            <?= htmlspecialchars($catName) ?>
                        <?php else: ?>
                            Tất cả sản phẩm
                        <?php endif; ?>
                    </h2>
                    <p class="text-muted mb-0">
                        Tìm thấy <?= $totalProducts ?> sản phẩm
                    </p>
                </div>

                <!-- Sort Options -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-sort-down"></i> Sắp xếp
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?sort=latest">Mới nhất</a></li>
                        <li><a class="dropdown-item" href="?sort=price_asc">Giá: Thấp đến cao</a></li>
                        <li><a class="dropdown-item" href="?sort=price_desc">Giá: Cao đến thấp</a></li>
                        <li><a class="dropdown-item" href="?sort=best_selling">Bán chạy nhất</a></li>
                    </ul>
                </div>
            </div>

            <!-- Products Grid -->
            <?php if (!empty($products)): ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-6 col-md-4">
                            <?php include __DIR__ . '/../../components/product-card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Product pagination" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>">
                                    <i class="bi bi-chevron-left"></i> Trước
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            if ($startPage > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=1' . ($selectedCategory ? '&category=' . $selectedCategory : '') . '">1</a></li>';
                                if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php 
                            endfor;
                            
                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . ($selectedCategory ? '&category=' . $selectedCategory : '') . '">' . $totalPages . '</a></li>';
                            }
                            ?>

                            <!-- Next -->
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>">
                                    Sau <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Products Found -->
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h3 class="mt-4">Không tìm thấy sản phẩm nào</h3>
                    <p class="text-muted">Vui lòng thử lại với bộ lọc khác hoặc từ khóa khác</p>
                    <a href="<?= base_url('products') ?>" class="btn btn-primary mt-3">
                        <i class="bi bi-arrow-left"></i> Xem tất cả sản phẩm
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Responsive adjustments */
@media (max-width: 768px) {
    .col-6 {
        /* Mobile: 1 column */
    }
}

@media (min-width: 768px) and (max-width: 992px) {
    .col-md-4 {
        /* Tablet: 2 columns */
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 992px) {
    .col-md-4 {
        /* Desktop: 3 columns */
    }
}

.list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.pagination .page-link {
    color: #0d6efd;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

