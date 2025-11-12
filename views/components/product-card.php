<?php
/**
 * =====================================================
 * COMPONENT: PRODUCT CARD
 * =====================================================
 * File: views/components/product-card.php
 * Mô tả: Card hiển thị sản phẩm (tái sử dụng)
 * Tham số: $product (array)
 * =====================================================
 */

// Validate dữ liệu sản phẩm
if (!isset($product) || empty($product)) {
    return;
}

// Xử lý dữ liệu
$productId = $product['id'] ?? 0;
$productName = htmlspecialchars($product['name'] ?? 'Sản phẩm');
$productPrice = number_format($product['price'] ?? 0, 0, ',', '.');
$productImage = $product['image'] ?? 'public/images/placeholder.png';
$productStock = $product['stock'] ?? 0;
$productSold = $product['sold'] ?? 0;
$productRating = $product['rating'] ?? 0;
$productStatus = $product['status'] ?? 'active';

// Kiểm tra còn hàng
$inStock = $productStock > 0;
$stockClass = $inStock ? 'text-success' : 'text-danger';
$stockText = $inStock ? "Còn {$productStock} sản phẩm" : "Hết hàng";

// URL chi tiết
$detailUrl = base_url("products/detail/{$productId}");
?>

<div class="product-card card h-100 shadow-sm">
    <!-- Badge trạng thái -->
    <?php if (!$inStock): ?>
        <div class="position-absolute top-0 end-0 m-2">
            <span class="badge bg-danger">Hết hàng</span>
        </div>
    <?php elseif ($productSold > 100): ?>
        <div class="position-absolute top-0 end-0 m-2">
            <span class="badge bg-warning text-dark">Bán chạy</span>
        </div>
    <?php endif; ?>

    <!-- Hình ảnh sản phẩm -->
    <a href="<?= $detailUrl ?>" class="text-decoration-none">
        <img src="<?= get_image_url($productImage, 'No Image', 250, 250) ?>" 
             class="card-img-top product-card-img" 
             alt="<?= $productName ?>"
             loading="lazy">
    </a>

    <!-- Nội dung card -->
    <div class="card-body d-flex flex-column">
        <!-- Tên sản phẩm -->
        <h5 class="card-title product-card-title">
            <a href="<?= $detailUrl ?>" class="text-dark text-decoration-none">
                <?= $productName ?>
            </a>
        </h5>

        <!-- Đánh giá -->
        <div class="product-rating mb-2">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php if ($i <= floor($productRating)): ?>
                    <i class="bi bi-star-fill text-warning"></i>
                <?php elseif ($i - 0.5 <= $productRating): ?>
                    <i class="bi bi-star-half text-warning"></i>
                <?php else: ?>
                    <i class="bi bi-star text-warning"></i>
                <?php endif; ?>
            <?php endfor; ?>
            <small class="text-muted">(<?= $productSold ?> đã bán)</small>
        </div>

        <!-- Giá -->
        <div class="product-price mb-2">
            <h4 class="text-primary mb-0"><?= $productPrice ?>đ</h4>
        </div>

        <!-- Tình trạng kho -->
        <p class="<?= $stockClass ?> mb-3">
            <i class="bi bi-box-seam"></i> <?= $stockText ?>
        </p>

        <!-- Nút hành động -->
        <div class="mt-auto">
            <div class="d-grid gap-2">
                <a href="<?= $detailUrl ?>" class="btn btn-outline-primary">
                    <i class="bi bi-eye"></i> Xem chi tiết
                </a>
                <?php if ($inStock): ?>
                    <button type="button" 
                            class="btn btn-primary btn-add-to-cart" 
                            data-product-id="<?= $productId ?>"
                            data-product-name="<?= $productName ?>"
                            data-product-price="<?= $product['price'] ?>">
                        <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="bi bi-x-circle"></i> Hết hàng
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e0e0e0;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}

.product-card-img {
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-card-img {
    transform: scale(1.05);
}

.product-card-title {
    font-size: 1rem;
    font-weight: 600;
    min-height: 48px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-card-title a:hover {
    color: #0d6efd !important;
}

.product-rating {
    font-size: 0.9rem;
}

.product-price h4 {
    font-weight: 700;
}
</style>

