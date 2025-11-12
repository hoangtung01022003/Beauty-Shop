<?php
/**
 * =====================================================
 * ADD CATEGORY PAGE - Thêm danh mục mới
 * =====================================================
 * File: views/admin/categories/add.php
 * Mô tả: Trang thêm danh mục mới
 * =====================================================
 */

$pageTitle = $pageTitle ?? 'Thêm danh mục mới';
?>

<?php include_once __DIR__ . '/../../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/categories') ?>">Danh mục</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="bi bi-folder-plus text-primary"></i> <?= $pageTitle ?>
            </h1>
            <a href="<?= base_url('admin/categories') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Form Component -->
        <?php include_once __DIR__ . '/_form.php'; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

