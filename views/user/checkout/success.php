<?php
/**
 * =====================================================
 * SUCCESS VIEW - Trang đặt hàng thành công
 * =====================================================
 * File: views/user/checkout/success.php
 * Mô tả: Hiển thị thông báo thành công sau khi đặt hàng
 * =====================================================
 */

$order = $order ?? [];
$pageTitle = $pageTitle ?? 'Đặt hàng thành công';
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Card -->
            <div class="card shadow-lg border-0">
                <div class="card-body text-center py-5">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="success-checkmark">
                            <div class="check-icon">
                                <span class="icon-line line-tip"></span>
                                <span class="icon-line line-long"></span>
                                <div class="icon-circle"></div>
                                <div class="icon-fix"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <h1 class="display-5 text-success mb-3">
                        <i class="bi bi-check-circle-fill"></i> Đặt hàng thành công!
                    </h1>
                    <p class="lead text-muted mb-4">
                        Cảm ơn bạn đã đặt hàng. Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý trong thời gian sớm nhất.
                    </p>

                    <hr class="my-4">

                    <!-- Order Information -->
                    <div class="row text-start mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded p-3">
                                        <i class="bi bi-receipt-cutoff fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="text-muted small">Mã đơn hàng</div>
                                    <div class="fw-bold fs-5 text-primary"><?= htmlspecialchars($order['order_code']) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 rounded p-3">
                                        <i class="bi bi-calendar-check fs-4 text-info"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="text-muted small">Ngày đặt hàng</div>
                                    <div class="fw-bold"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded p-3">
                                        <i class="bi bi-cash-stack fs-4 text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="text-muted small">Tổng tiền</div>
                                    <div class="fw-bold fs-5 text-danger"><?= number_format($order['final_price'], 0, ',', '.') ?>đ</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 rounded p-3">
                                        <i class="bi bi-credit-card fs-4 text-warning"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="text-muted small">Thanh toán</div>
                                    <div class="fw-bold">
                                        <?php
                                        $paymentMethods = [
                                            'cod' => 'COD',
                                            'bank_transfer' => 'Chuyển khoản',
                                            'momo' => 'MoMo',
                                            'vnpay' => 'VNPay'
                                        ];
                                        echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="alert alert-light text-start">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold mb-1">Địa chỉ giao hàng:</div>
                                <div class="text-muted"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($order['notes'])): ?>
                    <!-- Order Notes -->
                    <div class="alert alert-info text-start">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-chat-left-text-fill text-info fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold mb-1">Ghi chú:</div>
                                <div class="text-muted"><?= nl2br(htmlspecialchars($order['notes'])) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <hr class="my-4">

                    <!-- Next Steps -->
                    <div class="mb-4">
                        <h5 class="mb-3">📦 Bước tiếp theo</h5>
                        <ul class="list-unstyled text-start text-muted">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i> 
                                Chúng tôi đã gửi email xác nhận đơn hàng đến <strong><?= htmlspecialchars($order['email']) ?></strong>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i> 
                                Đơn hàng sẽ được xác nhận và xử lý trong vòng 24 giờ
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i> 
                                Bạn có thể theo dõi trạng thái đơn hàng trong mục "Đơn hàng của tôi"
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="<?= base_url('order/detail/' . $order['id']) ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye"></i> Xem chi tiết đơn hàng
                        </a>
                        <a href="<?= base_url('products') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-bag"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>

            <!-- Support Info -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="bi bi-headset"></i> 
                    Cần hỗ trợ? Liên hệ: 
                    <a href="tel:0123456789">0123-456-789</a> hoặc 
                    <a href="mailto:support@example.com">support@example.com</a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- CSS Animation -->
<style>
.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.success-checkmark .check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid #198754;
}

.success-checkmark .check-icon::before {
    top: 3px;
    left: -2px;
    width: 30px;
    transform-origin: 100% 50%;
    border-radius: 100px 0 0 100px;
}

.success-checkmark .check-icon::after {
    top: 0;
    left: 30px;
    width: 60px;
    transform-origin: 0 50%;
    border-radius: 0 100px 100px 0;
    animation: rotate-circle 4.25s ease-in;
}

.success-checkmark .check-icon::before,
.success-checkmark .check-icon::after {
    content: '';
    height: 100px;
    position: absolute;
    background: #fff;
    transform: rotate(-45deg);
}

.success-checkmark .icon-line {
    height: 5px;
    background-color: #198754;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.success-checkmark .icon-line.line-tip {
    top: 46px;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: icon-line-tip 0.75s;
}

.success-checkmark .icon-line.line-long {
    top: 38px;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: icon-line-long 0.75s;
}

.success-checkmark .icon-circle {
    top: -4px;
    left: -4px;
    z-index: 10;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    position: absolute;
    box-sizing: content-box;
    border: 4px solid rgba(25, 135, 84, .5);
}

.success-checkmark .icon-fix {
    top: 8px;
    width: 5px;
    left: 26px;
    z-index: 1;
    height: 85px;
    position: absolute;
    transform: rotate(-45deg);
    background-color: #fff;
}

@keyframes rotate-circle {
    0% { transform: rotate(-45deg); }
    5% { transform: rotate(-45deg); }
    12% { transform: rotate(-405deg); }
    100% { transform: rotate(-405deg); }
}

@keyframes icon-line-tip {
    0% { width: 0; left: 1px; top: 19px; }
    54% { width: 0; left: 1px; top: 19px; }
    70% { width: 50px; left: -8px; top: 37px; }
    84% { width: 17px; left: 21px; top: 48px; }
    100% { width: 25px; left: 14px; top: 45px; }
}

@keyframes icon-line-long {
    0% { width: 0; right: 46px; top: 54px; }
    65% { width: 0; right: 46px; top: 54px; }
    84% { width: 55px; right: 0px; top: 35px; }
    100% { width: 47px; right: 8px; top: 38px; }
}
</style>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

