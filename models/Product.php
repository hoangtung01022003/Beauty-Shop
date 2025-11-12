<?php
/**
 * =====================================================
 * PRODUCT MODEL - Quản lý sản phẩm
 * =====================================================
 * File: models/Product.php
 * Mô tả: Model xử lý dữ liệu sản phẩm
 * Ngày tạo: 12/11/2025
 * =====================================================
 */

require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->table = 'products';
    }
    
    /**
     * Lấy tất cả sản phẩm
     * @param int|null $limit - Giới hạn số lượng
     * @param int|null $offset - Vị trí bắt đầu
     * @param int|null $categoryId - Lọc theo danh mục
     * @return array
     */
    public function getAll($limit = null, $offset = null, $categoryId = null) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE 1=1";
            
            $params = [];
            
            // Lọc theo danh mục nếu có
            if ($categoryId !== null) {
                $sql .= " AND p.category_id = :category_id";
                $params['category_id'] = $categoryId;
            }
            
            $sql .= " ORDER BY p.created_at DESC";
            
            // Phân trang - Phải dùng prepare và bindValue riêng cho LIMIT/OFFSET
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            // Prepare statement
            $stmt = $this->db->prepare($sql);
            
            // Bind các params thông thường
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            // Bind LIMIT và OFFSET với PDO::PARAM_INT
            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in Product::getAll(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy sản phẩm theo ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.id = :id 
                    LIMIT 1";
            
            return $this->queryOne($sql, ['id' => $id]);
            
        } catch (Exception $e) {
            error_log("Error in Product::getById(): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy sản phẩm theo danh mục
     * @param int $categoryId
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getByCategory($categoryId, $limit = null, $offset = null) {
        return $this->getAll($limit, $offset, $categoryId);
    }
    
    /**
     * Tìm kiếm sản phẩm theo từ khóa
     * @param string $keyword - Từ khóa tìm kiếm
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function search($keyword, $limit = null, $offset = null) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE (p.name LIKE :keyword OR p.description LIKE :keyword)
                    ORDER BY p.created_at DESC";
            
            // Phân trang
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            // Prepare statement
            $stmt = $this->db->prepare($sql);
            
            // Bind keyword
            $stmt->bindValue(':keyword', '%' . trim($keyword) . '%', PDO::PARAM_STR);
            
            // Bind LIMIT và OFFSET
            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in Product::search(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Tạo sản phẩm mới
     * @param array $data - ['name', 'description', 'price', 'cost_price', 'image', 'gallery', 'category_id', 'stock', 'status']
     * @return int|false - ID của sản phẩm vừa tạo hoặc false
     */
    public function create($data) {
        try {
            // Validate dữ liệu bắt buộc
            if (empty($data['name']) || empty($data['price']) || empty($data['category_id'])) {
                error_log("Product name, price, and category_id are required");
                return false;
            }
            
            // Chuẩn bị dữ liệu
            $insertData = [
                'name' => trim($data['name']),
                'description' => isset($data['description']) ? trim($data['description']) : null,
                'price' => (float)$data['price'],
                'cost_price' => isset($data['cost_price']) ? (float)$data['cost_price'] : null,
                'image' => isset($data['image']) ? trim($data['image']) : null,
                'gallery' => isset($data['gallery']) ? $data['gallery'] : null,
                'category_id' => (int)$data['category_id'],
                'stock' => isset($data['stock']) ? (int)$data['stock'] : 0,
                'sold' => 0,
                'rating' => 0,
                'status' => isset($data['status']) ? $data['status'] : 'active'
            ];
            
            // Gọi phương thức create từ BaseModel
            return parent::create($insertData);
            
        } catch (Exception $e) {
            error_log("Error in Product::create(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật sản phẩm
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        try {
            // Kiểm tra sản phẩm có tồn tại không
            if (!$this->exists($id)) {
                error_log("Product ID {$id} not found");
                return false;
            }
            
            // Chuẩn bị dữ liệu cập nhật
            $updateData = [];
            
            if (isset($data['name'])) {
                $updateData['name'] = trim($data['name']);
            }
            
            if (isset($data['description'])) {
                $updateData['description'] = trim($data['description']);
            }
            
            if (isset($data['price'])) {
                $updateData['price'] = (float)$data['price'];
            }
            
            if (isset($data['cost_price'])) {
                $updateData['cost_price'] = (float)$data['cost_price'];
            }
            
            if (isset($data['image'])) {
                $updateData['image'] = trim($data['image']);
            }
            
            if (isset($data['gallery'])) {
                $updateData['gallery'] = $data['gallery'];
            }
            
            if (isset($data['category_id'])) {
                $updateData['category_id'] = (int)$data['category_id'];
            }
            
            if (isset($data['stock'])) {
                $updateData['stock'] = (int)$data['stock'];
            }
            
            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }
            
            // Kiểm tra có dữ liệu cần update không
            if (empty($updateData)) {
                return true; // Không có gì để update
            }
            
            // Gọi phương thức update từ BaseModel
            return parent::update($id, $updateData);
            
        } catch (Exception $e) {
            error_log("Error in Product::update(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa sản phẩm
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        try {
            // Kiểm tra sản phẩm có tồn tại không
            if (!$this->exists($id)) {
                error_log("Product ID {$id} not found");
                return false;
            }
            
            // TODO: Kiểm tra sản phẩm có trong đơn hàng nào không
            // Nếu có, nên chuyển sang soft delete thay vì xóa hẳn
            
            // Gọi phương thức delete từ BaseModel
            return parent::delete($id);
            
        } catch (Exception $e) {
            error_log("Error in Product::delete(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Đếm tổng số sản phẩm
     * @return int
     */
    public function countAll() {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $result = $this->queryOne($sql);
            
            return $result ? (int)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error in Product::countAll(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Đếm số sản phẩm theo danh mục
     * @param int $categoryId
     * @return int
     */
    public function countByCategory($categoryId) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE category_id = :category_id";
            $result = $this->queryOne($sql, ['category_id' => $categoryId]);
            
            return $result ? (int)$result['total'] : 0;
            
        } catch (Exception $e) {
            error_log("Error in Product::countByCategory(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Cập nhật tồn kho
     * @param int $id
     * @param int $quantity - Số lượng thay đổi (dương: tăng, âm: giảm)
     * @return bool
     */
    public function updateStock($id, $quantity) {
        try {
            // Kiểm tra sản phẩm có tồn tại không
            if (!$this->exists($id)) {
                error_log("Product ID {$id} not found");
                return false;
            }
            
            // Lấy thông tin sản phẩm hiện tại
            $product = $this->find($id);
            if (!$product) {
                return false;
            }
            
            // Tính tồn kho mới
            $newStock = (int)$product['stock'] + (int)$quantity;
            
            // Không cho phép tồn kho âm
            if ($newStock < 0) {
                error_log("Cannot update stock: New stock would be negative");
                return false;
            }
            
            // Cập nhật
            $sql = "UPDATE {$this->table} 
                    SET stock = :stock, updated_at = NOW() 
                    WHERE id = :id";
            
            return $this->execute($sql, [
                'stock' => $newStock,
                'id' => $id
            ]);
            
        } catch (Exception $e) {
            error_log("Error in Product::updateStock(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật số lượng đã bán
     * @param int $id
     * @param int $quantity - Số lượng đã bán thêm
     * @return bool
     */
    public function updateSold($id, $quantity) {
        try {
            if (!$this->exists($id)) {
                error_log("Product ID {$id} not found");
                return false;
            }
            
            $sql = "UPDATE {$this->table} 
                    SET sold = sold + :quantity, updated_at = NOW() 
                    WHERE id = :id";
            
            return $this->execute($sql, [
                'quantity' => (int)$quantity,
                'id' => $id
            ]);
            
        } catch (Exception $e) {
            error_log("Error in Product::updateSold(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy sản phẩm bán chạy nhất
     * @param int $limit - Số lượng sản phẩm
     * @return array
     */
    public function getBestSelling($limit = 10) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'active'
                    ORDER BY p.sold DESC 
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in Product::getBestSelling(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy sản phẩm mới nhất
     * @param int $limit - Số lượng sản phẩm
     * @return array
     */
    public function getLatest($limit = 10) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'active'
                    ORDER BY p.created_at DESC 
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in Product::getLatest(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy sản phẩm liên quan (cùng danh mục)
     * @param int $productId - ID sản phẩm hiện tại
     * @param int $limit - Số lượng sản phẩm
     * @return array
     */
    public function getRelated($productId, $limit = 6) {
        try {
            // Lấy category_id của sản phẩm hiện tại
            $product = $this->find($productId);
            if (!$product) {
                return [];
            }
            
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.category_id = :category_id 
                    AND p.id != :product_id 
                    AND p.status = 'active'
                    ORDER BY RAND() 
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':category_id', $product['category_id'], PDO::PARAM_INT);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error in Product::getRelated(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kiểm tra tồn kho có đủ không
     * @param int $id
     * @param int $quantity - Số lượng cần kiểm tra
     * @return bool
     */
    public function hasEnoughStock($id, $quantity) {
        try {
            $product = $this->find($id);
            if (!$product) {
                return false;
            }
            
            return (int)$product['stock'] >= (int)$quantity;
            
        } catch (Exception $e) {
            error_log("Error in Product::hasEnoughStock(): " . $e->getMessage());
            return false;
        }
    }
}

