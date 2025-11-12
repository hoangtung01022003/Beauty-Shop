<?php
/**
 * =====================================================
 * EDIT PRODUCT PAGE - Chỉnh sửa sản phẩm
 * =====================================================
 * File: views/admin/products/edit.php
 * Mô tả: Trang chỉnh sửa sản phẩm
 * =====================================================
 */

$product = $product ?? null;
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Chỉnh sửa sản phẩm';
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/products') ?>">Sản phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><?= $pageTitle ?></h1>
            <div>
                <a href="<?= base_url('products/detail/' . $product['id']) ?>" 
                   class="btn btn-info me-2"
                   target="_blank">
                    <i class="bi bi-eye"></i> Xem trang sản phẩm
                </a>
                <a href="<?= base_url('admin/products') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
                <?= $_SESSION['flash_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php 
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            ?>
        <?php endif; ?>

        <!-- Form Component -->
        <?php include_once __DIR__ . '/_form.php'; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

