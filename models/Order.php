<?php
/**
 * =====================================================
 * ORDER MODEL - Quản lý đơn hàng
 * =====================================================
 * File: models/Order.php
 * Mô tả: Model xử lý dữ liệu đơn hàng
 * =====================================================
 */

require_once __DIR__ . '/BaseModel.php';

class Order extends BaseModel {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'orders';
    }
    
    /**
     * Tạo đơn hàng mới
     * @param int $userId
     * @param array $data - ['total_amount', 'shipping_address', 'payment_method', 'notes']
     * @return int|false - order_id hoặc false
     */
    public function createOrder($userId, $data) {
        try {
            // Tạo order_code tự động
            $orderCode = $this->generateOrderCode();
            
            // Chuẩn bị dữ liệu
            $totalPrice = (float)$data['total_amount'];
            $discount = isset($data['discount']) ? (float)$data['discount'] : 0;
            $finalPrice = $totalPrice - $discount;
            
            $orderData = [
                'user_id' => (int)$userId,
                'order_code' => $orderCode,
                'total_price' => $totalPrice,
                'discount' => $discount,
                'final_price' => $finalPrice,
                'shipping_address' => isset($data['shipping_address']) ? trim($data['shipping_address']) : '',
                'payment_method' => isset($data['payment_method']) ? $data['payment_method'] : 'cash',
                'notes' => isset($data['notes']) ? trim($data['notes']) : null,
                'status' => 'pending'
            ];
            
            // Insert vào database
            $sql = "INSERT INTO {$this->table} 
                    (user_id, order_code, total_price, discount, final_price, shipping_address, payment_method, notes, status, created_at) 
                    VALUES 
                    (:user_id, :order_code, :total_price, :discount, :final_price, :shipping_address, :payment_method, :notes, :status, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($orderData);
            
            return $this->db->lastInsertId();
            
        } catch (Exception $e) {
            error_log("Error in Order::createOrder(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tạo mã đơn hàng tự động: ORD-2025-00001
     * @return string
     */
    private function generateOrderCode() {
        try {
            $year = date('Y');
            $prefix = "ORD-{$year}-";
            
            // Lấy order code lớn nhất trong năm
            $sql = "SELECT order_code FROM {$this->table} 
                    WHERE order_code LIKE :pattern 
                    ORDER BY id DESC LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['pattern' => $prefix . '%']);
            $lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($lastOrder) {
                // Tách số cuối cùng và tăng lên 1
                $lastNumber = (int)substr($lastOrder['order_code'], -5);
                $newNumber = $lastNumber + 1;
            } else {
                // Đơn hàng đầu tiên trong năm
                $newNumber = 1;
            }
            
            return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            
        } catch (Exception $e) {
            error_log("Error generating order code: " . $e->getMessage());
            return 'ORD-' . date('Y') . '-' . uniqid();
        }
    }
    
    /**
     * Thêm items vào đơn hàng
     * @param int $orderId
     * @param array $items - [['product_id', 'quantity', 'price'], ...]
     * @return bool
     */
    public function addItems($orderId, $items) {
        try {
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (:order_id, :product_id, :quantity, :price)";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($items as $item) {
                $stmt->execute([
                    'order_id' => (int)$orderId,
                    'product_id' => (int)$item['product_id'],
                    'quantity' => (int)$item['quantity'],
                    'price' => (float)$item['price']
                ]);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error in Order::addItems(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy thông tin đơn hàng theo ID (kèm items)
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        try {
            // Lấy thông tin order
            $sql = "SELECT o.*, u.username, u.email 
                    FROM {$this->table} o
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.id = :id";
            
            $order = $this->queryOne($sql, ['id' => $id]);
            
            if (!$order) {
                return null;
            }
            
            // Lấy items của order
            $itemsSql = "SELECT oi.*, p.name as product_name, p.image as product_image
                         FROM order_items oi
                         LEFT JOIN products p ON oi.product_id = p.id
                         WHERE oi.order_id = :order_id";
            
            $order['items'] = $this->query($itemsSql, ['order_id' => $id]);
            
            return $order;
            
        } catch (Exception $e) {
            error_log("Error in Order::getById(): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy đơn hàng theo user (phân trang)
     * @param int $userId
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getByUser($userId, $limit = null, $offset = null) {
        try {
            $sql = "SELECT o.*, 
                           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                    FROM {$this->table} o
                    WHERE o.user_id = :user_id
                    ORDER BY o.created_at DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
            
            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in Order::getByUser(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Đếm số đơn hàng của user
     * @param int $userId
     * @return int
     */
    public function countByUser($userId) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id";
            $result = $this->queryOne($sql, ['user_id' => $userId]);
            return $result ? (int)$result['total'] : 0;
        } catch (Exception $e) {
            error_log("Error in Order::countByUser(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Lấy tất cả đơn hàng (admin)
     * @param string|null $status - 'pending', 'processing', 'completed', 'cancelled'
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getAll($status = null, $limit = null, $offset = null) {
        try {
            $sql = "SELECT o.*, u.username, u.email,
                           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                    FROM {$this->table} o
                    LEFT JOIN users u ON o.user_id = u.id";
            
            $params = [];
            
            if ($status !== null) {
                $sql .= " WHERE o.status = :status";
                $params['status'] = $status;
            }
            
            $sql .= " ORDER BY o.created_at DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in Order::getAll(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     * @param int $id
     * @param string $status - 'pending', 'processing', 'completed', 'cancelled'
     * @return bool
     */
    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE {$this->table} 
                    SET status = :status, updated_at = NOW() 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'status' => $status
            ]);
            
        } catch (Exception $e) {
            error_log("Error in Order::updateStatus(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Đếm tổng số đơn hàng
     * @return int
     */
    public function countAll() {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $result = $this->queryOne($sql);
            return $result ? (int)$result['total'] : 0;
        } catch (Exception $e) {
            error_log("Error in Order::countAll(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Đếm đơn hàng theo trạng thái
     * @param string $status
     * @return int
     */
    public function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = :status";
            $result = $this->queryOne($sql, ['status' => $status]);
            return $result ? (int)$result['total'] : 0;
        } catch (Exception $e) {
            error_log("Error in Order::countByStatus(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Tính tổng doanh thu
     * @param string|null $status - null: tất cả, 'delivered': chỉ đơn hoàn thành
     * @return float
     */
    public function getTotalRevenue($status = 'delivered') {
        try {
            $sql = "SELECT SUM(final_price) as total FROM {$this->table}";
            $params = [];
            
            if ($status !== null) {
                $sql .= " WHERE status = :status";
                $params['status'] = $status;
            }
            
            $result = $this->queryOne($sql, $params);
            return $result ? (float)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error in Order::getTotalRevenue(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Tính doanh thu theo tháng hiện tại
     * @return float
     */
    public function getMonthlyRevenue() {
        try {
            $sql = "SELECT SUM(final_price) as total 
                    FROM {$this->table} 
                    WHERE status = 'delivered' 
                    AND MONTH(created_at) = MONTH(CURRENT_DATE())
                    AND YEAR(created_at) = YEAR(CURRENT_DATE())";
            
            $result = $this->queryOne($sql);
            return $result ? (float)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error in Order::getMonthlyRevenue(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Lấy đơn hàng gần đây
     * @param int $limit
     * @return array
     */
    public function getRecent($limit = 10) {
        try {
            return $this->getAll(null, $limit, 0);
        } catch (Exception $e) {
            error_log("Error in Order::getRecent(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Tìm kiếm đơn hàng
     * @param string $keyword - Tìm theo order_code, username, email
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function search($keyword, $limit = null, $offset = null) {
        try {
            $sql = "SELECT o.*, u.username, u.email,
                           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                    FROM {$this->table} o
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.order_code LIKE :keyword 
                       OR u.username LIKE :keyword
                       OR u.email LIKE :keyword
                    ORDER BY o.created_at DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':keyword', '%' . trim($keyword) . '%', PDO::PARAM_STR);
            
            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in Order::search(): " . $e->getMessage());
            return [];
        }
    }
}

