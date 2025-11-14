<?php
/**
 * =====================================================
 * USER HOME PAGE - Trang chủ
 * =====================================================
 * File: views/user/home.php
 * Mô tả: Trang chủ website bán mỹ phẩm
 * =====================================================
 */

// Lấy dữ liệu từ controller
$categories = $categories ?? [];
$latestProducts = $latestProducts ?? [];
$bestSellingProducts = $bestSellingProducts ?? [];
?>

<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Mỹ Phẩm Chính Hãng</h1>
                <p class="lead mb-4">Khám phá bộ sưu tập mỹ phẩm cao cấp từ những thương hiệu hàng đầu thế giới. Chất
                    lượng đảm bảo, giá cả hợp lý.</p>
                <div class="d-flex gap-3">
                    <a href="<?= base_url('products') ?>" class="btn btn-light btn-lg">
                        <i class="bi bi-shop"></i> Mua sắm ngay
                    </a>
                    <a href="#featured-categories" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-grid"></i> Danh mục
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?= base_url('banner.jpg') ?>" alt="Hero Banner" class="img-fluid rounded"
                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
            </div>
        </div>
    </div>
</section>

<!-- Search Bar Section -->
<section class="search-section py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="<?= base_url('products/search') ?>" method="GET" class="search-form">
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" name="keyword" class="form-control"
                            placeholder="Tìm kiếm sản phẩm: Son môi, kem dưỡng da, nước hoa..." required>
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories Section -->
<section id="featured-categories" class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Danh Mục Nổi Bật</h2>
            <p class="text-muted">Khám phá các danh mục sản phẩm phổ biến</p>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?= base_url('products?category=' . $category['id']) ?>"
                        class="category-card text-decoration-none">
                        <div class="card h-100 text-center shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="category-icon mb-3">
                                    <?php
                                    // Icon mapping cho các danh mục
                                    $icons = [
                                        'Chăm Sóc Da Mặt' => 'bi-moisture',
                                        'Trang Điểm' => 'bi-palette',
                                        'Chăm Sóc Cơ Thể' => 'bi-droplet',
                                        'Chăm Sóc Tóc' => 'bi-scissors',
                                        'Nước Hoa' => 'bi-flower1',
                                        'Mặt Nạ' => 'bi-stars'
                                    ];
                                    $iconClass = $icons[$category['name']] ?? 'bi-bag';
                                    ?>
                                    <i class="bi <?= $iconClass ?> fs-1 text-primary"></i>
                                </div>
                                <h6 class="card-title mb-0"><?= htmlspecialchars($category['name']) ?></h6>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="latest-products py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Sản Phẩm Mới Nhất</h2>
            <p class="text-muted">Cập nhật những sản phẩm mới nhất từ các thương hiệu uy tín</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($latestProducts)): ?>
                <?php foreach (array_slice($latestProducts, 0, 8) as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <?php include __DIR__ . '/../components/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted">Chưa có sản phẩm nào</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-grid"></i> Xem tất cả sản phẩm
            </a>
        </div>
    </div>
</section>

<!-- Best Selling Products Section -->
<section class="best-selling py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Sản Phẩm Bán Chạy</h2>
            <p class="text-muted">Những sản phẩm được yêu thích nhất</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($bestSellingProducts)): ?>
                <?php foreach (array_slice($bestSellingProducts, 0, 8) as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <?php include __DIR__ . '/../components/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted">Chưa có dữ liệu</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Blog/Tips Section -->
<section class="blog-tips py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Bí Quyết Làm Đẹp</h2>
            <p class="text-muted">Chia sẻ những mẹo hay về chăm sóc da và làm đẹp</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card blog-card h-100 shadow-sm">
                    <img src="<?= base_url('public/images/blog1.jpg') ?>" class="card-img-top" alt="Blog 1"
                        onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
                    <div class="card-body">
                        <h5 class="card-title">10 Bước Chăm Sóc Da Ban Đêm</h5>
                        <p class="card-text text-muted">Khám phá quy trình chăm sóc da ban đêm hiệu quả giúp da luôn
                            khỏe đẹp...</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">Đọc thêm</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card blog-card h-100 shadow-sm">
                    <img src="<?= base_url('public/images/blog2.jpg') ?>" class="card-img-top" alt="Blog 2"
                        onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
                    <div class="card-body">
                        <h5 class="card-title">Cách Chọn Son Phù Hợp Với Tông Da</h5>
                        <p class="card-text text-muted">Hướng dẫn chi tiết cách lựa chọn màu son môi phù hợp với từng
                            tone da...</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">Đọc thêm</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card blog-card h-100 shadow-sm">
                    <img src="<?= base_url('public/images/blog3.jpg') ?>" class="card-img-top" alt="Blog 3"
                        onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
                    <div class="card-body">
                        <h5 class="card-title">Review Top 5 Kem Chống Nắng 2025</h5>
                        <p class="card-text text-muted">Tổng hợp những sản phẩm kem chống nắng tốt nhất hiện nay...</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">Đọc thêm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg,rgb(77, 84, 114) 0%,rgb(99, 56, 143) 100%);
    }

    .category-card {
        display: block;
        transition: transform 0.3s ease;
    }

    .category-card:hover {
        transform: translateY(-5px);
    }

    .category-card .card {
        transition: box-shadow 0.3s ease;
    }

    .category-card:hover .card {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
    }

    .section-header h2 {
        color: #2c3e50;
        position: relative;
        display: inline-block;
    }

    .section-header h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: #667eea;
    }

    .blog-card {
        transition: transform 0.3s ease;
    }

    .blog-card:hover {
        transform: translateY(-5px);
    }

    .blog-card img {
        height: 200px;
        object-fit: cover;
    }
</style>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>