<?php
/**
 * =====================================================
 * ORDER CONTROLLER - Xử lý đơn hàng
 * =====================================================
 * File: controllers/OrderController.php
 * Mô tả: Controller xử lý checkout và đơn hàng
 * =====================================================
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Helper.php';

class OrderController extends BaseController {
    
    private $orderModel;
    private $cart;
    private $productModel;
    
    public function __construct() {
        $this->orderModel = new Order();
        $this->cart = new Cart();
        $this->productModel = new Product();
    }
    
    /**
     * Trang checkout
     */
    public function checkout() {
        // Kiểm tra user đã login
        if (!isLoggedIn()) {
            $this->setFlashMessage('error', 'Vui lòng đăng nhập để thanh toán');
            redirect(base_url('auth/login?redirect=' . urlencode('checkout')));
            return;
        }
        
        // Kiểm tra giỏ hàng không trống
        if ($this->cart->isEmpty()) {
            $this->setFlashMessage('error', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán');
            redirect(base_url('cart'));
            return;
        }
        
        // Validate giỏ hàng
        $validation = $this->cart->validate($this->productModel);
        if (!$validation['valid']) {
            foreach ($validation['errors'] as $error) {
                $this->setFlashMessage('error', $error);
            }
            redirect(base_url('cart'));
            return;
        }
        
        if ($this->isMethod('POST')) {
            // Xử lý POST - Tạo đơn hàng
            $this->processCheckout();
        } else {
            // GET - Hiển thị form checkout
            $this->showCheckoutForm();
        }
    }
    
    /**
     * Hiển thị form checkout
     */
    private function showCheckoutForm() {
        try {
            $user = getCurrentUser();
            $cartItems = $this->cart->getItemsWithDetails($this->productModel);
            $cartSummary = $this->cart->getSummary();
            
            // Phí vận chuyển
            $shippingFee = 30000;
            $freeShippingThreshold = 500000;
            $actualShippingFee = $cartSummary['total'] >= $freeShippingThreshold ? 0 : $shippingFee;
            $finalTotal = $cartSummary['total'] + $actualShippingFee;
            
            $data = [
                'user' => $user,
                'cartItems' => $cartItems,
                'cartSummary' => $cartSummary,
                'shippingFee' => $actualShippingFee,
                'finalTotal' => $finalTotal,
                'pageTitle' => 'Thanh toán'
            ];
            
            $this->view('user/checkout/checkout', $data);
            
        } catch (Exception $e) {
            error_log("Error in showCheckoutForm: " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url('cart'));
        }
    }
    
    /**
     * Xử lý checkout (tạo đơn hàng)
     */
    private function processCheckout() {
        try {
            // Lấy dữ liệu từ form
            $shippingAddress = $this->input('shipping_address');
            $paymentMethod = $this->input('payment_method', 'cod');
            $notes = $this->input('notes', '');
            
            // Validate
            $errors = $this->validateCheckoutData([
                'shipping_address' => $shippingAddress,
                'payment_method' => $paymentMethod
            ]);
            
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->setFlashMessage('error', $error);
                }
                redirect(base_url('checkout'));
                return;
            }
            
            // Lấy thông tin user
            $user = getCurrentUser();
            $userId = $user['id'];
            
            // Lấy items từ giỏ hàng
            $cartItems = $this->cart->getItemsWithDetails($this->productModel);
            
            if (empty($cartItems)) {
                $this->setFlashMessage('error', 'Giỏ hàng trống');
                redirect(base_url('cart'));
                return;
            }
            
            // Validate lại giỏ hàng (stock, giá)
            $validation = $this->cart->validate($this->productModel);
            if (!$validation['valid']) {
                foreach ($validation['errors'] as $error) {
                    $this->setFlashMessage('error', $error);
                }
                redirect(base_url('cart'));
                return;
            }
            
            // Tính tổng tiền
            $cartSummary = $this->cart->getSummary();
            $shippingFee = 30000;
            $freeShippingThreshold = 500000;
            $actualShippingFee = $cartSummary['total'] >= $freeShippingThreshold ? 0 : $shippingFee;
            $totalAmount = $cartSummary['total'] + $actualShippingFee;
            
            // Bắt đầu transaction
            $this->orderModel->beginTransaction();
            
            try {
                // 1. Tạo order
                $orderData = [
                    'total_amount' => $totalAmount,
                    'shipping_address' => $shippingAddress,
                    'payment_method' => $paymentMethod,
                    'notes' => $notes
                ];
                
                $orderId = $this->orderModel->createOrder($userId, $orderData);
                
                if (!$orderId) {
                    throw new Exception('Không thể tạo đơn hàng');
                }
                
                // 2. Thêm items vào order
                $orderItems = [];
                foreach ($cartItems as $productId => $item) {
                    $orderItems[] = [
                        'product_id' => $productId,
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ];
                }
                
                $itemsAdded = $this->orderModel->addItems($orderId, $orderItems);
                
                if (!$itemsAdded) {
                    throw new Exception('Không thể thêm sản phẩm vào đơn hàng');
                }
                
                // 3. Cập nhật stock và sold của sản phẩm
                foreach ($cartItems as $productId => $item) {
                    $product = $this->productModel->getById($productId);
                    
                    if ($product) {
                        $newStock = $product['stock'] - $item['quantity'];
                        $newSold = $product['sold'] + $item['quantity'];
                        
                        $this->productModel->update($productId, [
                            'stock' => $newStock,
                            'sold' => $newSold
                        ]);
                    }
                }
                
                // 4. Clear giỏ hàng
                $this->cart->clear();
                
                // Commit transaction
                $this->orderModel->commit();
                
                // Redirect đến trang success
                $this->setFlashMessage('success', 'Đặt hàng thành công!');
                redirect(base_url('order/success/' . $orderId));
                
            } catch (Exception $e) {
                // Rollback nếu có lỗi
                $this->orderModel->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("Error in processCheckout: " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
            redirect(base_url('checkout'));
        }
    }
    
    /**
     * Validate dữ liệu checkout
     */
    private function validateCheckoutData($data) {
        $errors = [];
        
        if (empty($data['shipping_address'])) {
            $errors[] = 'Vui lòng nhập địa chỉ giao hàng';
        } elseif (strlen($data['shipping_address']) < 10) {
            $errors[] = 'Địa chỉ giao hàng phải có ít nhất 10 ký tự';
        }
        
        $validPaymentMethods = ['cod', 'bank_transfer', 'momo', 'vnpay'];
        if (!in_array($data['payment_method'], $validPaymentMethods)) {
            $errors[] = 'Phương thức thanh toán không hợp lệ';
        }
        
        return $errors;
    }
    
    /**
     * Trang thành công sau khi đặt hàng
     */
    public function success($orderId) {
        try {
            // Kiểm tra user đã login
            if (!isLoggedIn()) {
                redirect(base_url('auth/login'));
                return;
            }
            
            // Lấy thông tin order
            $order = $this->orderModel->getById($orderId);
            
            if (!$order) {
                $this->setFlashMessage('error', 'Không tìm thấy đơn hàng');
                redirect(base_url());
                return;
            }
            
            // Kiểm tra quyền xem order (chỉ user sở hữu hoặc admin)
            $user = getCurrentUser();
            if ($order['user_id'] != $user['id'] && $user['role'] !== 'admin') {
                $this->setFlashMessage('error', 'Bạn không có quyền xem đơn hàng này');
                redirect(base_url());
                return;
            }
            
            $data = [
                'order' => $order,
                'pageTitle' => 'Đặt hàng thành công'
            ];
            
            $this->view('user/checkout/success', $data);
            
        } catch (Exception $e) {
            error_log("Error in success: " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url());
        }
    }
    
    /**
     * Danh sách đơn hàng của user
     */
    public function myOrders() {
        try {
            // Kiểm tra user đã login
            if (!isLoggedIn()) {
                $this->setFlashMessage('error', 'Vui lòng đăng nhập');
                redirect(base_url('auth/login'));
                return;
            }
            
            $user = getCurrentUser();
            $userId = $user['id'];
            
            // Phân trang
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            
            // Lấy orders
            $orders = $this->orderModel->getByUser($userId, $perPage, $offset);
            $totalOrders = $this->orderModel->countByUser($userId);
            $totalPages = ceil($totalOrders / $perPage);
            
            $data = [
                'orders' => $orders,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalOrders' => $totalOrders,
                'pageTitle' => 'Đơn hàng của tôi'
            ];
            
            $this->view('user/profile/my-orders', $data);
            
        } catch (Exception $e) {
            error_log("Error in myOrders: " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url());
        }
    }
    
    /**
     * Chi tiết đơn hàng
     */
    public function detail($orderId) {
        try {
            // Kiểm tra user đã login
            if (!isLoggedIn()) {
                $this->setFlashMessage('error', 'Vui lòng đăng nhập');
                redirect(base_url('auth/login'));
                return;
            }
            
            // Lấy thông tin order
            $order = $this->orderModel->getById($orderId);
            
            if (!$order) {
                $this->setFlashMessage('error', 'Không tìm thấy đơn hàng');
                redirect(base_url('order/my-orders'));
                return;
            }
            
            // Kiểm tra quyền xem order
            $user = getCurrentUser();
            if ($order['user_id'] != $user['id'] && $user['role'] !== 'admin') {
                $this->setFlashMessage('error', 'Bạn không có quyền xem đơn hàng này');
                redirect(base_url('order/my-orders'));
                return;
            }
            
            $data = [
                'order' => $order,
                'pageTitle' => 'Chi tiết đơn hàng #' . $order['order_code']
            ];
            
            $this->view('user/profile/order-detail', $data);
            
        } catch (Exception $e) {
            error_log("Error in detail: " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url('order/my-orders'));
        }
    }
    
    /**
     * Hủy đơn hàng (chỉ khi status = pending)
     */
    public function cancel($orderId) {
        try {
            // Kiểm tra user đã login
            if (!isLoggedIn()) {
                $this->jsonResponse(false, 'Vui lòng đăng nhập');
                return;
            }
            
            // Lấy order
            $order = $this->orderModel->getById($orderId);
            
            if (!$order) {
                $this->jsonResponse(false, 'Không tìm thấy đơn hàng');
                return;
            }
            
            // Kiểm tra quyền
            $user = getCurrentUser();
            if ($order['user_id'] != $user['id']) {
                $this->jsonResponse(false, 'Bạn không có quyền hủy đơn hàng này');
                return;
            }
            
            // Chỉ được hủy khi status = pending
            if ($order['status'] !== 'pending') {
                $this->jsonResponse(false, 'Chỉ có thể hủy đơn hàng ở trạng thái chờ xử lý');
                return;
            }
            
            // Cập nhật status
            $success = $this->orderModel->updateStatus($orderId, 'cancelled');
            
            if ($success) {
                // Hoàn lại stock
                foreach ($order['items'] as $item) {
                    $product = $this->productModel->getById($item['product_id']);
                    if ($product) {
                        $newStock = $product['stock'] + $item['quantity'];
                        $newSold = $product['sold'] - $item['quantity'];
                        
                        $this->productModel->update($item['product_id'], [
                            'stock' => $newStock,
                            'sold' => max(0, $newSold)
                        ]);
                    }
                }
                
                $this->jsonResponse(true, 'Hủy đơn hàng thành công');
            } else {
                $this->jsonResponse(false, 'Không thể hủy đơn hàng');
            }
            
        } catch (Exception $e) {
            error_log("Error in cancel: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi xảy ra');
        }
    }
}

