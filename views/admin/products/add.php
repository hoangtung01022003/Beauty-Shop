<?php
/**
 * =====================================================
 * ADD PRODUCT PAGE - Thêm sản phẩm mới
 * =====================================================
 * File: views/admin/products/add.php
 * Mô tả: Trang thêm sản phẩm mới
 * =====================================================
 */

$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Thêm sản phẩm mới';
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
                <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><?= $pageTitle ?></h1>
            <a href="<?= base_url('admin/products') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
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

