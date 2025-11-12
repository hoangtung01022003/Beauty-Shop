<?php
/**
 * =====================================================
 * CART VIEW - Trang giỏ hàng
 * =====================================================
 * File: views/user/cart/view.php
 * Mô tả: Giao diện giỏ hàng với AJAX
 * =====================================================
 */

$items = $items ?? [];
$cartSummary = $cartSummary ?? ['count' => 0, 'total' => 0, 'is_empty' => true];
$validationErrors = $validationErrors ?? [];
$pageTitle = $pageTitle ?? 'Giỏ hàng';

// Phí vận chuyển (có thể config)
$shippingFee = 30000; // 30.000đ
$freeShippingThreshold = 500000; // Miễn phí ship từ 500k

// Tính phí ship
$actualShippingFee = $cartSummary['total'] >= $freeShippingThreshold ? 0 : $shippingFee;
$finalTotal = $cartSummary['total'] + $actualShippingFee;
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-cart3"></i> Giỏ hàng của bạn
        </h1>
        <span class="badge bg-primary fs-6">
            <?= $cartSummary['count'] ?> sản phẩm
        </span>
    </div>

    <!-- Validation Errors -->
    <?php if (!empty($validationErrors)): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <strong>Cảnh báo:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($validationErrors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Alert Container for AJAX messages -->
    <div id="cart-alert-container"></div>

    <?php if ($cartSummary['is_empty']): ?>
        <!-- Empty Cart -->
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                <h3>Giỏ hàng trống</h3>
                <p class="text-muted mb-4">Bạn chưa có sản phẩm nào trong giỏ hàng.</p>
                <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-bag"></i> Tiếp tục mua sắm
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Cart Content -->
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="100">Hình ảnh</th>
                                        <th>Sản phẩm</th>
                                        <th width="120">Giá</th>
                                        <th width="150">Số lượng</th>
                                        <th width="120">Tổng</th>
                                        <th width="60"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <?php foreach ($items as $productId => $item): ?>
                                        <tr id="cart-item-<?= $productId ?>" data-product-id="<?= $productId ?>">
                                            <!-- Hình ảnh -->
                                            <td>
                                                <img src="<?= base_url($item['product_image'] ?? $item['image'] ?? 'public/images/placeholder.png') ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name'] ?? $item['name']) ?>"
                                                     class="img-thumbnail"
                                                     style="width: 80px; height: 80px; object-fit: cover;"
                                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2Y4ZjlmYSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM2Yzc1N2QiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                                            </td>
                                            
                                            <!-- Thông tin sản phẩm -->
                                            <td>
                                                <a href="<?= base_url('products/detail/' . $productId) ?>" class="text-decoration-none text-dark">
                                                    <strong><?= htmlspecialchars($item['product_name'] ?? $item['name']) ?></strong>
                                                </a>
                                                <?php if (isset($item['category_name'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($item['category_name']) ?></small>
                                                <?php endif; ?>
                                                
                                                <!-- Stock warning -->
                                                <?php if (isset($item['product_stock']) && $item['product_stock'] < $item['quantity']): ?>
                                                    <br><small class="text-danger">
                                                        <i class="bi bi-exclamation-triangle"></i> 
                                                        Chỉ còn <?= $item['product_stock'] ?> sản phẩm
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <!-- Giá -->
                                            <td>
                                                <strong class="text-primary item-price" data-price="<?= $item['price'] ?>">
                                                    <?= number_format($item['price'], 0, ',', '.') ?>đ
                                                </strong>
                                            </td>
                                            
                                            <!-- Số lượng -->
                                            <td>
                                                <div class="input-group input-group-sm" style="max-width: 130px;">
                                                    <button class="btn btn-outline-secondary btn-decrease" type="button">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="form-control text-center quantity-input" 
                                                           value="<?= $item['quantity'] ?>" 
                                                           min="1" 
                                                           max="<?= $item['product_stock'] ?? 999 ?>"
                                                           data-product-id="<?= $productId ?>">
                                                    <button class="btn btn-outline-secondary btn-increase" type="button">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            
                                            <!-- Tổng -->
                                            <td>
                                                <strong class="item-subtotal">
                                                    <?= number_format($item['subtotal'], 0, ',', '.') ?>đ
                                                </strong>
                                            </td>
                                            
                                            <!-- Xóa -->
                                            <td>
                                                <button class="btn btn-sm btn-danger btn-remove" 
                                                        data-product-id="<?= $productId ?>"
                                                        title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('products') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                    <button type="button" class="btn btn-outline-danger" id="btn-clear-cart">
                        <i class="bi bi-trash"></i> Xóa toàn bộ giỏ hàng
                    </button>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tạm tính -->
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <strong id="cart-subtotal">
                                <?= number_format($cartSummary['total'], 0, ',', '.') ?>đ
                            </strong>
                        </div>

                        <!-- Phí vận chuyển -->
                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                Phí vận chuyển:
                                <?php if ($cartSummary['total'] >= $freeShippingThreshold): ?>
                                    <br><small class="text-success">Miễn phí!</small>
                                <?php endif; ?>
                            </span>
                            <strong id="shipping-fee">
                                <?php if ($actualShippingFee > 0): ?>
                                    <?= number_format($actualShippingFee, 0, ',', '.') ?>đ
                                <?php else: ?>
                                    <span class="text-success">Miễn phí</span>
                                <?php endif; ?>
                            </strong>
                        </div>

                        <!-- Free shipping progress -->
                        <?php if ($cartSummary['total'] < $freeShippingThreshold): ?>
                            <?php
                            $remaining = $freeShippingThreshold - $cartSummary['total'];
                            $progress = ($cartSummary['total'] / $freeShippingThreshold) * 100;
                            ?>
                            <div class="mb-3">
                                <small class="text-muted">
                                    Mua thêm <strong class="text-primary"><?= number_format($remaining, 0, ',', '.') ?>đ</strong> 
                                    để được miễn phí vận chuyển
                                </small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" 
                                         role="progressbar" 
                                         style="width: <?= $progress ?>%"></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <!-- Tổng cộng -->
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">Tổng cộng:</h5>
                            <h5 class="mb-0 text-danger" id="cart-total">
                                <?= number_format($finalTotal, 0, ',', '.') ?>đ
                            </h5>
                        </div>

                        <!-- Checkout Button -->
                        <a href="<?= base_url('checkout') ?>" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="bi bi-credit-card"></i> Thanh toán
                        </a>

                        <!-- Coupon (Optional) -->
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" placeholder="Mã giảm giá">
                            <button class="btn btn-outline-secondary" type="button">Áp dụng</button>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="card-footer text-center text-muted">
                        <small>
                            <i class="bi bi-shield-check"></i> Thanh toán an toàn & bảo mật
                        </small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">Xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for AJAX Cart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingFee = <?= $shippingFee ?>;
    const freeShippingThreshold = <?= $freeShippingThreshold ?>;
    let deleteProductId = null;

    // Helper: Show alert
    function showAlert(message, type = 'success') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.getElementById('cart-alert-container').innerHTML = alertHtml;
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            const alert = document.querySelector('#cart-alert-container .alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 3000);
    }

    // Helper: Update cart summary
    function updateCartSummary(cartTotal, cartCount) {
        document.getElementById('cart-subtotal').textContent = formatPrice(cartTotal);
        
        // Calculate shipping
        const actualShipping = cartTotal >= freeShippingThreshold ? 0 : shippingFee;
        const finalTotal = cartTotal + actualShipping;
        
        if (actualShipping > 0) {
            document.getElementById('shipping-fee').textContent = formatPrice(actualShipping);
        } else {
            document.getElementById('shipping-fee').innerHTML = '<span class="text-success">Miễn phí</span>';
        }
        
        document.getElementById('cart-total').textContent = formatPrice(finalTotal);
        
        // Update badge in header
        const cartBadge = document.querySelector('.navbar .badge');
        if (cartBadge) {
            cartBadge.textContent = cartCount;
        }
    }

    // Helper: Format price
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price).replace('₫', 'đ');
    }

    // Update quantity (AJAX)
    function updateQuantity(productId, quantity) {
        fetch('<?= base_url('cart/update') ?>', {
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
                // Update item subtotal
                const row = document.getElementById(`cart-item-${productId}`);
                if (data.is_empty) {
                    // Cart is empty, reload page
                    location.reload();
                } else {
                    row.querySelector('.item-subtotal').textContent = data.item_subtotal_formatted;
                    updateCartSummary(data.cart_total, data.cart_count);
                    showAlert(data.message, 'success');
                }
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Có lỗi xảy ra khi cập nhật giỏ hàng', 'danger');
        });
    }

    // Decrease quantity
    document.querySelectorAll('.btn-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const input = row.querySelector('.quantity-input');
            const productId = input.dataset.productId;
            let quantity = parseInt(input.value);
            
            if (quantity > 1) {
                quantity--;
                input.value = quantity;
                updateQuantity(productId, quantity);
            }
        });
    });

    // Increase quantity
    document.querySelectorAll('.btn-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const input = row.querySelector('.quantity-input');
            const productId = input.dataset.productId;
            const max = parseInt(input.max);
            let quantity = parseInt(input.value);
            
            if (quantity < max) {
                quantity++;
                input.value = quantity;
                updateQuantity(productId, quantity);
            } else {
                showAlert('Đã đạt số lượng tối đa trong kho', 'warning');
            }
        });
    });

    // Manual input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            let quantity = parseInt(this.value);
            const max = parseInt(this.max);
            
            if (quantity < 1) {
                quantity = 1;
                this.value = 1;
            } else if (quantity > max) {
                quantity = max;
                this.value = max;
                showAlert('Đã đạt số lượng tối đa trong kho', 'warning');
            }
            
            updateQuantity(productId, quantity);
        });
    });

    // Remove item
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteProductId = this.dataset.productId;
            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        });
    });

    // Confirm delete
    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (deleteProductId) {
            fetch('<?= base_url('cart/remove') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `product_id=${deleteProductId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove row
                    const row = document.getElementById(`cart-item-${deleteProductId}`);
                    row.remove();
                    
                    if (data.is_empty) {
                        // Reload page to show empty cart
                        location.reload();
                    } else {
                        updateCartSummary(data.cart_total, data.cart_count);
                        showAlert(data.message, 'success');
                    }
                } else {
                    showAlert(data.message, 'danger');
                }
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                modal.hide();
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Có lỗi xảy ra', 'danger');
            });
        }
    });

    // Clear cart
    document.getElementById('btn-clear-cart')?.addEventListener('click', function() {
        if (confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?')) {
            fetch('<?= base_url('cart/clear') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Có lỗi xảy ra', 'danger');
            });
        }
    });
});
</script>

<style>
.sticky-top {
    position: sticky;
}

.quantity-input {
    -moz-appearance: textfield;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.btn-decrease, .btn-increase {
    padding: 0.25rem 0.5rem;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.5rem;
}

.card.sticky-top {
    z-index: 1020;
}

@media (max-width: 991px) {
    .card.sticky-top {
        position: relative !important;
        top: 0 !important;
    }
}
</style>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

