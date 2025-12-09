<?php
/**
 * =====================================================
 * TEST USER MODEL
 * =====================================================
 * File: test_user_model.php
 * Mô tả: Test các chức năng của User Model
 * =====================================================
 */

// Load các file cần thiết
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'models/User.php';

echo "<h1>🧪 TEST USER MODEL</h1>";
echo "<hr>";

// Khởi tạo User Model
$userModel = new User();

// ========================================
// TEST 1: Tạo user mới
// ========================================
echo "<h2>1️⃣ Test create() - Tạo user mới</h2>";

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
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ Tạo user thành công!<br>";
    echo "<strong>User ID:</strong> {$userId}<br>";
    echo "<strong>Username:</strong> {$newUserData['username']}<br>";
    echo "<strong>Email:</strong> {$newUserData['email']}";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ Tạo user thất bại!";
    echo "</div>";
}

echo "<hr>";

// ========================================
// TEST 2: Tìm user theo ID
// ========================================
echo "<h2>2️⃣ Test findById() - Tìm user theo ID</h2>";

if ($userId) {
    $user = $userModel->findById($userId);
    
    if ($user) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ Tìm thấy user!<br>";
        echo "<strong>ID:</strong> {$user['id']}<br>";
        echo "<strong>Username:</strong> {$user['username']}<br>";
        echo "<strong>Email:</strong> {$user['email']}<br>";
        echo "<strong>Role:</strong> {$user['role']}<br>";
        echo "<strong>Created at:</strong> {$user['created_at']}";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
        echo "❌ Không tìm thấy user!";
        echo "</div>";
    }
}

echo "<hr>";

// ========================================
// TEST 3: Tìm user theo username
// ========================================
echo "<h2>3️⃣ Test findByUsername() - Tìm user theo username</h2>";

$foundUser = $userModel->findByUsername($newUserData['username']);

if ($foundUser) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "✅ Tìm thấy user theo username!<br>";
    echo "<strong>Username:</strong> {$foundUser['username']}<br>";
    echo "<strong>Email:</strong> {$foundUser['email']}";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "❌ Không tìm thấy user!";
    echo "</div>";
}

echo "<hr>";

// ========================================
// TEST 4: Tìm user theo email
// ========================================
echo "<h2>4️⃣ Test findByEmail() - Tìm user theo email</h2>";

$foundUser2 = $userModel->findByEmail($newUserData['email']);

if ($foundUser2) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "✅ Tìm thấy user theo email!<br>";
    echo "<strong>Email:</strong> {$foundUser2['email']}<br>";
    echo "<strong>Username:</strong> {$foundUser2['username']}";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "❌ Không tìm thấy user!";
    echo "</div>";
}

echo "<hr>";

// ========================================
// TEST 5: Xác thực đăng nhập
// ========================================
echo "<h2>5️⃣ Test authenticate() - Xác thực đăng nhập</h2>";

// Test với password đúng
$authUser = $userModel->authenticate($newUserData['username'], '123456');

if ($authUser) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "✅ Đăng nhập thành công!<br>";
    echo "<strong>Username:</strong> {$authUser['username']}<br>";
    echo "<strong>Role:</strong> {$authUser['role']}<br>";
    echo "<strong>Password đã bị xóa khỏi kết quả:</strong> " . (isset($authUser['password']) ? 'NO' : 'YES ✅');
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "❌ Đăng nhập thất bại!";
    echo "</div>";
}

// Test với password sai
echo "<p><strong>Test với password sai:</strong></p>";
$authUserFail = $userModel->authenticate($newUserData['username'], 'wrong_password');

if ($authUserFail) {
    echo "<p style='color: red;'>❌ Lỗi: Đăng nhập thành công với password sai!</p>";
} else {
    echo "<p style='color: green;'>✅ Đúng: Đăng nhập thất bại với password sai</p>";
}

echo "<hr>";

// ========================================
// TEST 6: Cập nhật user
// ========================================
echo "<h2>6️⃣ Test update() - Cập nhật user</h2>";

if ($userId) {
    $updateData = [
        'phone' => '0987654321',
        'address' => 'TP. Hồ Chí Minh, Việt Nam'
    ];
    
    $updateResult = $userModel->update($userId, $updateData);
    
    if ($updateResult) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
        echo "✅ Cập nhật user thành công!<br>";
        
        // Lấy lại user để kiểm tra
        $updatedUser = $userModel->findById($userId);
        echo "<strong>Phone mới:</strong> {$updatedUser['phone']}<br>";
        echo "<strong>Address mới:</strong> {$updatedUser['address']}";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
        echo "❌ Cập nhật user thất bại!";
        echo "</div>";
    }
}

echo "<hr>";

