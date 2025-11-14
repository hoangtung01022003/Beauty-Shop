<?php
/**
 * =====================================================
 * WIDGET - Người dùng mới
 * =====================================================
 * File: views/admin/dashboard/widgets/recent-users.php
 * Mô tả: Widget hiển thị danh sách người dùng đăng ký mới
 * =====================================================
 */

// Lấy dữ liệu users
$users = $recentUsers ?? [];
?>

<?php if (empty($users)): ?>
    <div class="text-center py-4 text-muted">
        <i class="fas fa-user-slash fa-3x mb-3"></i>
        <p>Chưa có người dùng mới</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-2">
                                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($user['username']) ?></strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= htmlspecialchars($user['email']) ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-crown"></i> Admin
                                </span>
                            <?php else: ?>
                                <span class="badge bg-info">
                                    <i class="fas fa-user"></i> User
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </small>
                        </td>
                        <td class="text-end">
                            <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="text-center mt-3">
        <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-primary">
            Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
<?php endif; ?>
