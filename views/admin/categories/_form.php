<?php
/**
 * =====================================================
 * CATEGORY FORM COMPONENT - Form danh mục
 * =====================================================
 * File: views/admin/categories/_form.php
 * Mô tả: Form tái sử dụng cho cả add & edit danh mục
 * =====================================================
 */

$category = $category ?? null;
$action = $action ?? 'add';
$errors = $_SESSION['errors'] ?? [];
$oldData = $_SESSION['old_data'] ?? [];

// Clear session errors và old data sau khi sử dụng
unset($_SESSION['errors']);
unset($_SESSION['old_data']);

// Helper function để lấy giá trị cũ hoặc giá trị hiện tại
function getValue($field, $category, $oldData) {
    if (isset($oldData[$field])) {
        return $oldData[$field];
    }
    if ($category && isset($category[$field])) {
        return $category[$field];
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
                    <h5 class="mb-0">Thông tin danh mục</h5>
                </div>
                <div class="card-body">
                    <!-- Tên danh mục -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Tên danh mục <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= htmlspecialchars(getValue('name', $category, $oldData)) ?>"
                               required
                               maxlength="100"
                               placeholder="Ví dụ: Trang điểm, Chăm sóc da, Nước hoa...">
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $errors['name'] ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Tên danh mục sẽ hiển thị trên menu và trang danh sách sản phẩm</small>
                    </div>

                    <!-- Mô tả -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả danh mục</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="5"
                                  placeholder="Nhập mô tả ngắn về danh mục này..."><?= htmlspecialchars(getValue('description', $category, $oldData)) ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $errors['description'] ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Mô tả giúp khách hàng hiểu rõ hơn về các sản phẩm trong danh mục</small>
                    </div>
                </div>
            </div>

            <!-- Hình ảnh -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Hình ảnh đại diện</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh danh mục</label>
                        <input type="file" 
                               class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               onchange="previewImage(this)">
                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['image'] ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB. Khuyến nghị: 800x600px</small>
                    </div>

                    <!-- Preview Image -->
                    <div id="imagePreview" class="mt-3">
                        <?php if ($category && $category['image']): ?>
                            <img src="<?= base_url($category['image']) ?>" 
                                 alt="Current image" 
                                 class="img-thumbnail"
                                 style="max-width: 400px;"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+DQogICAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+DQo8L3N2Zz4='">
                            <p class="text-muted mt-2">Ảnh hiện tại (chọn file mới để thay đổi)</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Action Buttons -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thao tác</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> 
                            <?= $action === 'add' ? 'Thêm danh mục' : 'Cập nhật' ?>
                        </button>
                        <a href="<?= base_url('admin/categories') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </div>
            </div>

            <!-- Thông tin thêm (nếu đang edit) -->
            <?php if ($category): ?>
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Thông tin</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1"><strong>ID:</strong></small>
                            <span class="badge bg-primary">#<?= $category['id'] ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1"><strong>Số sản phẩm:</strong></small>
                            <span class="badge bg-success fs-6"><?= $category['product_count'] ?? 0 ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1"><strong>Ngày tạo:</strong></small>
                            <small><?= date('d/m/Y H:i', strtotime($category['created_at'])) ?></small>
                        </div>
                        
                        <div class="mb-0">
                            <small class="text-muted d-block mb-1"><strong>Cập nhật:</strong></small>
                            <small><?= date('d/m/Y H:i', strtotime($category['updated_at'])) ?></small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Hướng dẫn -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Gợi ý</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <ul class="ps-3 mb-0">
                            <li>Tên danh mục nên ngắn gọn, dễ hiểu</li>
                            <li>Sử dụng mô tả để giải thích rõ hơn</li>
                            <li>Hình ảnh đẹp giúp thu hút khách hàng</li>
                            <li>Không thể xóa danh mục có sản phẩm</li>
                        </ul>
                    </small>
                </div>
            </div>
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
                     style="max-width: 400px;">
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

