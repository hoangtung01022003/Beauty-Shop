<?php
/**
 * =====================================================
 * TEST USER MODEL - CLI VERSION
 * =====================================================
 * File: test_user_model_cli.php
 * Mô tả: Test User Model qua Command Line
 * =====================================================
 */

// Load các file cần thiết
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'models/User.php';

echo "========================================\n";
echo "🧪 TEST USER MODEL - CLI VERSION\n";
echo "========================================\n\n";

// Khởi tạo User Model
$userModel = new User();

// ========================================
// TEST 1: Tạo user mới
// ========================================
echo "1️⃣ Test create() - Tạo user mới\n";
echo "----------------------------------------\n";

$newUserData = [
    'username' => 'testuser_' . time(),
    'email' => 'testuser_' . time() . '@example.com',
    'password' => '123456',
    'role' => 'user',
    'phone' => '0901234567',
    'address' => 'Hà Nội, Việt Nam'
];

$userId = $userModel->create($newUserData);

if ($userId) {
    echo "✅ Tạo user thành công!\n";
    echo "   User ID: {$userId}\n";
    echo "   Username: {$newUserData['username']}\n";
    echo "   Email: {$newUserData['email']}\n";
} else {
    echo "❌ Tạo user thất bại!\n";
    exit(1);
}

echo "\n";

// ========================================
// TEST 2: Tìm user theo ID
// ========================================
echo "2️⃣ Test findById() - Tìm user theo ID\n";
echo "----------------------------------------\n";

$user = $userModel->findById($userId);

if ($user) {
    echo "✅ Tìm thấy user!\n";
    echo "   ID: {$user['id']}\n";
    echo "   Username: {$user['username']}\n";
    echo "   Email: {$user['email']}\n";
    echo "   Role: {$user['role']}\n";
    echo "   Created at: {$user['created_at']}\n";
} else {
    echo "❌ Không tìm thấy user!\n";
}

echo "\n";

// ========================================
// TEST 3: Tìm user theo username
// ========================================
echo "3️⃣ Test findByUsername() - Tìm user theo username\n";
echo "----------------------------------------\n";

$foundUser = $userModel->findByUsername($newUserData['username']);

if ($foundUser) {
    echo "✅ Tìm thấy user theo username!\n";
    echo "   Username: {$foundUser['username']}\n";
    echo "   Email: {$foundUser['email']}\n";
} else {
    echo "❌ Không tìm thấy user!\n";
}

echo "\n";

// ========================================
// TEST 4: Tìm user theo email
// ========================================
echo "4️⃣ Test findByEmail() - Tìm user theo email\n";
echo "----------------------------------------\n";

$foundUser2 = $userModel->findByEmail($newUserData['email']);

if ($foundUser2) {
    echo "✅ Tìm thấy user theo email!\n";
    echo "   Email: {$foundUser2['email']}\n";
    echo "   Username: {$foundUser2['username']}\n";
} else {
    echo "❌ Không tìm thấy user!\n";
}

echo "\n";

// ========================================
// TEST 5: Xác thực đăng nhập
// ========================================
echo "5️⃣ Test authenticate() - Xác thực đăng nhập\n";
echo "----------------------------------------\n";

// Test với password đúng
$authUser = $userModel->authenticate($newUserData['username'], '123456');

if ($authUser) {
    echo "✅ Đăng nhập thành công!\n";
    echo "   Username: {$authUser['username']}\n";
    echo "   Role: {$authUser['role']}\n";
    echo "   Password đã bị xóa: " . (isset($authUser['password']) ? 'NO ❌' : 'YES ✅') . "\n";
} else {
    echo "❌ Đăng nhập thất bại!\n";
}

// Test với password sai
echo "\n   Test với password sai:\n";
$authUserFail = $userModel->authenticate($newUserData['username'], 'wrong_password');

if ($authUserFail) {
    echo "   ❌ Lỗi: Đăng nhập thành công với password sai!\n";
} else {
    echo "   ✅ Đúng: Đăng nhập thất bại với password sai\n";
}

echo "\n";

// ========================================
// TEST 6: Cập nhật user
// ========================================
echo "6️⃣ Test update() - Cập nhật user\n";
echo "----------------------------------------\n";

$updateData = [
    'phone' => '0987654321',
    'address' => 'TP. Hồ Chí Minh, Việt Nam'
];

$updateResult = $userModel->update($userId, $updateData);

