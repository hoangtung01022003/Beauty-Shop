<?php
/**
 * =====================================================
 * CART MODEL - Quản lý giỏ hàng (Session-based)
 * =====================================================
 * File: models/Cart.php
 * Mô tả: Model xử lý giỏ hàng lưu trong Session
 * Ngày tạo: 12/11/2025
 * =====================================================
 */

class Cart {
    
    /**
     * Session key cho giỏ hàng
     */
    private $sessionKey = 'cart';
    
    /**
     * Constructor - Khởi tạo session
     */
    public function __construct() {
        $this->init();
    }
    
    /**
     * Khởi tạo giỏ hàng trong session
     * @return void
     */
    public function init() {
        // Khởi động session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Tạo giỏ hàng rỗng nếu chưa tồn tại
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
    }
    
    /**
     * Thêm sản phẩm vào giỏ hàng
     * @param int $productId - ID sản phẩm
     * @param int $quantity - Số lượng
     * @param float $price - Giá sản phẩm
     * @param string $name - Tên sản phẩm (optional)
     * @param string $image - Hình ảnh sản phẩm (optional)
     * @return bool
     */
    public function add($productId, $quantity = 1, $price = 0, $name = '', $image = '') {
        try {
            $this->init();
            
            // Validate
            if ($productId <= 0 || $quantity <= 0 || $price < 0) {
                error_log("Invalid cart item data");
                return false;
            }
            
            // Kiểm tra sản phẩm đã có trong giỏ chưa
            if ($this->has($productId)) {
                // Nếu đã có → cộng thêm số lượng
                $_SESSION[$this->sessionKey][$productId]['quantity'] += (int)$quantity;
            } else {
                // Nếu chưa có → thêm mới
                $_SESSION[$this->sessionKey][$productId] = [
                    'product_id' => (int)$productId,
                    'quantity' => (int)$quantity,
                    'price' => (float)$price,
                    'name' => $name,
                    'image' => $image,
                    'subtotal' => (float)$price * (int)$quantity
                ];
            }
            
            // Cập nhật subtotal
            $this->updateSubtotal($productId);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error in Cart::add(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     * @param int $productId - ID sản phẩm
     * @param int $quantity - Số lượng mới
     * @return bool
     */
    public function update($productId, $quantity) {
        try {
            $this->init();
            
            // Nếu quantity = 0 hoặc âm → xóa item
            if ($quantity <= 0) {
                return $this->remove($productId);
            }
            
            // Kiểm tra item có tồn tại không
            if (!$this->has($productId)) {
                error_log("Product ID {$productId} not found in cart");
                return false;
            }
            
            // Cập nhật quantity
            $_SESSION[$this->sessionKey][$productId]['quantity'] = (int)$quantity;
            
            // Cập nhật subtotal
            $this->updateSubtotal($productId);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error in Cart::update(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa 1 sản phẩm khỏi giỏ hàng
     * @param int $productId - ID sản phẩm
     * @return bool
     */
    public function remove($productId) {
        try {
            $this->init();
            
            if ($this->has($productId)) {
                unset($_SESSION[$this->sessionKey][$productId]);
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error in Cart::remove(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa tất cả sản phẩm trong giỏ (clear giỏ hàng)
     * @return void
     */
    public function clear() {
        $this->init();
        $_SESSION[$this->sessionKey] = [];
    }
    
    /**
     * Lấy danh sách tất cả items trong giỏ
     * @return array
     */
    public function getItems() {
        $this->init();
        return $_SESSION[$this->sessionKey] ?? [];
    }
    
    /**
     * Lấy 1 item cụ thể
     * @param int $productId
     * @return array|null
     */
    public function getItem($productId) {
        $this->init();
        return $_SESSION[$this->sessionKey][$productId] ?? null;
    }
    
    /**
     * Tính tổng tiền của giỏ hàng
     * @return float
     */
    public function getTotal() {
        $total = 0;
        
        foreach ($this->getItems() as $item) {
            $total += (float)$item['subtotal'];
        }
        
        return $total;
    }
    
    /**
     * Đếm số lượng items trong giỏ (số loại sản phẩm)
     * @return int
     */
    public function getCount() {
        return count($this->getItems());
    }
    
    /**
     * Đếm tổng số sản phẩm trong giỏ (tổng quantity)
     * @return int
     */
    public function getTotalQuantity() {
        $total = 0;
        
        foreach ($this->getItems() as $item) {
            $total += (int)$item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Kiểm tra giỏ hàng có trống không
     * @return bool
     */
    public function isEmpty() {
        return $this->getCount() === 0;
    }
    
    /**
     * Kiểm tra sản phẩm có trong giỏ không
     * @param int $productId
     * @return bool
     */
    public function has($productId) {
        $this->init();
        return isset($_SESSION[$this->sessionKey][$productId]);
    }
    
    /**
     * Cập nhật subtotal của 1 item
     * @param int $productId
     * @return void
     */
    private function updateSubtotal($productId) {
        if ($this->has($productId)) {
            $item = $_SESSION[$this->sessionKey][$productId];
            $_SESSION[$this->sessionKey][$productId]['subtotal'] = 
                (float)$item['price'] * (int)$item['quantity'];
        }
    }
    
    /**
     * Lấy thông tin chi tiết giỏ hàng (kèm thông tin sản phẩm từ DB)
     * @param object $productModel - Instance của Product model
     * @return array
     */
    public function getItemsWithDetails($productModel) {
        $items = $this->getItems();
        $detailedItems = [];
        
        foreach ($items as $productId => $item) {
            // Lấy thông tin sản phẩm từ database
            $product = $productModel->getById($productId);
            
            if ($product) {
                $detailedItems[$productId] = array_merge($item, [
                    'product_name' => $product['name'],
                    'product_image' => $product['image'],
                    'product_stock' => $product['stock'],
                    'product_status' => $product['status'],
                    'category_name' => $product['category_name'] ?? ''
                ]);
            } else {
                // Nếu sản phẩm không tồn tại, giữ nguyên data từ session
                $detailedItems[$productId] = $item;
            }
        }
        
        return $detailedItems;
    }
    
    /**
     * Validate giỏ hàng (kiểm tra tồn kho, giá, trạng thái)
     * @param object $productModel - Instance của Product model
     * @return array - ['valid' => bool, 'errors' => array]
     */
    public function validate($productModel) {
        $errors = [];
        $items = $this->getItems();
        
        foreach ($items as $productId => $item) {
            // Lấy thông tin sản phẩm từ database
            $product = $productModel->getById($productId);
            
            if (!$product) {
                $errors[$productId] = "Sản phẩm không tồn tại";
                continue;
            }
            
            // Kiểm tra trạng thái
            if ($product['status'] !== 'active') {
                $errors[$productId] = "Sản phẩm '{$product['name']}' hiện không khả dụng";
            }
            
            // Kiểm tra tồn kho
            if ($product['stock'] < $item['quantity']) {
                $errors[$productId] = "Sản phẩm '{$product['name']}' chỉ còn {$product['stock']} trong kho";
            }
            
            // Kiểm tra giá (optional - có thể bỏ qua nếu cho phép giá thay đổi)
            if ($product['price'] != $item['price']) {
                // Cập nhật lại giá mới
                $this->updatePrice($productId, $product['price']);
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Cập nhật giá của item trong giỏ
     * @param int $productId
     * @param float $newPrice
     * @return bool
     */
    public function updatePrice($productId, $newPrice) {
        try {
            $this->init();
            
            if (!$this->has($productId)) {
                return false;
            }
            
            $_SESSION[$this->sessionKey][$productId]['price'] = (float)$newPrice;
            $this->updateSubtotal($productId);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error in Cart::updatePrice(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy tóm tắt giỏ hàng (dùng cho hiển thị)
     * @return array
     */
    public function getSummary() {
        return [
            'count' => $this->getCount(),
            'total_quantity' => $this->getTotalQuantity(),
            'total' => $this->getTotal(),
            'is_empty' => $this->isEmpty()
        ];
    }
    
    /**
     * Sync giỏ hàng từ array (dùng khi user login - merge cart)
     * @param array $cartData
     * @return void
     */
    public function sync($cartData) {
        $this->init();
        
        foreach ($cartData as $productId => $item) {
            if ($this->has($productId)) {
                // Nếu đã có, cộng quantity
                $_SESSION[$this->sessionKey][$productId]['quantity'] += $item['quantity'];
                $this->updateSubtotal($productId);
            } else {
                // Nếu chưa có, thêm mới
                $_SESSION[$this->sessionKey][$productId] = $item;
            }
        }
    }
    
    /**
     * Export giỏ hàng ra array (để lưu vào DB hoặc cookie)
     * @return array
     */
    public function export() {
        return $this->getItems();
    }
    
    /**
     * Debug: In ra giỏ hàng
     * @return void
     */
    public function debug() {
        echo "<pre>";
        echo "=== CART DEBUG ===\n";
        echo "Count: " . $this->getCount() . "\n";
        echo "Total Quantity: " . $this->getTotalQuantity() . "\n";
        echo "Total: " . number_format($this->getTotal(), 0, ',', '.') . "đ\n";
        echo "Is Empty: " . ($this->isEmpty() ? 'Yes' : 'No') . "\n\n";
        echo "Items:\n";
        print_r($this->getItems());
        echo "</pre>";
    }
}

