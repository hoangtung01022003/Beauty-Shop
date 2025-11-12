<?php
/**
 * =====================================================
 * CART CONTROLLER - Xử lý giỏ hàng
 * =====================================================
 * File: controllers/CartController.php
 * Mô tả: Controller xử lý logic giỏ hàng
 * Ngày tạo: 12/11/2025
 * =====================================================
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../helpers/Helper.php';

class CartController extends BaseController {
    
    private $cart;
    private $productModel;
    
    public function __construct() {
        // Không cần gọi parent::__construct() vì BaseController không có
        
        $this->cart = new Cart();
        $this->productModel = new Product();
    }
    
    /**
     * Hiển thị trang giỏ hàng
     */
    public function index() {
        try {
            // Lấy items từ giỏ hàng với thông tin chi tiết từ DB
            $items = $this->cart->getItemsWithDetails($this->productModel);
            
            // Validate giỏ hàng (kiểm tra stock, giá, trạng thái)
            $validation = $this->cart->validate($this->productModel);
            
            // Tính tổng tiền
            $cartSummary = $this->cart->getSummary();
            
            // Nếu có lỗi validation, hiển thị cảnh báo
            if (!$validation['valid']) {
                foreach ($validation['errors'] as $error) {
                    $this->setFlashMessage('warning', $error);
                }
            }
            
            // Load view
            $data = [
                'items' => $items,
                'cartSummary' => $cartSummary,
                'validationErrors' => $validation['errors'] ?? [],
                'pageTitle' => 'Giỏ hàng'
            ];
            
            $this->view('user/cart/view', $data);
            
        } catch (Exception $e) {
            error_log("Error in CartController::index(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi tải giỏ hàng');
            $this->redirect(base_url());
        }
    }
    
    /**
     * Thêm sản phẩm vào giỏ hàng
     * Hỗ trợ cả AJAX và POST thường
     */
    public function add() {
        try {
            // Chỉ chấp nhận POST
            if (!$this->isMethod('POST')) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Invalid request method');
                }
                $this->redirect(base_url('cart'));
                return;
            }
            
            // Lấy dữ liệu
            $productId = $this->input('product_id');
            $quantity = $this->input('quantity', 1);
            
            // Validate input
            if (empty($productId) || !is_numeric($productId)) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'ID sản phẩm không hợp lệ');
                }
                $this->setFlashMessage('error', 'ID sản phẩm không hợp lệ');
                $this->redirect(base_url('cart'));
                return;
            }
            
            if (!is_numeric($quantity) || $quantity < 1) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Số lượng không hợp lệ');
                }
                $this->setFlashMessage('error', 'Số lượng không hợp lệ');
                $this->redirect(base_url('cart'));
                return;
            }
            
            // Lấy thông tin sản phẩm từ DB
            $product = $this->productModel->getById($productId);
            
            if (!$product) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Sản phẩm không tồn tại');
                }
                $this->setFlashMessage('error', 'Sản phẩm không tồn tại');
                $this->redirect(base_url('cart'));
                return;
            }
            
            // Kiểm tra trạng thái sản phẩm
            if ($product['status'] !== 'active') {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Sản phẩm hiện không khả dụng');
                }
                $this->setFlashMessage('error', 'Sản phẩm hiện không khả dụng');
                $this->redirect(base_url('cart'));
                return;
            }
            
            // Kiểm tra tồn kho
            $currentQuantity = 0;
            if ($this->cart->has($productId)) {
                $item = $this->cart->getItem($productId);
                $currentQuantity = $item['quantity'];
            }
            
            $newQuantity = $currentQuantity + $quantity;
            
            if ($product['stock'] < $newQuantity) {
                $availableStock = $product['stock'] - $currentQuantity;
                $message = "Sản phẩm chỉ còn {$product['stock']} trong kho";
                
                if ($this->isAjax()) {
                    $this->jsonResponse(false, $message, [
                        'available_stock' => $availableStock,
                        'current_quantity' => $currentQuantity
                    ]);
                }
                $this->setFlashMessage('error', $message);
                $this->redirect(base_url('cart'));
                return;
            }
            
            // Thêm vào giỏ hàng
            $success = $this->cart->add(
                $productId,
                $quantity,
                $product['price'],
                $product['name'],
                $product['image']
            );
            
            if ($success) {
                $cartSummary = $this->cart->getSummary();
                
                if ($this->isAjax()) {
                    $this->jsonResponse(true, 'Đã thêm sản phẩm vào giỏ hàng', [
                        'cart_count' => $cartSummary['count'],
                        'cart_total' => $cartSummary['total'],
                        'cart_total_formatted' => formatPrice($cartSummary['total']),
                        'product_name' => $product['name']
                    ]);
                }
                
                $this->setFlashMessage('success', "Đã thêm {$product['name']} vào giỏ hàng");
                $this->redirect(base_url('cart'));
            } else {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Không thể thêm vào giỏ hàng');
                }
                $this->setFlashMessage('error', 'Không thể thêm vào giỏ hàng');
                $this->redirect(base_url('cart'));
            }
            
        } catch (Exception $e) {
            error_log("Error in CartController::add(): " . $e->getMessage());
            
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Có lỗi xảy ra: ' . $e->getMessage());
            }
            
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi thêm vào giỏ hàng');
            $this->redirect(base_url('cart'));
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     * Request AJAX
     */
    public function update() {
        try {
            // Chỉ chấp nhận POST và AJAX
            if (!$this->isMethod('POST')) {
                $this->jsonResponse(false, 'Invalid request method');
                return;
            }
            
            // Lấy dữ liệu
            $productId = $this->input('product_id');
            $quantity = $this->input('quantity');
            
            // Validate input
            if (empty($productId) || !is_numeric($productId)) {
                $this->jsonResponse(false, 'ID sản phẩm không hợp lệ');
                return;
            }
            
            if (!is_numeric($quantity) || $quantity < 0) {
                $this->jsonResponse(false, 'Số lượng không hợp lệ');
                return;
            }
            
            // Kiểm tra sản phẩm có trong giỏ không
            if (!$this->cart->has($productId)) {
                $this->jsonResponse(false, 'Sản phẩm không có trong giỏ hàng');
                return;
            }
            
            // Nếu quantity = 0, xóa sản phẩm
            if ($quantity == 0) {
                $this->cart->remove($productId);
                $cartSummary = $this->cart->getSummary();
                
                $this->jsonResponse(true, 'Đã xóa sản phẩm khỏi giỏ hàng', [
                    'cart_count' => $cartSummary['count'],
                    'cart_total' => $cartSummary['total'],
                    'cart_total_formatted' => formatPrice($cartSummary['total']),
                    'is_empty' => $cartSummary['is_empty']
                ]);
                return;
            }
            
            // Kiểm tra tồn kho
            $product = $this->productModel->getById($productId);
            
            if (!$product) {
                $this->jsonResponse(false, 'Sản phẩm không tồn tại');
                return;
            }
            
            if ($product['stock'] < $quantity) {
                $this->jsonResponse(false, "Sản phẩm chỉ còn {$product['stock']} trong kho", [
                    'max_quantity' => $product['stock']
                ]);
                return;
            }
            
            // Cập nhật giỏ hàng
            $success = $this->cart->update($productId, $quantity);
            
            if ($success) {
                $item = $this->cart->getItem($productId);
                $cartSummary = $this->cart->getSummary();
                
                $this->jsonResponse(true, 'Cập nhật thành công', [
                    'cart_count' => $cartSummary['count'],
                    'cart_total' => $cartSummary['total'],
                    'cart_total_formatted' => formatPrice($cartSummary['total']),
                    'item_subtotal' => $item['subtotal'],
                    'item_subtotal_formatted' => formatPrice($item['subtotal']),
                    'quantity' => $item['quantity']
                ]);
            } else {
                $this->jsonResponse(false, 'Không thể cập nhật giỏ hàng');
            }
            
        } catch (Exception $e) {
            error_log("Error in CartController::update(): " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     * Request AJAX hoặc POST
     */
    public function remove() {
        try {
            // Chỉ chấp nhận POST
            if (!$this->isMethod('POST')) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Invalid request method');
                }
                $this->redirect(base_url('cart'));
                return;
            }
            
            // Lấy product_id
            $productId = $this->input('product_id');
            
            // Validate
            if (empty($productId) || !is_numeric($productId)) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'ID sản phẩm không hợp lệ');
                }
                $this->setFlashMessage('error', 'ID sản phẩm không hợp lệ');
                $this->redirect(base_url('cart'));
                return;
            }
            
            // Kiểm tra có trong giỏ không
            if (!$this->cart->has($productId)) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Sản phẩm không có trong giỏ hàng');
                }
                $this->setFlashMessage('error', 'Sản phẩm không có trong giỏ hàng');
                $this->redirect(base_url('cart'));
                return;
            }
            
            // Xóa khỏi giỏ
            $success = $this->cart->remove($productId);
            
            if ($success) {
                $cartSummary = $this->cart->getSummary();
                
                if ($this->isAjax()) {
                    $this->jsonResponse(true, 'Đã xóa sản phẩm khỏi giỏ hàng', [
                        'cart_count' => $cartSummary['count'],
                        'cart_total' => $cartSummary['total'],
                        'cart_total_formatted' => formatPrice($cartSummary['total']),
                        'is_empty' => $cartSummary['is_empty']
                    ]);
                }
                
                $this->setFlashMessage('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
                $this->redirect(base_url('cart'));
            } else {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Không thể xóa sản phẩm');
                }
                $this->setFlashMessage('error', 'Không thể xóa sản phẩm');
                $this->redirect(base_url('cart'));
            }
            
        } catch (Exception $e) {
            error_log("Error in CartController::remove(): " . $e->getMessage());
            
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Có lỗi xảy ra: ' . $e->getMessage());
            }
            
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi xóa sản phẩm');
            $this->redirect(base_url('cart'));
        }
    }
    
    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear() {
        try {
            $this->cart->clear();
            
            if ($this->isAjax()) {
                $this->jsonResponse(true, 'Đã xóa toàn bộ giỏ hàng', [
                    'cart_count' => 0,
                    'cart_total' => 0,
                    'is_empty' => true
                ]);
            }
            
            $this->setFlashMessage('success', 'Đã xóa toàn bộ giỏ hàng');
            $this->redirect(base_url('cart'));
            
        } catch (Exception $e) {
            error_log("Error in CartController::clear(): " . $e->getMessage());
            
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Có lỗi xảy ra: ' . $e->getMessage());
            }
            
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi xóa giỏ hàng');
            $this->redirect(base_url('cart'));
        }
    }
    
    /**
     * Lấy số lượng item trong giỏ (AJAX)
     */
    public function count() {
        try {
            $cartSummary = $this->cart->getSummary();
            
            $this->jsonResponse(true, 'Success', [
                'count' => $cartSummary['count'],
                'total_quantity' => $cartSummary['total_quantity'],
                'total' => $cartSummary['total'],
                'total_formatted' => formatPrice($cartSummary['total'])
            ]);
            
        } catch (Exception $e) {
            error_log("Error in CartController::count(): " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi xảy ra');
        }
    }
}

