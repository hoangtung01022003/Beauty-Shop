<?php
/**
 * =====================================================
 * CHECKOUT VIEW - Trang thanh toán
 * =====================================================
 * File: views/user/checkout/checkout.php
 * Mô tả: Form checkout với 2 cột
 * =====================================================
 */

$user = $user ?? [];
$cartItems = $cartItems ?? [];
$cartSummary = $cartSummary ?? ['count' => 0, 'total' => 0];
$shippingFee = $shippingFee ?? 30000;
$finalTotal = $finalTotal ?? 0;
$pageTitle = $pageTitle ?? 'Thanh toán';
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('cart') ?>">Giỏ hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3">
            <i class="bi bi-credit-card"></i> Thanh toán
        </h1>
        <p class="text-muted">Vui lòng điền đầy đủ thông tin để hoàn tất đơn hàng</p>
    </div>

    <form action="<?= base_url('checkout') ?>" method="POST" id="checkout-form">
        <div class="row">
            <!-- Cột trái: Form thông tin -->
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-circle"></i> Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <!-- Thông tin user (readonly) -->
                        <div class="mb-3">
                            <label class="form-label">Họ tên:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>

                        <?php if (!empty($user['phone'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Địa chỉ giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">
                                Địa chỉ chi tiết <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                name="shipping_address" 
                                id="shipping_address" 
                                class="form-control" 
                                rows="4" 
                                placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"
                                required
                            ><?= !empty($user['address']) ? htmlspecialchars($user['address']) : '' ?></textarea>
                            <div class="invalid-feedback">
                                Vui lòng nhập địa chỉ giao hàng (tối thiểu 10 ký tự)
                            </div>
                            <small class="text-muted">
                                Ví dụ: 123 Nguyễn Văn Linh, Phường Tân Phú, Quận 7, TP. Hồ Chí Minh
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-wallet2"></i> Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                            <label class="form-check-label" for="payment_cod">
                                <i class="bi bi-cash-coin text-success"></i> <strong>Thanh toán khi nhận hàng (COD)</strong>
                                <br><small class="text-muted">Bạn sẽ thanh toán bằng tiền mặt khi nhận hàng</small>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                            <label class="form-check-label" for="payment_bank">
                                <i class="bi bi-bank text-primary"></i> <strong>Chuyển khoản ngân hàng</strong>
                                <br><small class="text-muted">Chuyển khoản trực tiếp vào tài khoản ngân hàng</small>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_momo" value="momo">
                            <label class="form-check-label" for="payment_momo">
                                <i class="bi bi-phone text-danger"></i> <strong>Ví MoMo</strong>
                                <br><small class="text-muted">Thanh toán qua ví điện tử MoMo</small>
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_vnpay" value="vnpay">
                            <label class="form-check-label" for="payment_vnpay">
                                <i class="bi bi-credit-card text-info"></i> <strong>VNPay</strong>
                                <br><small class="text-muted">Thanh toán qua cổng VNPay</small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Ghi chú đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea 
                                name="notes" 
                                id="notes" 
                                class="form-control" 
                                rows="3" 
                                placeholder="Ví dụ: Giao giờ hành chính, gọi trước 15 phút, để hàng ở bảo vệ..."
                            ></textarea>
                            <small class="text-muted">Nhập ghi chú về đơn hàng của bạn (nếu có)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Tóm tắt đơn hàng -->
            <div class="col-lg-5">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <!-- Danh sách sản phẩm -->
                        <div class="mb-3">
                            <h6 class="border-bottom pb-2">Sản phẩm (<?= $cartSummary['count'] ?> mặt hàng)</h6>
                            <div class="order-items" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex mb-3">
                                    <img src="<?= base_url($item['product_image'] ?? $item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['product_name'] ?? $item['name']) ?>"
                                         class="img-thumbnail me-3"
                                         style="width: 60px; height: 60px; object-fit: cover;"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2Y4ZjlmYSIvPjwvc3ZnPg=='">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small"><?= htmlspecialchars($item['product_name'] ?? $item['name']) ?></div>
                                        <div class="text-muted small">
                                            SL: <?= $item['quantity'] ?> × <?= number_format($item['price'], 0, ',', '.') ?>đ
                                        </div>
                                        <div class="text-primary small fw-bold">
                                            <?= number_format($item['subtotal'], 0, ',', '.') ?>đ
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <hr>

                        <!-- Tóm tắt giá -->
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Tạm tính:</span>
                            <strong><?= number_format($cartSummary['total'], 0, ',', '.') ?>đ</strong>
                        </div>

                        <div class="mb-2 d-flex justify-content-between">
                            <span>Phí vận chuyển:</span>
                            <strong>
                                <?php if ($shippingFee > 0): ?>
                                    <?= number_format($shippingFee, 0, ',', '.') ?>đ
                                <?php else: ?>
                                    <span class="text-success">Miễn phí</span>
                                <?php endif; ?>
                            </strong>
                        </div>

                        <hr>

                        <div class="mb-3 d-flex justify-content-between">
                            <h5 class="mb-0">Tổng cộng:</h5>
                            <h5 class="mb-0 text-danger"><?= number_format($finalTotal, 0, ',', '.') ?>đ</h5>
                        </div>

                        <!-- Nút thanh toán -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Đặt hàng
                        </button>

                        <a href="<?= base_url('cart') ?>" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left"></i> Quay lại giỏ hàng
                        </a>

                        <!-- Trust badges -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="bi bi-shield-check text-success"></i> Thanh toán an toàn & bảo mật<br>
                                <i class="bi bi-truck text-primary"></i> Miễn phí đổi trả trong 7 ngày
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const shippingAddress = document.getElementById('shipping_address');

    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Reset validation
        form.classList.remove('was-validated');
        shippingAddress.classList.remove('is-invalid');

        // Validate shipping address
        const address = shippingAddress.value.trim();
        if (address.length < 10) {
            e.preventDefault();
            isValid = false;
            shippingAddress.classList.add('is-invalid');
            shippingAddress.focus();
        }

        if (!isValid) {
            e.preventDefault();
            form.classList.add('was-validated');
            
            // Show alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Lỗi:</strong> Vui lòng kiểm tra lại thông tin đặt hàng.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            form.insertBefore(alertDiv, form.firstChild);
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Real-time validation
    shippingAddress.addEventListener('input', function() {
        if (this.value.trim().length >= 10) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
    });
});
</script>

<style>
.order-items::-webkit-scrollbar {
    width: 6px;
}

.order-items::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.order-items::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.order-items::-webkit-scrollbar-thumb:hover {
    background: #555;
}

@media (max-width: 991px) {
    .card.sticky-top {
        position: relative !important;
        top: 0 !important;
    }
}
</style>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

