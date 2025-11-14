<?php
/**
 * =====================================================
 * ADMIN - CHI TIẾT ĐƠN HÀNG
 * =====================================================
 */

if (!isLoggedIn() || !isAdmin()) {
    redirect(base_url('auth/login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Chi Tiết Đơn Hàng' ?> - Beauty Shop Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 260px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 0; overflow-y: auto; z-index: 1000; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar-brand { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-brand h4 { color: white; font-weight: bold; margin: 0; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 12px 20px; color: rgba(255,255,255,0.9); text-decoration: none; transition: all 0.3s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: rgba(255,255,255,0.1); color: white; padding-left: 30px; }
        .sidebar-menu li a i { width: 20px; margin-right: 10px; }
        .main-content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        .page-header { background: white; padding: 20px 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .content-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .info-row { display: flex; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #6b7280; min-width: 150px; }
        .info-value { color: #1f2937; flex: 1; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .status-timeline { position: relative; padding: 20px 0; }
        .status-item { position: relative; padding-left: 40px; padding-bottom: 30px; }
        .status-item:last-child { padding-bottom: 0; }
        .status-item::before { content: ''; position: absolute; left: 12px; top: 30px; bottom: -10px; width: 2px; background: #e5e7eb; }
        .status-item:last-child::before { display: none; }
        .status-dot { position: absolute; left: 0; top: 5px; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .status-dot.active { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .status-dot.pending { background: #e5e7eb; color: #9ca3af; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fas fa-spa"></i> Beauty Shop</h4>
        <small>Admin Panel</small>
    </div>
    
    <ul class="sidebar-menu">
        <li><a href="<?= base_url('admin/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="<?= base_url('admin/products') ?>"><i class="fas fa-box"></i> Sản phẩm</a></li>
        <li><a href="<?= base_url('admin/categories') ?>"><i class="fas fa-tags"></i> Danh mục</a></li>
        <li><a href="<?= base_url('admin/orders') ?>" class="active"><i class="fas fa-shopping-cart"></i> Đơn hàng</a></li>
        <li><a href="<?= base_url('admin/users') ?>"><i class="fas fa-users"></i> Người dùng</a></li>
        <li><a href="<?= base_url('home') ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Xem website</a></li>
        <li><a href="<?= base_url('auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
    </ul>
</div>

<div class="main-content">
    
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1"><i class="fas fa-receipt"></i> <?= $pageTitle ?></h4>
                <p class="text-muted mb-0">Chi tiết và quản lý đơn hàng</p>
            </div>
            <div>
                <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>
    
    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-md-8">
            <div class="content-card">
                <h5 class="mb-4"><i class="fas fa-info-circle text-primary"></i> Thông Tin Đơn Hàng</h5>
                
                <div class="info-row">
                    <div class="info-label">Mã đơn hàng:</div>
                    <div class="info-value"><strong class="text-primary"><?= htmlspecialchars($order['order_code']) ?></strong></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Ngày đặt:</div>
                    <div class="info-value"><?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Trạng thái:</div>
                    <div class="info-value">
                        <?php
                        $statusClass = [
                            'pending' => 'warning',
                            'confirmed' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $statusText = [
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đã xác nhận',
                            'shipped' => 'Đang giao',
                            'delivered' => 'Đã giao',
                            'cancelled' => 'Đã hủy'
                        ];
                        $class = $statusClass[$order['status']] ?? 'secondary';
                        $text = $statusText[$order['status']] ?? $order['status'];
                        ?>
                        <span class="badge bg-<?= $class ?>" id="current-status-badge"><?= $text ?></span>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Phương thức thanh toán:</div>
                    <div class="info-value">
                        <?php
                        $paymentMethods = [
                            'cod' => 'Thanh toán khi nhận hàng (COD)',
                            'bank' => 'Chuyển khoản ngân hàng',
                            'momo' => 'Ví MoMo',
                            'vnpay' => 'VNPay'
                        ];
                        echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                        ?>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Địa chỉ giao hàng:</div>
                    <div class="info-value"><?= htmlspecialchars($order['shipping_address'] ?? 'N/A') ?></div>
                </div>
                
                <?php if (!empty($order['notes'])): ?>
                <div class="info-row">
                    <div class="info-label">Ghi chú:</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($order['notes'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sản phẩm trong đơn hàng -->
            <div class="content-card">
                <h5 class="mb-4"><i class="fas fa-box text-success"></i> Sản Phẩm Đã Đặt</h5>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="80">Hình</th>
                                <th>Sản phẩm</th>
                                <th width="100">Đơn giá</th>
                                <th width="80">SL</th>
                                <th width="120" class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $subtotal = 0;
                            foreach ($orderItems as $item): 
                                $itemTotal = $item['quantity'] * $item['price'];
                                $subtotal += $itemTotal;
                            ?>
                                <tr>
                                    <td>
                                        <img src="<?= get_image_url($item['product_image'], $item['product_name']) ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>"
                                             class="product-img">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                    </td>
                                    <td><?= formatPrice($item['price']) ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end"><strong><?= formatPrice($itemTotal) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Tạm tính:</strong></td>
                                <td class="text-end"><strong><?= formatPrice($subtotal) ?></strong></td>
                            </tr>
                            <?php if (!empty($order['discount']) && $order['discount'] > 0): ?>
                            <tr>
                                <td colspan="4" class="text-end text-danger"><strong>Giảm giá:</strong></td>
                                <td class="text-end text-danger"><strong>-<?= formatPrice($order['discount']) ?></strong></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="table-active">
                                <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                                <td class="text-end"><strong class="text-success fs-5"><?= formatPrice($order['final_price']) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Sidebar phải -->
        <div class="col-md-4">
            <!-- Thông tin khách hàng -->
            <div class="content-card">
                <h5 class="mb-4"><i class="fas fa-user text-info"></i> Khách Hàng</h5>
                
                <div class="info-row">
                    <div class="info-label">Tên:</div>
                    <div class="info-value"><strong><?= htmlspecialchars($user['username'] ?? 'N/A') ?></strong></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?= htmlspecialchars($user['email'] ?? 'N/A') ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Số điện thoại:</div>
                    <div class="info-value"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Địa chỉ:</div>
                    <div class="info-value"><?= htmlspecialchars($user['address'] ?? 'N/A') ?></div>
                </div>
            </div>
            
            <!-- Cập nhật trạng thái -->
            <div class="content-card">
                <h5 class="mb-4"><i class="fas fa-edit text-warning"></i> Cập Nhật Trạng Thái</h5>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Chọn trạng thái mới:</strong></label>
                    <select class="form-select" id="new-status">
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                        <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Đang giao</option>
                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Đã giao</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
                
                <button type="button" class="btn btn-primary w-100" onclick="updateStatus()">
                    <i class="fas fa-save"></i> Cập nhật trạng thái
                </button>
                
                <div id="status-message" class="mt-3" style="display: none;"></div>
            </div>
            
            <!-- Timeline trạng thái -->
            <div class="content-card">
                <h5 class="mb-4"><i class="fas fa-history text-secondary"></i> Lịch Sử Trạng Thái</h5>
                
                <div class="status-timeline">
                    <div class="status-item">
                        <div class="status-dot active">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <strong>Đơn hàng đã đặt</strong>
                            <div class="small text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                        </div>
                    </div>
                    
                    <?php
                    $statusFlow = ['pending', 'processing', 'shipping', 'completed'];
                    $currentStatusIndex = array_search($order['status'], $statusFlow);
                    
                    $statusNames = [
                        'processing' => 'Đang xử lý',
                        'shipping' => 'Đang giao hàng',
                        'completed' => 'Đã hoàn thành'
                    ];
                    
                    foreach ($statusNames as $status => $name):
                        $statusIndex = array_search($status, $statusFlow);
                        $isActive = $currentStatusIndex !== false && $statusIndex <= $currentStatusIndex;
                    ?>
                        <div class="status-item">
                            <div class="status-dot <?= $isActive ? 'active' : 'pending' ?>">
                                <i class="fas <?= $isActive ? 'fa-check' : 'fa-circle' ?>"></i>
                            </div>
                            <div>
                                <strong><?= $name ?></strong>
                                <?php if ($isActive): ?>
                                    <div class="small text-muted">Đã cập nhật</div>
                                <?php else: ?>
                                    <div class="small text-muted">Chưa cập nhật</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if ($order['status'] === 'cancelled'): ?>
                        <div class="status-item">
                            <div class="status-dot" style="background: #ef4444; color: white;">
                                <i class="fas fa-times"></i>
                            </div>
                            <div>
                                <strong class="text-danger">Đơn hàng đã hủy</strong>
                                <div class="small text-muted">Đã hủy</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateStatus() {
    const newStatus = document.getElementById('new-status').value;
    const orderId = <?= $order['id'] ?>;
    const messageDiv = document.getElementById('status-message');
    const badge = document.getElementById('current-status-badge');
    
    // Hiển thị loading
    messageDiv.style.display = 'block';
    messageDiv.className = 'alert alert-info mt-3';
    messageDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
    
    // Gửi request AJAX
    fetch('<?= base_url('admin/orders/updateStatus/' . $order['id']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Thành công
            messageDiv.className = 'alert alert-success mt-3';
            messageDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            
            // Cập nhật badge
            const statusClass = {
                'pending': 'warning',
                'processing': 'info',
                'shipping': 'primary',
                'completed': 'success',
                'cancelled': 'danger'
            };
            badge.className = 'badge bg-' + statusClass[data.new_status];
            badge.textContent = data.status_text;
            
            // Reload sau 2 giây
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            // Lỗi
            messageDiv.className = 'alert alert-danger mt-3';
            messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.className = 'alert alert-danger mt-3';
        messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Có lỗi xảy ra khi cập nhật';
    });
}
</script>

</body>
</html>

