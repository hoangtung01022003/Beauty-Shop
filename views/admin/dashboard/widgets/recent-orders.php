<?php
/**
 * =====================================================
 * WIDGET - Đơn hàng gần đây
 * =====================================================
 * File: views/admin/dashboard/widgets/recent-orders.php
 * Mô tả: Widget hiển thị danh sách đơn hàng gần đây
 * =====================================================
 */

// Lấy dữ liệu orders
$orders = $recentOrders ?? [];

// Hàm helper để lấy class badge theo status
function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-warning',
        'processing' => 'bg-info',
        'shipped' => 'bg-primary',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger'
    ];
    return $badges[$status] ?? 'bg-secondary';
}

// Hàm helper để lấy text theo status
function getStatusText($status) {
    $texts = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đang giao',
        'delivered' => 'Đã giao',
        'cancelled' => 'Đã hủy'
    ];
    return $texts[$status] ?? $status;
}
?>

<?php if (empty($orders)): ?>
    <div class="text-center py-4 text-muted">
        <i class="fas fa-inbox fa-3x mb-3"></i>
        <p>Chưa có đơn hàng nào</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <strong class="text-primary">
                                <?= htmlspecialchars($order['order_code'] ?? '#' . $order['id']) ?>
                            </strong>
                        </td>
                        <td>
                            <?php if (!empty($order['user_id'])): ?>
                                <small class="text-muted">
                                    ID: <?= $order['user_id'] ?>
                                </small>
                            <?php else: ?>
                                <small class="text-muted">Khách vãng lai</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong class="text-danger">
                                <?= number_format($order['final_price'] ?? 0) ?>đ
                            </strong>
                        </td>
                        <td>
                            <span class="badge <?= getStatusBadge($order['status']) ?>">
                                <?= getStatusText($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </small>
                        </td>
                        <td class="text-end">
                            <a href="<?= base_url('admin/orders/' . $order['id']) ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="text-center mt-3">
        <a href="<?= base_url('admin/orders') ?>" class="btn btn-sm btn-outline-primary">
            Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
<?php endif; ?>

