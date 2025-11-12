<?php
/**
 * =====================================================
 * ORDER DETAIL VIEW - Chi tiết đơn hàng
 * =====================================================
 * File: views/user/profile/order-detail.php
 * Mô tả: Hiển thị thông tin chi tiết đơn hàng
 * =====================================================
 */

$order = $order ?? [];
$pageTitle = $pageTitle ?? 'Chi tiết đơn hàng';
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('order/my-orders') ?>">Đơn hàng của tôi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-receipt"></i> Chi tiết đơn hàng
            </h1>
            <p class="text-muted mb-0">Mã đơn hàng: <strong class="text-primary"><?= htmlspecialchars($order['order_code']) ?></strong></p>
        </div>
        <div>
            <?php
            $statusConfig = [
                'pending' => ['label' => 'Chờ xử lý', 'class' => 'warning', 'icon' => 'clock-history'],
                'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'info', 'icon' => 'check-circle'],
                'shipped' => ['label' => 'Đang giao', 'class' => 'primary', 'icon' => 'truck'],
                'delivered' => ['label' => 'Đã giao', 'class' => 'success', 'icon' => 'check-all'],
                'cancelled' => ['label' => 'Đã hủy', 'class' => 'danger', 'icon' => 'x-circle']
            ];
            $status = $statusConfig[$order['status']] ?? ['label' => $order['status'], 'class' => 'secondary', 'icon' => 'question'];
            ?>
            <span class="badge bg-<?= $status['class'] ?> fs-6">
                <i class="bi bi-<?= $status['icon'] ?>"></i>
                <?= $status['label'] ?>
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Cột trái: Thông tin đơn hàng -->
        <div class="col-lg-8">
            <!-- Order Timeline -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Tiến trình đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php
                        $statuses = ['pending', 'confirmed', 'shipped', 'delivered'];
                        $currentIndex = array_search($order['status'], $statuses);
                        $isCancelled = $order['status'] === 'cancelled';
                        
                        foreach ($statuses as $index => $statusKey):
                            $statusInfo = $statusConfig[$statusKey];
                            $isActive = $index <= $currentIndex && !$isCancelled;
                            $isCurrent = $index == $currentIndex && !$isCancelled;
                        ?>
                        <div class="timeline-item <?= $isActive ? 'active' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                            <div class="timeline-marker">
                                <i class="bi bi-<?= $statusInfo['icon'] ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0"><?= $statusInfo['label'] ?></h6>
                                <?php if ($isCurrent): ?>
                                <small class="text-muted">Đang xử lý</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if ($isCancelled): ?>
                        <div class="timeline-item active current">
                            <div class="timeline-marker bg-danger">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0 text-danger">Đơn hàng đã bị hủy</h6>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-bag"></i> Sản phẩm đã đặt</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th width="100" class="text-center">Số lượng</th>
                                    <th width="120" class="text-end">Đơn giá</th>
                                    <th width="120" class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $subtotal = 0;
                                foreach ($order['items'] as $item): 
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    $subtotal += $itemTotal;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= base_url($item['product_image']) ?>" 
                                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                 class="img-thumbnail me-3"
                                                 style="width: 60px; height: 60px; object-fit: cover;"
                                                 onerror="this.src='<?= base_url('public/images/placeholder.png') ?>'">
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                                                <small class="text-muted">ID: #<?= $item['product_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?= $item['quantity'] ?></span>
                                    </td>
                                    <td class="text-end">
                                        <?= number_format($item['price'], 0, ',', '.') ?>đ
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary"><?= number_format($itemTotal, 0, ',', '.') ?>đ</strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tạm tính:</strong></td>
                                    <td class="text-end">
                                        <strong><?= number_format($order['total_price'], 0, ',', '.') ?>đ</strong>
                                    </td>
                                </tr>
                                <?php if ($order['discount'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Giảm giá:</strong></td>
                                    <td class="text-end text-success">
                                        <strong>-<?= number_format($order['discount'], 0, ',', '.') ?>đ</strong>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td class="text-end">
                                        <h5 class="mb-0 text-danger"><?= number_format($order['final_price'], 0, ',', '.') ?>đ</h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mb-4">
                <a href="<?= base_url('order/my-orders') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại đơn hàng của tôi
                </a>
                
                <?php if ($order['status'] === 'pending'): ?>
                <button type="button" class="btn btn-danger" id="btn-cancel-order">
                    <i class="bi bi-x-circle"></i> Hủy đơn hàng
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột phải: Thông tin chi tiết -->
        <div class="col-lg-4">
            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Ngày đặt hàng</small>
                        <strong><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Cập nhật lần cuối</small>
                        <strong><?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Phương thức thanh toán</small>
                        <strong>
                            <?php
                            $paymentMethods = [
                                'cod' => 'Thanh toán khi nhận hàng (COD)',
                                'bank_transfer' => 'Chuyển khoản ngân hàng',
                                'momo' => 'Ví MoMo',
                                'vnpay' => 'VNPay'
                            ];
                            echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </strong>
                    </div>

                    <div>
                        <small class="text-muted d-block">Tổng số lượng</small>
                        <strong><?= count($order['items']) ?> sản phẩm</strong>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-person"></i> Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Tên khách hàng</small>
                        <strong><?= htmlspecialchars($order['username']) ?></strong>
                    </div>

                    <div>
                        <small class="text-muted d-block">Email</small>
                        <strong><?= htmlspecialchars($order['email']) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Địa chỉ giao hàng</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>
            </div>

            <!-- Order Notes -->
            <?php if (!empty($order['notes'])): ?>
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-chat-left-text"></i> Ghi chú</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CSS for Timeline -->
<style>
.timeline {
    position: relative;
    padding: 0;
}

.timeline-item {
    display: flex;
    padding-bottom: 30px;
    position: relative;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 40px;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item.active:not(:last-child)::before {
    background: #0d6efd;
}

.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    flex-shrink: 0;
    z-index: 1;
}

.timeline-item.active .timeline-marker {
    background: #0d6efd;
    color: white;
}

.timeline-item.current .timeline-marker {
    animation: pulse 2s infinite;
    box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
    }
}

.timeline-content {
    padding-top: 5px;
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCancel = document.getElementById('btn-cancel-order');
    
    if (btnCancel) {
        btnCancel.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn hủy đơn hàng <?= $order['order_code'] ?>?')) {
                // Disable button
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';

                fetch('<?= base_url('order/cancel/' . $order['id']) ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Hủy đơn hàng thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                        this.disabled = false;
                        this.innerHTML = '<i class="bi bi-x-circle"></i> Hủy đơn hàng';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi hủy đơn hàng');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-x-circle"></i> Hủy đơn hàng';
                });
            }
        });
    }
});
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