// ========================================
// TEST 7: Kiểm tra username/email tồn tại
// ========================================
echo "<h2>7️⃣ Test usernameExists() & emailExists()</h2>";

$usernameExists = $userModel->usernameExists($newUserData['username']);
echo "<p>✅ usernameExists('{$newUserData['username']}'): " . ($usernameExists ? "✅ TỒN TẠI" : "❌ KHÔNG TỒN TẠI") . "</p>";

$emailExists = $userModel->emailExists($newUserData['email']);
echo "<p>✅ emailExists('{$newUserData['email']}'): " . ($emailExists ? "✅ TỒN TẠI" : "❌ KHÔNG TỒN TẠI") . "</p>";

$usernameNotExists = $userModel->usernameExists('user_not_exists');
echo "<p>✅ usernameExists('user_not_exists'): " . ($usernameNotExists ? "❌ TỒN TẠI" : "✅ KHÔNG TỒN TẠI") . "</p>";

echo "<hr>";

// ========================================
// TEST 8: Lấy danh sách users
// ========================================
echo "<h2>8️⃣ Test getAll() - Lấy danh sách users</h2>";

$allUsers = $userModel->getAll(5); // Lấy 5 users đầu tiên

echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Tổng số users lấy được:</strong> " . count($allUsers) . "</p>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #17a2b8; color: white;'>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Created At</th>
      </tr>";

foreach ($allUsers as $u) {
    echo "<tr>";
    echo "<td>{$u['id']}</td>";
    echo "<td>{$u['username']}</td>";
    echo "<td>{$u['email']}</td>";
    echo "<td><strong>{$u['role']}</strong></td>";
    echo "<td>{$u['created_at']}</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

echo "<hr>";

// ========================================
// TEST 9: Đếm tổng số users
// ========================================
echo "<h2>9️⃣ Test countAll() - Đếm tổng số users</h2>";

$totalUsers = $userModel->countAll();
$totalAdmins = $userModel->countAll(['role' => 'admin']);
$totalNormalUsers = $userModel->countAll(['role' => 'user']);

echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px;'>";
echo "<p>📊 <strong>Tổng số users:</strong> {$totalUsers}</p>";
echo "<p>👑 <strong>Tổng số admin:</strong> {$totalAdmins}</p>";
echo "<p>👤 <strong>Tổng số user thường:</strong> {$totalNormalUsers}</p>";
echo "</div>";

echo "<hr>";

// ========================================
// TEST 10: Lấy thống kê
// ========================================
echo "<h2>🔟 Test getStats() - Lấy thống kê</h2>";

$stats = $userModel->getStats();

echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
echo "<h3>📈 Thống kê Users:</h3>";
echo "<p>📊 <strong>Tổng số users:</strong> {$stats['total']}</p>";
echo "<p>👑 <strong>Số admin:</strong> {$stats['total_admin']}</p>";
echo "<p>👤 <strong>Số user thường:</strong> {$stats['total_user']}</p>";
echo "<p>🆕 <strong>User mới hôm nay:</strong> {$stats['today_new']}</p>";
echo "</div>";

echo "<hr>";

// ========================================
// TEST 11: Tìm kiếm users
// ========================================
echo "<h2>1️⃣1️⃣ Test search() - Tìm kiếm users</h2>";

$searchResults = $userModel->search('admin', 5);

echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Tìm kiếm keyword 'admin':</strong> " . count($searchResults) . " kết quả</p>";

if (!empty($searchResults)) {
    echo "<ul>";
    foreach ($searchResults as $sr) {
        echo "<li><strong>{$sr['username']}</strong> ({$sr['email']}) - Role: {$sr['role']}</li>";
    }
    echo "</ul>";
}
echo "</div>";

echo "<hr>";

// ========================================
// TEST 12: Xóa user (test cuối cùng)
// ========================================
echo "<h2>1️⃣2️⃣ Test delete() - Xóa user</h2>";

if ($userId) {
    $deleteResult = $userModel->delete($userId);
    
    if ($deleteResult) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
        echo "✅ Xóa user thành công!<br>";
        echo "<strong>User ID đã xóa:</strong> {$userId}";
        echo "</div>";
        
        // Kiểm tra xem user còn tồn tại không
        $deletedUser = $userModel->findById($userId);
        echo "<p><strong>Kiểm tra lại:</strong> ";
        echo ($deletedUser === null ? "✅ User đã bị xóa hoàn toàn" : "❌ User vẫn còn tồn tại");
        echo "</p>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
        echo "❌ Xóa user thất bại!";
        echo "</div>";
    }
}

echo "<hr>";
echo "<h2>✅ TEST HOÀN THÀNH!</h2>";
echo "<p><strong>User Model đã sẵn sàng sử dụng!</strong></p>";
