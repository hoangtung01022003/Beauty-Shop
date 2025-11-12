<?php
/**
 * =====================================================
 * PRODUCT FORM COMPONENT - Form sản phẩm
 * =====================================================
 * File: views/admin/products/_form.php
 * Mô tả: Form tái sử dụng cho cả add & edit sản phẩm
 * =====================================================
 */

$product = $product ?? null;
$categories = $categories ?? [];
$action = $action ?? 'create';
$errors = $_SESSION['errors'] ?? [];
$oldData = $_SESSION['old_data'] ?? [];

// Clear session errors và old data sau khi sử dụng
unset($_SESSION['errors']);
unset($_SESSION['old_data']);

// Helper function để lấy giá trị cũ hoặc giá trị hiện tại
function getValue($field, $product, $oldData) {
    if (isset($oldData[$field])) {
        return $oldData[$field];
    }
    if ($product && isset($product[$field])) {
        return $product[$field];
    }
    return '';
}
?>

<form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin sản phẩm</h5>
                </div>
                <div class="card-body">
                    <!-- Tên sản phẩm -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Tên sản phẩm <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= htmlspecialchars(getValue('name', $product, $oldData)) ?>"
                               required
                               maxlength="150">
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $errors['name'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Mô tả -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả sản phẩm</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="6"
                                  placeholder="Nhập mô tả chi tiết về sản phẩm..."><?= htmlspecialchars(getValue('description', $product, $oldData)) ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $errors['description'] ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Mô tả chi tiết sẽ hiển thị trên trang chi tiết sản phẩm</small>
                    </div>

                    <!-- Hàng 1: Giá bán & Giá vốn -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">
                                    Giá bán <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                                           id="price" 
                                           name="price" 
                                           value="<?= htmlspecialchars(getValue('price', $product, $oldData)) ?>"
                                           required
                                           min="0"
                                           step="1000">
                                    <span class="input-group-text">đ</span>
                                    <?php if (isset($errors['price'])): ?>
                                        <div class="invalid-feedback">
                                            <?= $errors['price'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">
                                    Giá vốn
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control <?= isset($errors['cost_price']) ? 'is-invalid' : '' ?>" 
                                           id="cost_price" 
                                           name="cost_price" 
                                           value="<?= htmlspecialchars(getValue('cost_price', $product, $oldData)) ?>"
                                           min="0"
                                           step="1000">
                                    <span class="input-group-text">đ</span>
                                    <?php if (isset($errors['cost_price'])): ?>
                                        <div class="invalid-feedback">
                                            <?= $errors['cost_price'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">Giá vốn dùng để tính toán lợi nhuận (không hiển thị cho khách)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Hàng 2: Tồn kho -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">
                                    Số lượng tồn kho <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>" 
                                       id="stock" 
                                       name="stock" 
                                       value="<?= htmlspecialchars(getValue('stock', $product, $oldData)) ?>"
                                       required
                                       min="0">
                                <?php if (isset($errors['stock'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['stock'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($product): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Đã bán</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="<?= $product['sold'] ?? 0 ?>"
                                           disabled>
                                    <small class="text-muted">Số lượng đã bán (chỉ xem)</small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Hình ảnh -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Hình ảnh sản phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">
                            Hình ảnh chính <?= !$product ? '<span class="text-danger">*</span>' : '' ?>
                        </label>
                        <input type="file" 
                               class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               onchange="previewImage(this)"
                               <?= !$product ? 'required' : '' ?>>
                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['image'] ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</small>
                    </div>

                    <!-- Preview Image -->
                    <div id="imagePreview" class="mt-3">
                        <?php if ($product && $product['image']): ?>
                            <img src="<?= base_url($product['image']) ?>" 
                                 alt="Current image" 
                                 class="img-thumbnail"
                                 style="max-width: 300px;">
                            <p class="text-muted mt-2">Ảnh hiện tại (chọn file mới để thay đổi)</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Danh mục & Trạng thái -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Phân loại</h5>
                </div>
                <div class="card-body">
                    <!-- Danh mục -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">
                            Danh mục <span class="text-danger">*</span>
                        </label>
                        <select class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" 
                                id="category_id" 
                                name="category_id" 
                                required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= getValue('category_id', $product, $oldData) == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category_id'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['category_id'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= getValue('status', $product, $oldData) === 'active' || !$product ? 'selected' : '' ?>>
                                Kích hoạt
                            </option>
                            <option value="inactive" <?= getValue('status', $product, $oldData) === 'inactive' ? 'selected' : '' ?>>
                                Ẩn
                            </option>
                        </select>
                        <small class="text-muted">Sản phẩm ẩn sẽ không hiển thị trên website</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> 
                            <?= $action === 'create' ? 'Thêm sản phẩm' : 'Cập nhật' ?>
                        </button>
                        <a href="<?= base_url('admin/products') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </div>
            </div>

            <!-- Thông tin thêm (nếu đang edit) -->
            <?php if ($product): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Thông tin</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>ID:</strong> #<?= $product['id'] ?><br>
                            <strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($product['created_at'])) ?><br>
                            <strong>Cập nhật:</strong> <?= date('d/m/Y H:i', strtotime($product['updated_at'])) ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<script>
// Preview image khi chọn file
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" 
                     alt="Preview" 
                     class="img-thumbnail"
                     style="max-width: 300px;">
                <p class="text-muted mt-2">Ảnh mới (chưa lưu)</p>
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Bootstrap form validation
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

