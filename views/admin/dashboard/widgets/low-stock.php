<?php
/**
 * =====================================================
 * WIDGET - Sản phẩm sắp hết hàng
 * =====================================================
 * File: views/admin/dashboard/widgets/low-stock.php
 * Mô tả: Widget cảnh báo sản phẩm có số lượng tồn kho thấp
 * =====================================================
 */

// Lấy dữ liệu sản phẩm
$products = $lowStockProducts ?? [];
?>

<?php if (empty($products)): ?>
    <div class="text-center py-4 text-success">
        <i class="fas fa-check-circle fa-3x mb-3"></i>
        <p>Tất cả sản phẩm đều đủ hàng</p>
    </div>
<?php else: ?>
    <div class="alert alert-warning mb-3">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Cảnh báo:</strong> Có <?= count($products) ?> sản phẩm sắp hết hàng (tồn kho < 10)
    </div>
    
    <div class="list-group list-group-flush">
        <?php foreach ($products as $product): ?>
            <div class="list-group-item px-0 border-0 border-bottom">
                <div class="d-flex align-items-center">
                    <!-- Product Image -->
                    <div class="me-3">
                        <?php 
                        $imagePath = !empty($product['image']) ? 
                            base_url('uploads/products/' . $product['image']) : 
                            base_url('public/images/placeholder.png');
                        ?>
                        <img src="<?= $imagePath ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="product-img">
                    </div>
                    
                    <!-- Product Info -->
                    <div class="flex-fill">
                        <h6 class="mb-1" style="font-size: 14px;">
                            <?= htmlspecialchars(mb_substr($product['name'], 0, 35)) ?>
                            <?= mb_strlen($product['name']) > 35 ? '...' : '' ?>
                        </h6>
                        <div class="d-flex align-items-center">
                            <small class="text-danger fw-bold me-3">
                                <i class="fas fa-box"></i> 
                                Còn <?= $product['stock'] ?> sản phẩm
                            </small>
                            <small class="text-success">
                                <?= number_format($product['price'] ?? 0) ?>đ
                            </small>
                        </div>
                    </div>
                    
                    <!-- Stock Alert -->
                    <div class="text-end">
                        <?php if ($product['stock'] <= 3): ?>
                            <span class="badge bg-danger">
                                <i class="fas fa-exclamation-circle"></i> Rất thấp
                            </span>
                        <?php elseif ($product['stock'] <= 5): ?>
                            <span class="badge bg-warning">
                                <i class="fas fa-exclamation-triangle"></i> Thấp
                            </span>
                        <?php else: ?>
                            <span class="badge bg-info">
                                <i class="fas fa-info-circle"></i> Cần nhập
                            </span>
                        <?php endif; ?>
                        <div class="mt-2">
                            <a href="<?= base_url('admin/products/edit/' . $product['id']) ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Cập nhật
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-3">
        <a href="<?= base_url('admin/products') ?>" class="btn btn-sm btn-outline-primary">
            Quản lý tồn kho <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
<?php endif; ?>
