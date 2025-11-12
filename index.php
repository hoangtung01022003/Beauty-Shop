<?php
/**
 * =====================================================
 * INDEX.PHP - Entry Point của ứng dụng
 * =====================================================
 * File: index.php
 * Mô tả: File chính xử lý tất cả request
 * =====================================================
 */

// =====================================================
// 1. KHỞI TẠO SESSION
// =====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// 2. LOAD CÁC FILE CẤU HÌNH VÀ HELPERS
// =====================================================

// Load constants
require_once __DIR__ . '/config/constants.php';

// Load database
require_once __DIR__ . '/config/database.php';

// Load helpers
require_once __DIR__ . '/helpers/Helper.php';
require_once __DIR__ . '/helpers/Auth.php';
require_once __DIR__ . '/helpers/Validator.php';
require_once __DIR__ . '/helpers/FileUpload.php';

// Load base controllers và models
require_once __DIR__ . '/controllers/BaseController.php';
require_once __DIR__ . '/models/BaseModel.php';

// Load router
require_once __DIR__ . '/config/Router.php';

// =====================================================
// 3. ĐỊNH NGHĨA ROUTES
// =====================================================

// ===== AUTH ROUTES =====
Router::get('auth/register', 'AuthController@register');
Router::post('auth/register', 'AuthController@register');

Router::get('auth/login', 'AuthController@login');
Router::post('auth/login', 'AuthController@login');

Router::get('auth/logout', 'AuthController@logout');

Router::get('auth/forgot-password', 'AuthController@showForgotPasswordForm');
Router::post('auth/forgot-password', 'AuthController@forgotPassword');

// ===== USER ROUTES =====
Router::get('', 'ProductController@home');
Router::get('home', 'ProductController@home');
Router::get('user/home', 'ProductController@home');

// Products (User)
Router::get('products', 'ProductController@index');
Router::get('products/search', 'ProductController@search');
Router::get('products/detail/{id}', 'ProductController@detail');
Router::get('products/category/{id}', 'ProductController@category');

// Cart
Router::get('cart', 'CartController@index');
Router::post('cart/add', 'CartController@add');
Router::post('cart/update', 'CartController@update');
Router::post('cart/remove', 'CartController@remove');
Router::post('cart/clear', 'CartController@clear');

// Checkout
Router::get('checkout', 'OrderController@checkout');
Router::post('checkout', 'OrderController@checkout');
Router::get('checkout/success', 'OrderController@success');

// Order - User
Router::get('order/my-orders', 'OrderController@myOrders');
Router::get('order/detail/{id}', 'OrderController@detail');
Router::post('order/cancel/{id}', 'OrderController@cancel');
Router::get('order/success/{id}', 'OrderController@success');

// User profile
Router::get('profile', 'AuthController@profile');
Router::post('profile', 'AuthController@updateProfile');
Router::get('profile/orders', 'OrderController@myOrders');
Router::get('profile/orders/{id}', 'OrderController@detail');

// ===== ADMIN ROUTES =====
Router::get('admin', 'DashboardController@index');
Router::get('admin/dashboard', 'DashboardController@index');

// Admin - Users
Router::get('admin/users', 'UserAdminController@index');
Router::get('admin/users/edit/{id}', 'UserAdminController@edit');
Router::post('admin/users/update/{id}', 'UserAdminController@update');
Router::post('admin/users/delete/{id}', 'UserAdminController@delete');

// Admin - Categories
Router::get('admin/categories', 'CategoryAdminController@index');
Router::get('admin/categories/add', 'CategoryAdminController@add');
Router::post('admin/categories/add', 'CategoryAdminController@store');
Router::get('admin/categories/edit/{id}', 'CategoryAdminController@edit');
Router::post('admin/categories/update/{id}', 'CategoryAdminController@update');
Router::post('admin/categories/delete/{id}', 'CategoryAdminController@delete');

// Admin - Products
Router::get('admin/products', 'ProductAdminController@index');
Router::get('admin/products/create', 'ProductAdminController@create');
Router::post('admin/products/create', 'ProductAdminController@create');
Router::get('admin/products/edit/{id}', 'ProductAdminController@edit');
Router::post('admin/products/edit/{id}', 'ProductAdminController@edit');
Router::post('admin/products/delete/{id}', 'ProductAdminController@delete');
Router::get('admin/products/view/{id}', 'ProductAdminController@view');

// Admin - Orders
Router::get('admin/orders', 'OrderAdminController@index');
Router::get('admin/orders/{id}', 'OrderAdminController@detail');
Router::post('admin/orders/update-status/{id}', 'OrderAdminController@updateStatus');

// =====================================================
// 4. DISPATCH REQUEST
// =====================================================
Router::dispatch();

