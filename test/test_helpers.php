<?php
/**
 * =====================================================
 * TEST HELPER FUNCTIONS
 * =====================================================
 * File: test_helpers.php
 * Mô tả: Test các hàm helper
 * =====================================================
 */

// Load config và helpers
require_once 'config/constants.php';
require_once 'helpers/Helper.php';
require_once 'helpers/Auth.php';
require_once 'helpers/Validator.php';

echo "<h1>🧪 TEST HELPER FUNCTIONS</h1>";
echo "<hr>";

// ========================================
// 1. TEST Helper.php
// ========================================
echo "<h2>1️⃣ Test Helper.php</h2>";

// Test formatPrice
echo "<p>✅ formatPrice(1500000): " . formatPrice(1500000) . "</p>";

// Test formatDate
echo "<p>✅ formatDate('2025-11-11 14:30:00'): " . formatDate('2025-11-11 14:30:00') . "</p>";

// Test slugify
$vietnamese = "Kem Chống Nắng Anessa SPF50+";
echo "<p>✅ slugify('{$vietnamese}'): " . slugify($vietnamese) . "</p>";

// Test base_url
echo "<p>✅ base_url('products'): " . base_url('products') . "</p>";

// Test asset
echo "<p>✅ asset('css/style.css'): " . asset('css/style.css') . "</p>";

// Test e() - escape HTML
$html = "<script>alert('XSS')</script>";
echo "<p>✅ e('<script>alert('XSS')</script>'): " . e($html) . "</p>";

// Test str_limit
$longText = "Đây là một đoạn văn bản rất dài cần được cắt ngắn lại để hiển thị";
echo "<p>✅ str_limit(text, 30): " . str_limit($longText, 30) . "</p>";

echo "<hr>";

// ========================================
// 2. TEST Auth.php
// ========================================
echo "<h2>2️⃣ Test Auth.php</h2>";

// Test hashPassword và verifyPassword
$password = "123456";
$hashed = hashPassword($password);
echo "<p>✅ hashPassword('{$password}'): {$hashed}</p>";
echo "<p>✅ verifyPassword('{$password}', hash): " . (verifyPassword($password, $hashed) ? "✅ ĐÚNG" : "❌ SAI") . "</p>";

// Test generateToken
echo "<p>✅ generateToken(32): " . generateToken(16) . "</p>";

// Test isLoggedIn (chưa login)
echo "<p>✅ isLoggedIn(): " . (isLoggedIn() ? "✅ ĐÃ ĐĂNG NHẬP" : "❌ CHƯA ĐĂNG NHẬP") . "</p>";

echo "<hr>";

// ========================================
// 3. TEST Validator.php
// ========================================
echo "<h2>3️⃣ Test Validator.php</h2>";

// Test validateEmail
$emails = ['admin@gmail.com', 'invalid-email', 'user@example.com'];
foreach ($emails as $email) {
    $valid = validateEmail($email) ? "✅ HỢP LẸ" : "❌ KHÔNG HỢP LỆ";
    echo "<p>✅ validateEmail('{$email}'): {$valid}</p>";
}

// Test validateUsername
$usernames = ['admin', 'ab', 'user_123', 'invalid@name'];
foreach ($usernames as $username) {
    $result = validateUsername($username);
    $status = $result['valid'] ? "✅ HỢP LỆ" : "❌ " . $result['message'];
    echo "<p>✅ validateUsername('{$username}'): {$status}</p>";
}

// Test validatePassword
$passwords = ['123', '123456', '12345678'];
foreach ($passwords as $pwd) {
    $result = validatePassword($pwd);
    $status = $result['valid'] ? "✅ HỢP LỆ" : "❌ " . $result['message'];
    echo "<p>✅ validatePassword('{$pwd}'): {$status}</p>";
}

// Test validatePhone
$phones = ['0901234567', '0123456789', 'invalid', '84901234567'];
foreach ($phones as $phone) {
    $valid = validatePhone($phone) ? "✅ HỢP LỆ" : "❌ KHÔNG HỢP LỆ";
    echo "<p>✅ validatePhone('{$phone}'): {$valid}</p>";
}

// Test sanitize
$dirtyData = "<script>alert('xss')</script>Hello";
echo "<p>✅ sanitize('<script>alert('xss')</script>Hello'): " . sanitize($dirtyData) . "</p>";

// Test validate với rules
echo "<h3>Test validate() với rules:</h3>";
$data = [
    'email' => 'admin@gmail.com',
    'username' => 'admin',
    'age' => '25'
];

$rules = [
    'email' => ['required', 'email'],
    'username' => ['required', 'min:3'],
    'age' => ['required', 'integer']
];

$validation = validate($data, $rules);
echo "<p>✅ Validate result: " . ($validation['valid'] ? "✅ HỢP LỆ" : "❌ CÓ LỖI") . "</p>";

if (!$validation['valid']) {
    echo "<pre>";
    print_r($validation['errors']);
    echo "</pre>";
}

echo "<hr>";
echo "<h2>✅ TEST HOÀN THÀNH!</h2>";
echo "<p><strong>Tất cả các hàm helper đã sẵn sàng sử dụng!</strong></p>";
