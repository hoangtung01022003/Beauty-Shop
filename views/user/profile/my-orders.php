<?php
/**
 * =====================================================
 * MY ORDERS VIEW - Danh sách đơn hàng của user
 * =====================================================
 * File: views/user/profile/my-orders.php
 * Mô tả: Hiển thị danh sách đơn hàng
 * =====================================================
 */

$orders = $orders ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalOrders = $totalOrders ?? 0;
$pageTitle = $pageTitle ?? 'Đơn hàng của tôi';
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('profile') ?>">Tài khoản</a></li>
            <li class="breadcrumb-item active" aria-current="page">Đơn hàng của tôi</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-box-seam"></i> Đơn hàng của tôi
            </h1>
            <p class="text-muted mb-0">Quản lý và theo dõi đơn hàng của bạn</p>
        </div>
        <div>
            <span class="badge bg-primary fs-6">
                Tổng: <?= $totalOrders ?> đơn hàng
            </span>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <!-- Empty State -->
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-inbox display-1 text-muted mb-4"></i>
                <h3>Chưa có đơn hàng nào</h3>
                <p class="text-muted mb-4">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!</p>
                <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-bag"></i> Mua sắm ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Orders Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="150">Mã đơn hàng</th>
                                <th width="120">Ngày đặt</th>
                                <th width="80" class="text-center">Số lượng</th>
                                <th width="120" class="text-end">Tổng tiền</th>
                                <th width="120" class="text-center">Trạng thái</th>
                                <th width="100" class="text-center">Thanh toán</th>
                                <th width="150" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <!-- Mã đơn hàng -->
                                <td>
                                    <a href="<?= base_url('order/detail/' . $order['id']) ?>" class="fw-bold text-primary text-decoration-none">
                                        <?= htmlspecialchars($order['order_code']) ?>
                                    </a>
                                </td>

                                <!-- Ngày đặt -->
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3"></i>
                                        <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                        <br>
                                        <i class="bi bi-clock"></i>
                                        <?= date('H:i', strtotime($order['created_at'])) ?>
                                    </small>
                                </td>

                                <!-- Số lượng items -->
                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        <?= $order['item_count'] ?? 0 ?> sản phẩm
                                    </span>
                                </td>

                                <!-- Tổng tiền -->
                                <td class="text-end">
                                    <strong class="text-danger">
                                        <?= number_format($order['final_price'], 0, ',', '.') ?>đ
                                    </strong>
                                </td>

                                <!-- Trạng thái -->
                                <td class="text-center">
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
                                    <span class="badge bg-<?= $status['class'] ?>">
                                        <i class="bi bi-<?= $status['icon'] ?>"></i>
                                        <?= $status['label'] ?>
                                    </span>
                                </td>

                                <!-- Phương thức thanh toán -->
                                <td class="text-center">
                                    <?php
                                    $paymentIcons = [
                                        'cod' => ['icon' => 'cash-coin', 'text' => 'COD'],
                                        'bank_transfer' => ['icon' => 'bank', 'text' => 'Chuyển khoản'],
                                        'momo' => ['icon' => 'phone', 'text' => 'MoMo'],
                                        'vnpay' => ['icon' => 'credit-card', 'text' => 'VNPay']
                                    ];
                                    $payment = $paymentIcons[$order['payment_method']] ?? ['icon' => 'wallet2', 'text' => $order['payment_method']];
                                    ?>
                                    <small class="text-muted">
                                        <i class="bi bi-<?= $payment['icon'] ?>"></i>
                                        <?= $payment['text'] ?>
                                    </small>
                                </td>

                                <!-- Hành động -->
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('order/detail/' . $order['id']) ?>" 
                                           class="btn btn-outline-primary" 
                                           title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <?php if ($order['status'] === 'pending'): ?>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-cancel-order" 
                                                data-order-id="<?= $order['id'] ?>"
                                                data-order-code="<?= htmlspecialchars($order['order_code']) ?>"
                                                title="Hủy đơn">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="card-footer">
                <nav aria-label="Orders pagination">
                    <ul class="pagination justify-content-center mb-0">
                        <!-- Previous -->
                        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('order/my-orders?page=' . ($currentPage - 1)) ?>">
                                <i class="bi bi-chevron-left"></i> Trước
                            </a>
                        </li>

                        <!-- Page numbers -->
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_url('order/my-orders?page=' . $i) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>

                        <!-- Next -->
                        <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('order/my-orders?page=' . ($currentPage + 1)) ?>">
                                Sau <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận hủy đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn hủy đơn hàng <strong id="cancel-order-code"></strong>?</p>
                <p class="text-muted small">Lưu ý: Chỉ có thể hủy đơn hàng khi đơn đang ở trạng thái "Chờ xử lý".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" id="confirm-cancel-btn">Xác nhận hủy</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cancelOrderId = null;
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));

    // Show cancel modal
    document.querySelectorAll('.btn-cancel-order').forEach(btn => {
        btn.addEventListener('click', function() {
            cancelOrderId = this.dataset.orderId;
            document.getElementById('cancel-order-code').textContent = this.dataset.orderCode;
            cancelModal.show();
        });
    });

    // Confirm cancel
    document.getElementById('confirm-cancel-btn').addEventListener('click', function() {
        if (!cancelOrderId) return;

        // Disable button
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';

        fetch('<?= base_url('order/cancel/') ?>' + cancelOrderId, {
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
                cancelModal.hide();
                this.disabled = false;
                this.innerHTML = 'Xác nhận hủy';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi hủy đơn hàng');
            cancelModal.hide();
            this.disabled = false;
            this.innerHTML = 'Xác nhận hủy';
        });
    });
});
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

