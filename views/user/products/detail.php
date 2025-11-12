<?php
/**
 * =====================================================
 * PRODUCT DETAIL PAGE - Chi tiết sản phẩm
 * =====================================================
 * File: views/user/products/detail.php
 * Mô tả: Trang chi tiết sản phẩm với gallery, thông tin, form mua hàng
 * =====================================================
 */

// Lấy dữ liệu từ controller
$product = $product ?? null;
$relatedProducts = $relatedProducts ?? [];

if (!$product) {
    header('Location: ' . base_url('products'));
    exit;
}

$productId = $product['id'];
$productName = htmlspecialchars($product['name']);
$productPrice = number_format($product['price'], 0, ',', '.');
$productDescription = $product['description'] ?? '';
$productImage = $product['image'] ?? 'public/images/placeholder.png';
$productStock = $product['stock'] ?? 0;
$productSold = $product['sold'] ?? 0;
$productRating = $product['rating'] ?? 0;
$categoryName = $product['category_name'] ?? 'N/A';
$inStock = $productStock > 0;
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('products') ?>">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $productName ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-5 mb-4">
            <div class="product-image-gallery">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <img id="mainImage" src="<?= base_url($productImage) ?>" class="img-fluid rounded shadow"
                        alt="<?= $productName ?>"
                        onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">

                    <!-- Zoom Icon -->
                    <button class="btn btn-light btn-zoom" data-bs-toggle="modal" data-bs-target="#imageModal">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                </div>

                <!-- Thumbnail Gallery -->
                <div class="thumbnail-gallery">
                    <div class="row g-2">
                        <div class="col-3">
                            <img src="<?= base_url($productImage) ?>" class="img-thumbnail thumbnail-item active"
                                alt="Thumbnail 1" onclick="changeMainImage(this)"
                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
                        </div>
                        <?php
                        // Giả lập thêm 3 ảnh thumbnail (có thể lấy từ gallery field)
                        for ($i = 2; $i <= 4; $i++):
                            ?>
                            <div class="col-3">
                                <img src="<?= base_url($productImage) ?>" class="img-thumbnail thumbnail-item"
                                    alt="Thumbnail <?= $i ?>" onclick="changeMainImage(this)"
                                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="product-info">
                <!-- Product Name -->
                <h1 class="product-title mb-3"><?= $productName ?></h1>

                <!-- Rating & Sold -->
                <div class="product-meta mb-3">
                    <div class="d-flex align-items-center gap-4">
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= floor($productRating)): ?>
                                    <i class="bi bi-star-fill text-warning"></i>
                                <?php elseif ($i - 0.5 <= $productRating): ?>
                                    <i class="bi bi-star-half text-warning"></i>
                                <?php else: ?>
                                    <i class="bi bi-star text-warning"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <span class="ms-2"><?= number_format($productRating, 1) ?></span>
                        </div>
                        <div class="sold text-muted">
                            <i class="bi bi-bag-check"></i> <?= $productSold ?> đã bán
                        </div>
                        <div class="category text-muted">
                            <i class="bi bi-tag"></i> <?= $categoryName ?>
                        </div>
                    </div>
                </div>

                <!-- Price -->
                <div class="product-price mb-4">
                    <h2 class="text-primary fw-bold mb-0"><?= $productPrice ?>đ</h2>
                </div>

                <!-- Stock Status -->
                <div class="stock-status mb-4">
                    <?php if ($inStock): ?>
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle"></i> Còn hàng: <?= $productStock ?> sản phẩm
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger fs-6">
                            <i class="bi bi-x-circle"></i> Hết hàng
                        </span>
                    <?php endif; ?>
                </div>

                <hr>

                <!-- Add to Cart Form -->
                <?php if ($inStock): ?>
                    <form id="addToCartForm" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Số lượng:</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" id="quantity" name="quantity" class="form-control text-center"
                                        value="1" min="1" max="<?= $productStock ?>" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Quick Actions -->
                    <div class="d-flex gap-2 mb-4">
                        <a href="<?= base_url('checkout?product_id=' . $productId) ?>" class="btn btn-success flex-fill">
                            <i class="bi bi-lightning"></i> Mua ngay
                        </a>
                        <button class="btn btn-outline-danger" onclick="addToWishlist(<?= $productId ?>)">
                            <i class="bi bi-heart"></i> Yêu thích
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Sản phẩm hiện đang hết hàng. Vui lòng liên hệ để được tư
                        vấn sản phẩm tương tự.
                    </div>
                <?php endif; ?>

                <!-- Product Features -->
                <div class="product-features">
                    <h5 class="fw-bold mb-3">Đặc điểm nổi bật:</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> Sản phẩm chính hãng 100%</li>
                        <li><i class="bi bi-check-circle text-success"></i> Bảo hành đổi trả trong 7 ngày</li>
                        <li><i class="bi bi-check-circle text-success"></i> Miễn phí vận chuyển cho đơn hàng từ 500.000đ
                        </li>
                        <li><i class="bi bi-check-circle text-success"></i> Tích điểm thành viên</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">
                        <i class="bi bi-info-circle"></i> Mô tả sản phẩm
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#specifications">
                        <i class="bi bi-list-ul"></i> Thông số kỹ thuật
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">
                        <i class="bi bi-star"></i> Đánh giá (0)
                    </button>
                </li>
            </ul>

            <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <h4 class="mb-3">Mô tả chi tiết</h4>
                    <div class="product-description">
                        <?= nl2br(htmlspecialchars($productDescription)) ?>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specifications" role="tabpanel">
                    <h4 class="mb-3">Thông số kỹ thuật</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="30%">Mã sản phẩm</th>
                                <td>SP<?= str_pad($productId, 6, '0', STR_PAD_LEFT) ?></td>
                            </tr>
                            <tr>
                                <th>Danh mục</th>
                                <td><?= $categoryName ?></td>
                            </tr>
                            <tr>
                                <th>Tình trạng</th>
                                <td><?= $inStock ? 'Còn hàng' : 'Hết hàng' ?></td>
                            </tr>
                            <tr>
                                <th>Xuất xứ</th>
                                <td>Hàn Quốc / Nhật Bản / Pháp</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <h4 class="mb-3">Đánh giá từ khách hàng</h4>
                    <div class="text-center py-5">
                        <i class="bi bi-chat-square-text display-1 text-muted"></i>
                        <p class="text-muted mt-3">Chưa có đánh giá nào cho sản phẩm này</p>
                        <button class="btn btn-primary">Viết đánh giá đầu tiên</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="fw-bold mb-4">Sản phẩm liên quan</h3>
                <div class="row g-4">
                    <?php foreach (array_slice($relatedProducts, 0, 4) as $product): ?>
                        <div class="col-6 col-md-3">
                            <?php include __DIR__ . '/../../components/product-card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Image Zoom Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $productName ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= base_url($productImage) ?>" class="img-fluid" alt="<?= $productName ?>"
                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAwIiBoZWlnaHQ9IjgwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIzNiIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
            </div>
        </div>
    </div>
