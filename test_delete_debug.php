<?php
/**
 * Test Delete Functionality
 */
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/Helper.php';
require_once __DIR__ . '/helpers/Auth.php';

echo "=== TEST DELETE USER ===\n\n";

// Giả lập đăng nhập admin
$_SESSION['user_id'] = 2;
$_SESSION['username'] = 'hoangtung11';
$_SESSION['role'] = 'admin';

echo "1. Kiểm tra session admin:\n";
echo "   - Logged in: " . (isLoggedIn() ? 'Yes' : 'No') . "\n";
echo "   - Is admin: " . (isAdmin() ? 'Yes' : 'No') . "\n\n";

echo "2. Test POST Request simulation:\n";
$_SERVER['REQUEST_METHOD'] = 'POST';
echo "   - Method: " . $_SERVER['REQUEST_METHOD'] . "\n\n";

echo "3. Kiểm tra User Model delete():\n";
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/User.php';

$userModel = new User();

// Lấy một user để test (không phải admin đang login)
$users = $userModel->getAll(10, 0);
$testUser = null;
foreach ($users as $u) {
    if ($u['id'] != 2 && $u['role'] != 'admin') {
        $testUser = $u;
        break;
    }
}

if ($testUser) {
    echo "   - Found test user: ID={$testUser['id']}, Username={$testUser['username']}\n";
    echo "   - Role: {$testUser['role']}\n\n";
    
    echo "4. Test các điều kiện trong controller:\n";
    
    // Kiểm tra user tồn tại
    $user = $userModel->findById($testUser['id']);
    echo "   ✅ User exists: " . ($user ? 'Yes' : 'No') . "\n";
    
    // Kiểm tra không xóa chính mình
    $currentUser = getUser();
    $isSelf = ($currentUser['id'] == $testUser['id']);
    echo "   ✅ Is not self: " . ($isSelf ? 'No (BLOCKED)' : 'Yes') . "\n";
    
    // Kiểm tra không xóa admin cuối
    if ($user['role'] === 'admin') {
        $adminCount = $userModel->countAll('admin');
        echo "   ✅ Admin count: {$adminCount}\n";
        echo "   ✅ Can delete admin: " . ($adminCount > 1 ? 'Yes' : 'No (BLOCKED)') . "\n";
    }
    
    echo "\n5. Giả lập xóa (KHÔNG thực sự xóa):\n";
    echo "   - Sẽ gọi: \$userModel->delete({$testUser['id']})\n";
    echo "   - User: {$testUser['username']}\n";
    echo "   ⚠️  Không thực hiện xóa trong test\n\n";
    
} else {
    echo "   ⚠️  Không tìm thấy user phù hợp để test\n\n";
}

echo "6. Kiểm tra URL và Route:\n";
$testId = 5;
echo "   - DELETE URL: " . base_url('admin/users/delete/' . $testId) . "\n";
echo "   - Expected: POST request to this URL\n";
echo "   - Controller: UserAdminController@delete\n\n";

echo "======================\n";
echo "✅ TEST HOÀN TẤT\n";
echo "======================\n\n";

echo "🔍 CÁCH DEBUG:\n";
echo "1. Mở browser console (F12)\n";
echo "2. Click nút Delete\n";
echo "3. Kiểm tra:\n";
echo "   - Form action URL có đúng không?\n";
echo "   - Request có được gửi đi không?\n";
echo "   - Response status code là gì?\n\n";

echo "💡 NẾU VẪN LỖI:\n";
echo "   Thêm dòng này vào đầu UserAdminController::delete():\n";
echo "   error_log('DELETE called for user ID: ' . \$id);\n";
echo "   Sau đó kiểm tra PHP error log\n";
