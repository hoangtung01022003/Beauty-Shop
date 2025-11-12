<?php
/**
 * =====================================================
 * USER MODEL - Model quản lý người dùng
 * =====================================================
 * File: models/User.php
 * Mô tả: Xử lý logic CRUD cho bảng users
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

// Load BaseModel
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    /**
     * Tìm user theo username
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username) {
        return $this->findBy(['username' => $username]);
    }
    
    /**
     * Tìm user theo email
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email) {
        return $this->findBy(['email' => $email]);
    }
    
    /**
     * Tìm user theo ID
     * @param int $id
     * @return array|null
     */
    public function findById($id) {
        return $this->find($id);
    }
    
    /**
     * Lấy user theo ID (alias của findById)
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        return $this->findById($id);
    }
    
    /**
     * Tạo user mới
     * @param array $data - Dữ liệu user (username, email, password, role, phone, address)
     * @return int|false - ID của user vừa tạo hoặc false
     */
    public function create($data) {
        // Hash password nếu có
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }
        
        // Set role mặc định là 'user' nếu không có
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }
        
        return parent::create($data);
    }
    
    /**
     * Cập nhật user
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        // Hash password nếu có trong data
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }
        
        return parent::update($id, $data);
    }
    
    /**
     * Xóa user
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        return parent::delete($id);
    }
    
    /**
     * Lấy tất cả users
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getAll($limit = null, $offset = null) {
        return $this->findAll($limit, $offset);
    }
    
    /**
     * Đếm tổng số users
     * @param array|null $conditions
     * @return int
     */
    public function countAll($conditions = null) {
        return $this->count($conditions);
    }
    
    /**
     * Kiểm tra password có đúng không
     * @param string $password - Password nhập vào
     * @param string $hash - Hash từ database
     * @return bool
     */
    public function checkPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Hash password
     * @param string $password
     * @return string
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    /**
     * Lấy users theo role
     * @param string $role - 'admin' hoặc 'user'
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getUsersByRole($role, $limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE role = :role";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
            
            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error in getUsersByRole(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kiểm tra username đã tồn tại chưa
     * @param string $username
     * @param int|null $excludeId - ID user cần loại trừ (dùng khi update)
     * @return bool
     */
    public function usernameExists($username, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
            
            if ($excludeId !== null) {
                $sql .= " AND id != :excludeId";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            
            if ($excludeId !== null) {
                $stmt->bindValue(':excludeId', (int)$excludeId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error in usernameExists(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kiểm tra email đã tồn tại chưa
     * @param string $email
     * @param int|null $excludeId - ID user cần loại trừ (dùng khi update)
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
            
            if ($excludeId !== null) {
                $sql .= " AND id != :excludeId";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            
            if ($excludeId !== null) {
                $stmt->bindValue(':excludeId', (int)$excludeId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error in emailExists(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xác thực đăng nhập
     * @param string $username - Username hoặc email
     * @param string $password - Password
     * @return array|null - Thông tin user nếu đăng nhập thành công, null nếu thất bại
     */
    public function authenticate($username, $password) {
        // Tìm user theo username hoặc email
        $user = $this->findByUsername($username);
        
        if (!$user) {
            $user = $this->findByEmail($username);
        }
        
        // Không tìm thấy user
        if (!$user) {
            return null;
        }
        
        // Kiểm tra password
        if (!$this->checkPassword($password, $user['password'])) {
            return null;
        }
        
        // Xóa password khỏi kết quả trả về (bảo mật)
        unset($user['password']);
        
        return $user;
    }
    
    /**
     * Cập nhật avatar
     * @param int $id
     * @param string $avatarPath
     * @return bool
     */
    public function updateAvatar($id, $avatarPath) {
        return $this->update($id, ['avatar' => $avatarPath]);
    }
    
    /**
     * Đổi password
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function changePassword($id, $newPassword) {
        return $this->update($id, ['password' => $newPassword]);
    }
    
    /**
     * Lấy thống kê users
     * @return array
     */
    public function getStats() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admin,
                        SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as total_user,
                        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_new
                    FROM {$this->table}";
            
            $result = $this->queryOne($sql);
            return $result ?: [
                'total' => 0,
                'total_admin' => 0,
                'total_user' => 0,
                'today_new' => 0
            ];
            
        } catch (Exception $e) {
            error_log("Error in getStats(): " . $e->getMessage());
            return [
                'total' => 0,
                'total_admin' => 0,
                'total_user' => 0,
                'today_new' => 0
            ];
        }
    }
    
    /**
     * Tìm kiếm users
     * @param string $keyword
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function search($keyword, $limit = null, $offset = null) {
        try {
            $searchTerm = "%{$keyword}%";
            
            $sql = "SELECT * FROM {$this->table} 
                    WHERE username LIKE ? 
                       OR email LIKE ? 
                       OR phone LIKE ?";
            
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit;
                if ($offset !== null) {
                    $sql .= " OFFSET " . (int)$offset;
                }
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error in search(): " . $e->getMessage());
            return [];
        }
    }
}