</div>

<style>
    .product-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .main-image {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
    }

    .main-image img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .btn-zoom {
        position: absolute;
        top: 10px;
        right: 10px;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        padding: 0;
    }

    .thumbnail-item {
        cursor: pointer;
        transition: all 0.3s ease;
        opacity: 0.6;
    }

    .thumbnail-item:hover,
    .thumbnail-item.active {
        opacity: 1;
        border-color: #0d6efd;
    }

    .product-price h2 {
        font-size: 2.5rem;
    }

    .product-features ul li {
        padding: 8px 0;
    }

    .product-features ul li i {
        margin-right: 10px;
    }
</style>

<script>
    // Change main image when clicking thumbnail
    function changeMainImage(thumbnail) {
        const mainImage = document.getElementById('mainImage');
        mainImage.src = thumbnail.src;

        // Update active thumbnail
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('active');
        });
        thumbnail.classList.add('active');
    }

    // Quantity controls
    function increaseQuantity() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }

    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }

    // Add to cart form submission
    document.getElementById('addToCartForm')?.addEventListener('submit', function (e) {
        e.preventDefault();

        const quantity = document.getElementById('quantity').value;
        const productId = <?= $productId ?>;
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        // Disable button và hiển thị loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';

        // Gửi AJAX request
        fetch('<?= base_url('cart/add') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `product_id=${productId}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị thông báo thành công
                showAlert('success', data.message || 'Đã thêm sản phẩm vào giỏ hàng!');
                
                // Cập nhật số lượng giỏ hàng trên header
                updateCartCount(data.cart_count || 0);
                
                // Reset quantity về 1
                document.getElementById('quantity').value = 1;
            } else {
                // Hiển thị lỗi
                showAlert('danger', data.message || 'Không thể thêm vào giỏ hàng');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Có lỗi xảy ra khi thêm vào giỏ hàng');
        })
        .finally(() => {
            // Enable lại button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });

    // Hàm hiển thị alert
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Tìm container để hiển thị alert (trên form)
        const form = document.getElementById('addToCartForm');
        const existingAlert = form.previousElementSibling;
        
        if (existingAlert && existingAlert.classList.contains('alert')) {
            existingAlert.remove();
        }
        
        form.insertAdjacentHTML('beforebegin', alertHtml);
        
        // Auto remove sau 3 giây
        setTimeout(() => {
            const alert = form.previousElementSibling;
            if (alert && alert.classList.contains('alert')) {
                alert.remove();
            }
        }, 3000);
    }

    // Hàm cập nhật số lượng giỏ hàng
    function updateCartCount(count) {
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            cartBadge.textContent = count;
            
            // Animation nhấp nháy
            cartBadge.classList.add('animate-pulse');
            setTimeout(() => {
                cartBadge.classList.remove('animate-pulse');
            }, 500);
        }
    }

    // Add to wishlist
    function addToWishlist(productId) {
        // TODO: Implement wishlist functionality
        alert('Đã thêm vào danh sách yêu thích!');
    }
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>