if ($updateResult) {
    echo "✅ Cập nhật user thành công!\n";
    
    $updatedUser = $userModel->findById($userId);
    echo "   Phone mới: {$updatedUser['phone']}\n";
    echo "   Address mới: {$updatedUser['address']}\n";
} else {
    echo "❌ Cập nhật user thất bại!\n";
}

echo "\n";

// ========================================
// TEST 7: Kiểm tra username/email tồn tại
// ========================================
echo "7️⃣ Test usernameExists() & emailExists()\n";
echo "----------------------------------------\n";

$usernameExists = $userModel->usernameExists($newUserData['username']);
echo "✅ usernameExists('{$newUserData['username']}'): " . ($usernameExists ? "TỒN TẠI ✅" : "KHÔNG TỒN TẠI ❌") . "\n";

$emailExists = $userModel->emailExists($newUserData['email']);
echo "✅ emailExists('{$newUserData['email']}'): " . ($emailExists ? "TỒN TẠI ✅" : "KHÔNG TỒN TẠI ❌") . "\n";

$usernameNotExists = $userModel->usernameExists('user_not_exists');
echo "✅ usernameExists('user_not_exists'): " . ($usernameNotExists ? "TỒN TẠI ❌" : "KHÔNG TỒN TẠI ✅") . "\n";

echo "\n";

// ========================================
// TEST 8: Lấy danh sách users
// ========================================
echo "8️⃣ Test getAll() - Lấy danh sách users\n";
echo "----------------------------------------\n";

$allUsers = $userModel->getAll(5);

echo "Tổng số users lấy được: " . count($allUsers) . "\n\n";
echo "ID | Username          | Email                    | Role  | Created At\n";
echo "--------------------------------------------------------------------------------\n";

foreach ($allUsers as $u) {
    printf("%-3s| %-17s | %-24s | %-5s | %s\n", 
        $u['id'], 
        $u['username'], 
        $u['email'], 
        $u['role'], 
        $u['created_at']
    );
}

echo "\n";

// ========================================
// TEST 9: Đếm tổng số users
// ========================================
echo "9️⃣ Test countAll() - Đếm tổng số users\n";
echo "----------------------------------------\n";

$totalUsers = $userModel->countAll();
$totalAdmins = $userModel->countAll(['role' => 'admin']);
$totalNormalUsers = $userModel->countAll(['role' => 'user']);

echo "📊 Tổng số users: {$totalUsers}\n";
echo "👑 Tổng số admin: {$totalAdmins}\n";
echo "👤 Tổng số user thường: {$totalNormalUsers}\n";

echo "\n";

// ========================================
// TEST 10: Lấy thống kê
// ========================================
echo "🔟 Test getStats() - Lấy thống kê\n";
echo "----------------------------------------\n";

$stats = $userModel->getStats();

echo "📈 Thống kê Users:\n";
echo "   📊 Tổng số users: {$stats['total']}\n";
echo "   👑 Số admin: {$stats['total_admin']}\n";
echo "   👤 Số user thường: {$stats['total_user']}\n";
echo "   🆕 User mới hôm nay: {$stats['today_new']}\n";

echo "\n";

// ========================================
// TEST 11: Tìm kiếm users
// ========================================
echo "1️⃣1️⃣ Test search() - Tìm kiếm users\n";
echo "----------------------------------------\n";

$searchResults = $userModel->search('admin', 5);

echo "Tìm kiếm keyword 'admin': " . count($searchResults) . " kết quả\n";

if (!empty($searchResults)) {
    foreach ($searchResults as $sr) {
        echo "   - {$sr['username']} ({$sr['email']}) - Role: {$sr['role']}\n";
    }
}

echo "\n";

// ========================================
// TEST 12: Xóa user
// ========================================
echo "1️⃣2️⃣ Test delete() - Xóa user\n";
echo "----------------------------------------\n";

$deleteResult = $userModel->delete($userId);

if ($deleteResult) {
    echo "✅ Xóa user thành công!\n";
    echo "   User ID đã xóa: {$userId}\n";
    
    $deletedUser = $userModel->findById($userId);
    echo "   Kiểm tra lại: " . ($deletedUser === null ? "✅ User đã bị xóa hoàn toàn" : "❌ User vẫn còn tồn tại") . "\n";
} else {
    echo "❌ Xóa user thất bại!\n";
}

echo "\n";
echo "========================================\n";
echo "✅ TEST HOÀN THÀNH!\n";
echo "User Model đã sẵn sàng sử dụng!\n";
echo "========================================\n";
