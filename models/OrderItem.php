<?php
/**
 * =====================================================
 * ORDER ITEM MODEL - Chi tiết đơn hàng
 * =====================================================
 * File: models/OrderItem.php
 * Mô tả: Model xử lý dữ liệu chi tiết đơn hàng
 * =====================================================
 */

require_once __DIR__ . '/BaseModel.php';

class OrderItem extends BaseModel {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'order_items';
    }
    
    /**
     * Lấy tất cả items của một đơn hàng
     * @param int $orderId
     * @return array
     */
    public function getByOrderId($orderId) {
        try {
            $sql = "SELECT oi.*, 
                           p.name as product_name, 
                           p.image as product_image,
                           p.slug as product_slug,
                           (oi.quantity * oi.price) as subtotal
                    FROM {$this->table} oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = :order_id
                    ORDER BY oi.id ASC";
            
            return $this->query($sql, ['order_id' => $orderId]);
            
        } catch (Exception $e) {
            error_log("Error in OrderItem::getByOrderId(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Thêm item vào đơn hàng
     * @param int $orderId
     * @param int $productId
     * @param int $quantity
     * @param float $price
     * @return bool
     */
    public function add($orderId, $productId, $quantity, $price) {
        try {
            $sql = "INSERT INTO {$this->table} (order_id, product_id, quantity, price) 
                    VALUES (:order_id, :product_id, :quantity, :price)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'order_id' => (int)$orderId,
                'product_id' => (int)$productId,
                'quantity' => (int)$quantity,
                'price' => (float)$price
            ]);
            
        } catch (Exception $e) {
            error_log("Error in OrderItem::add(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa tất cả items của một đơn hàng
     * @param int $orderId
     * @return bool
     */
    public function deleteByOrderId($orderId) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE order_id = :order_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['order_id' => $orderId]);
            
        } catch (Exception $e) {
            error_log("Error in OrderItem::deleteByOrderId(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Đếm số items trong đơn hàng
     * @param int $orderId
     * @return int
     */
    public function countByOrderId($orderId) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE order_id = :order_id";
            $result = $this->queryOne($sql, ['order_id' => $orderId]);
            return $result ? (int)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error in OrderItem::countByOrderId(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Tính tổng giá trị items trong đơn hàng
     * @param int $orderId
     * @return float
     */
    public function getTotalByOrderId($orderId) {
        try {
            $sql = "SELECT SUM(quantity * price) as total FROM {$this->table} WHERE order_id = :order_id";
            $result = $this->queryOne($sql, ['order_id' => $orderId]);
            return $result ? (float)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error in OrderItem::getTotalByOrderId(): " . $e->getMessage());
            return 0;
        }
    }
}

