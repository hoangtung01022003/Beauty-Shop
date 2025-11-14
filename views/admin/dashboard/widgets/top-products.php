<?php
/**
 * =====================================================
 * WIDGET - Sản phẩm bán chạy
 * =====================================================
 * File: views/admin/dashboard/widgets/top-products.php
 * Mô tả: Widget hiển thị sản phẩm bán chạy nhất
 * =====================================================
 */

// Lấy dữ liệu sản phẩm
$products = $bestSellingProducts ?? [];
?>

<?php if (empty($products)): ?>
    <div class="text-center py-4 text-muted">
        <i class="fas fa-box-open fa-3x mb-3"></i>
        <p>Chưa có dữ liệu</p>
    </div>
<?php else: ?>
    <div class="list-group list-group-flush">
        <?php foreach ($products as $index => $product): ?>
            <div class="list-group-item px-0 border-0 border-bottom">
                <div class="d-flex align-items-center">
                    <!-- Rank -->
                    <div class="me-3">
                        <span class="badge bg-gradient rounded-circle" 
                              style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; 
                                     background: linear-gradient(135deg, <?= $index === 0 ? '#f59e0b, #d97706' : ($index === 1 ? '#3b82f6, #2563eb' : '#10b981, #059669') ?>);">
                            <strong><?= $index + 1 ?></strong>
                        </span>
                    </div>
                    
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
                            <?= htmlspecialchars(mb_substr($product['name'], 0, 30)) ?>
                            <?= mb_strlen($product['name']) > 30 ? '...' : '' ?>
                        </h6>
                        <div class="d-flex align-items-center">
                            <small class="text-muted me-3">
                                <i class="fas fa-shopping-cart"></i> 
                                <?= number_format($product['total_sold'] ?? 0) ?> đã bán
                            </small>
                            <small class="text-success fw-bold">
                                <?= number_format($product['price'] ?? 0) ?>đ
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-3">
        <a href="<?= base_url('admin/products') ?>" class="btn btn-sm btn-outline-primary">
            Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
<?php endif; ?>

<style>
.bg-gradient {
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
</style>
