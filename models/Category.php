<?php
/**
 * =====================================================
 * CATEGORY MODEL - Quản lý danh mục sản phẩm
 * =====================================================
 * File: models/Category.php
 * Mô tả: Model xử lý dữ liệu danh mục sản phẩm
 * Ngày tạo: 12/11/2025
 * =====================================================
 */

require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = 'categories';
    }

    /**
     * Lấy tất cả danh mục
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getAll($limit = null, $offset = null)
    {
        try {
            $sql = "SELECT c.*, 
                           COUNT(p.id) as product_count
                    FROM {$this->table} c
                    LEFT JOIN products p ON c.id = p.category_id
                    GROUP BY c.id 
                    ORDER BY c.name ASC";

            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }

            $stmt = $this->db->prepare($sql);

            if ($limit !== null) {
                $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
                }
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error in Category::getAll(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh mục theo ID
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        try {
            return $this->find($id);

        } catch (Exception $e) {
            error_log("Error in Category::getById(): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo danh mục mới
     * @param array $data - ['name', 'description', 'image', 'status']
     * @return int|false - ID của danh mục vừa tạo hoặc false
     */
    public function create($data)
    {
        try {
            // Validate dữ liệu
            if (empty($data['name'])) {
                return false;
            }

            // Chuẩn bị dữ liệu với timestamp
            $insertData = [
                'name' => trim($data['name']),
                'description' => isset($data['description']) ? trim($data['description']) : '',
                'image' => isset($data['image']) ? $data['image'] : null,
                'status' => isset($data['status']) ? $data['status'] : 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Gọi phương thức create từ BaseModel
            $result = parent::create($insertData);
            
            return $result;

        } catch (Exception $e) {
            error_log("Error in Category::create(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật danh mục
     * @param int $id
     * @param array $data - ['name', 'description', 'image', 'status']
     * @return bool
     */
    public function update($id, $data)
    {
        try {
            // Kiểm tra danh mục có tồn tại không
            if (!$this->exists($id)) {
                return false;
            }

            // Chuẩn bị dữ liệu cập nhật
            $updateData = [
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (isset($data['name'])) {
                $updateData['name'] = trim($data['name']);
            }

            if (isset($data['description'])) {
                $updateData['description'] = trim($data['description']);
            }

            if (isset($data['image'])) {
                $updateData['image'] = $data['image'];
            }

            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            // Gọi phương thức update từ BaseModel
            $result = parent::update($id, $updateData);
            
            return $result;

        } catch (Exception $e) {
            error_log("Error in Category::update(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa danh mục
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            // Kiểm tra danh mục có tồn tại không
            if (!$this->exists($id)) {
                error_log("Category ID {$id} not found");
                return false;
            }

            // Kiểm tra có sản phẩm nào trong danh mục không
            $productCount = $this->countProducts($id);
            if ($productCount > 0) {
                error_log("Cannot delete category ID {$id}: {$productCount} products exist");
                return false;
            }

            // Gọi phương thức delete từ BaseModel
            return parent::delete($id);

        } catch (Exception $e) {
            error_log("Error in Category::delete(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số lượng sản phẩm trong danh mục
     * @param int $categoryId
     * @return int
     */
    public function countProducts($categoryId)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM products WHERE category_id = :category_id";
            $result = $this->queryOne($sql, ['category_id' => $categoryId]);

            return $result ? (int) $result['total'] : 0;

        } catch (Exception $e) {
            error_log("Error in Category::countProducts(): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm tổng số danh mục
     * @param string|null $status
     * @return int
     */
    public function countAll($status = null)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $params = [];

            if ($status !== null) {
                $sql .= " WHERE status = :status";
                $params['status'] = $status;
            }

            $result = $this->queryOne($sql, $params);
            return $result ? (int) $result['total'] : 0;

        } catch (Exception $e) {
            error_log("Error in Category::countAll(): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy danh mục kèm số lượng sản phẩm
     * @param string|null $status
     * @return array
     */
    public function getAllWithProductCount($status = null)
    {
        try {
            $sql = "SELECT c.*, 
                           COUNT(p.id) as product_count
                    FROM {$this->table} c
                    LEFT JOIN products p ON c.id = p.category_id";

            $params = [];

            if ($status !== null) {
                $sql .= " WHERE c.status = :status";
                $params['status'] = $status;
            }

            $sql .= " GROUP BY c.id ORDER BY c.name ASC";

            return $this->query($sql, $params);

        } catch (Exception $e) {
            error_log("Error in Category::getAllWithProductCount(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra tên danh mục đã tồn tại chưa
     * @param string $name
     * @param int|null $excludeId - ID danh mục cần loại trừ (dùng khi update)
     * @return bool
     */
    public function isNameExists($name, $excludeId = null)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE name = :name";
            $params = ['name' => trim($name)];

            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params['exclude_id'] = $excludeId;
            }

            $result = $this->queryOne($sql, $params);

            return $result && (int) $result['total'] > 0;

        } catch (Exception $e) {
            error_log("Error in Category::isNameExists(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm kiếm danh mục theo tên
     * @param string $keyword
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function search($keyword, $limit = null, $offset = null)
    {
        try {
            $sql = "SELECT c.*, 
                           COUNT(p.id) as product_count
                    FROM {$this->table} c
                    LEFT JOIN products p ON c.id = p.category_id
                    WHERE c.name LIKE :keyword OR c.description LIKE :keyword
                    GROUP BY c.id 
                    ORDER BY c.name ASC";

            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':keyword', '%' . trim($keyword) . '%', PDO::PARAM_STR);

            if ($limit !== null) {
                $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
                }
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error in Category::search(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm danh mục theo tên
     * @param string $name
     * @return array|null
     */
    public function findByName($name)
    {
        try {
            return $this->findBy(['name' => trim($name)]);
        } catch (Exception $e) {
            error_log("Error in Category::findByName(): " . $e->getMessage());
            return null;
        }
    }
}

