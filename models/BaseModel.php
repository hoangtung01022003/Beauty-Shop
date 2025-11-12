<?php
/**
 * =====================================================
 * BASE MODEL - Class Model cơ sở
 * =====================================================
 * File: models/BaseModel.php
 * Mô tả: Class cơ sở cho tất cả các Model, chứa CRUD cơ bản
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

class BaseModel {
    protected $db;           // PDO connection
    protected $table;        // Tên bảng
    protected $primaryKey = 'id';  // Primary key
    
    /**
     * Constructor - Kết nối database
     */
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Tìm record theo ID
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("Error in find(): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy tất cả records
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findAll($limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM {$this->table}";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error in findAll(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Tìm record theo điều kiện
     * @param array $conditions - ['column' => 'value']
     * @return array|null
     */
    public function findBy($conditions) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE ";
            $whereClauses = [];
            $params = [];
            
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            
            $sql .= implode(' AND ', $whereClauses) . " LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("Error in findBy(): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Tạo record mới
     * @param array $data
     * @return int|false - ID của record vừa tạo hoặc false
     */
    public function create($data) {
        try {
            $columns = array_keys($data);
            $placeholders = array_map(function($col) {
                return ":{$col}";
            }, $columns);
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->db->prepare($sql);
            
            if ($stmt->execute($data)) {
                return (int)$this->db->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error in create(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật record
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        try {
            $setClauses = [];
            $params = [];
            
            foreach ($data as $column => $value) {
                $setClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            
            $params['id'] = $id;
            
            $sql = "UPDATE {$this->table} 
                    SET " . implode(', ', $setClauses) . " 
                    WHERE {$this->primaryKey} = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Error in update(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa record
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Error in delete(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Đếm tổng số records
     * @param array|null $conditions
     * @return int
     */
    public function count($conditions = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            
            if ($conditions !== null && !empty($conditions)) {
                $sql .= " WHERE ";
                $whereClauses = [];
                
                foreach ($conditions as $column => $value) {
                    $whereClauses[] = "{$column} = :{$column}";
                }
                
                $sql .= implode(' AND ', $whereClauses);
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($conditions !== null) {
                $stmt->execute($conditions);
            } else {
                $stmt->execute();
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
            
        } catch (PDOException $e) {
            error_log("Error in count(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Query tùy chỉnh
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error in query(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Query trả về 1 record
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    protected function queryOne($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("Error in queryOne(): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Bắt đầu transaction
     * @return bool
     */
    public function beginTransaction() {
        try {
            return $this->db->beginTransaction();
        } catch (PDOException $e) {
            error_log("Error in beginTransaction(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Commit transaction
     * @return bool
     */
    public function commit() {
        try {
            return $this->db->commit();
        } catch (PDOException $e) {
            error_log("Error in commit(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rollback transaction
     * @return bool
     */
    public function rollBack() {
        try {
            return $this->db->rollBack();
        } catch (PDOException $e) {
            error_log("Error in rollBack(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kiểm tra record có tồn tại không
     * @param int $id
     * @return bool
     */
    public function exists($id) {
        $result = $this->find($id);
        return $result !== null;
    }
}
