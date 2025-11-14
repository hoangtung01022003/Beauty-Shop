<?php
/**
 * =====================================================
 * ADMIN - SỬA ĐƠN HÀNG
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
    <title><?= $pageTitle ?? 'Sửa Đơn Hàng' ?> - Beauty Shop Admin</title>
    
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
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
        .item-row { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
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
                <h4 class="mb-1"><i class="fas fa-edit"></i> <?= $pageTitle ?></h4>
                <p class="text-muted mb-0">Chỉnh sửa thông tin đơn hàng (chỉ đơn chờ xử lý)</p>
            </div>
            <div>
                <a href="<?= base_url('admin/orders/detail/' . $order['id']) ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </div>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?= base_url('admin/orders/update/' . $order['id']) ?>">
        
        <div class="row">
            <!-- Cột trái -->
            <div class="col-md-8">
                
                <!-- Thông tin cơ bản -->
                <div class="content-card">
                    <h5 class="mb-4"><i class="fas fa-info-circle text-primary"></i> Thông Tin Đơn Hàng</h5>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Mã đơn hàng:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($order['order_code']) ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Ngày đặt:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label"><span class="text-danger">*</span> Trạng thái:</label>
                        <div class="col-sm-9">
                            <select name="status" class="form-select" required>
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Đang giao</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Đã giao</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label"><span class="text-danger">*</span> Phương thức thanh toán:</label>
                        <div class="col-sm-9">
                            <select name="payment_method" class="form-select" required>
                                <option value="cod" <?= $order['payment_method'] === 'cod' ? 'selected' : '' ?>>Thanh toán khi nhận hàng (COD)</option>
                                <option value="bank" <?= $order['payment_method'] === 'bank' ? 'selected' : '' ?>>Chuyển khoản ngân hàng</option>
                                <option value="momo" <?= $order['payment_method'] === 'momo' ? 'selected' : '' ?>>Ví MoMo</option>
                                <option value="vnpay" <?= $order['payment_method'] === 'vnpay' ? 'selected' : '' ?>>VNPay</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label"><span class="text-danger">*</span> Địa chỉ giao hàng:</label>
                        <div class="col-sm-9">
                            <textarea name="shipping_address" class="form-control" rows="2" required><?= htmlspecialchars($order['shipping_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Ghi chú:</label>
                        <div class="col-sm-9">
                            <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Giảm giá:</label>
                        <div class="col-sm-9">
                            <input type="number" name="discount" class="form-control" step="0.01" min="0" value="<?= $order['discount'] ?? 0 ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Sản phẩm -->
                <div class="content-card">
                    <h5 class="mb-4">
                        <i class="fas fa-box text-success"></i> Sản Phẩm 
                        <span class="text-muted small">(Có thể sửa số lượng và giá)</span>
                    </h5>
                    
                    <div id="order-items">
                        <?php foreach ($orderItems as $index => $item): ?>
                            <div class="item-row" data-index="<?= $index ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-1">
                                        <img src="<?= get_image_url($item['product_image'], $item['product_name']) ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>"
                                             class="product-img">
                                    </div>
                                    <div class="col-md-4">
                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                        <input type="hidden" name="items[<?= $index ?>][product_id]" value="<?= $item['product_id'] ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Đơn giá:</label>
                                        <input type="number" 
                                               name="items[<?= $index ?>][price]" 
                                               class="form-control form-control-sm item-price" 
                                               step="0.01" 
                                               min="0" 
                                               value="<?= $item['price'] ?>"
                                               data-index="<?= $index ?>"
                                               required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Số lượng:</label>
                                        <input type="number" 
                                               name="items[<?= $index ?>][quantity]" 
                                               class="form-control form-control-sm item-quantity" 
                                               min="1" 
                                               value="<?= $item['quantity'] ?>"
                                               data-index="<?= $index ?>"
                                               required>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <label class="form-label small">Thành tiền:</label>
                                        <div class="fw-bold text-success item-subtotal" data-index="<?= $index ?>">
                                            <?= formatPrice($item['price'] * $item['quantity']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-8 text-end"><strong>Tổng tiền:</strong></div>
                            <div class="col-4 text-end">
                                <strong class="text-success fs-5" id="total-amount">
                                    <?= formatPrice($order['total_price'] ?? 0) ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Cột phải -->
            <div class="col-md-4">
                
                <!-- Thông tin khách hàng -->
                <div class="content-card">
                    <h5 class="mb-4"><i class="fas fa-user text-info"></i> Khách Hàng</h5>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted">Tên khách hàng:</label>
                        <div><strong><?= htmlspecialchars($user['username'] ?? 'N/A') ?></strong></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted">Email:</label>
                        <div><?= htmlspecialchars($user['email'] ?? 'N/A') ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted">Số điện thoại:</label>
                        <div><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted">Địa chỉ:</label>
                        <div><?= htmlspecialchars($user['address'] ?? 'N/A') ?></div>
                    </div>
                </div>
                
                <!-- Nút lưu -->
                <div class="content-card">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                    
                    <a href="<?= base_url('admin/orders/detail/' . $order['id']) ?>" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
                
                <!-- Cảnh báo -->
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Lưu ý:</strong> Chỉ có thể sửa đơn hàng ở trạng thái "Chờ xử lý"
                </div>
                
            </div>
        </div>
        
    </form>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Tính toán lại tổng tiền khi thay đổi giá hoặc số lượng
document.querySelectorAll('.item-price, .item-quantity').forEach(input => {
    input.addEventListener('input', function() {
        const index = this.dataset.index;
        const row = document.querySelector(`.item-row[data-index="${index}"]`);
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const quantity = parseInt(row.querySelector('.item-quantity').value) || 0;
        const subtotal = price * quantity;
        
        // Cập nhật thành tiền
        row.querySelector('.item-subtotal').textContent = formatPrice(subtotal);
        
        // Cập nhật tổng
        updateTotal();
    });
});

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const quantity = parseInt(row.querySelector('.item-quantity').value) || 0;
        total += price * quantity;
    });
    
    document.getElementById('total-amount').textContent = formatPrice(total);
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}
</script>

</body>
</html>

