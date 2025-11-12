<?php
/**
 * =====================================================
 * BASE CONTROLLER - Controller cơ sở
 * =====================================================
 * File: controllers/BaseController.php
 * Mô tả: Class cơ sở cho tất cả controllers
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

class BaseController {
    
    /**
     * Render view (alias của render method)
     * @param string $view - Đường dẫn view
     * @param array $data - Dữ liệu truyền vào view
     * @return void
     */
    protected function view($view, $data = []) {
        return $this->render($view, $data);
    }
    
    /**
     * Render view
     * @param string $view - Đường dẫn view (vd: 'auth/login', 'user/home')
     * @param array $data - Dữ liệu truyền vào view
     * @return void
     */
    protected function render($view, $data = []) {
        // Extract data để sử dụng trong view
        extract($data);
        
        // Đường dẫn đến file view
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        // Kiểm tra file view có tồn tại không
        if (!file_exists($viewPath)) {
            die("View không tồn tại: {$view}");
        }
        
        // Include view
        require_once $viewPath;
    }
    
    /**
     * Render view với layout (header, footer)
     * @param string $view - Đường dẫn view
     * @param array $data - Dữ liệu truyền vào view
     * @param string $layout - Layout sử dụng (default: 'main')
     * @return void
     */
    protected function renderWithLayout($view, $data = [], $layout = 'main') {
        // Extract data
        extract($data);
        
        // Bắt đầu output buffering
        ob_start();
        
        // Include view content
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        }
        
        // Lấy nội dung view
        $content = ob_get_clean();
        
        // Include layout
        $layoutPath = __DIR__ . '/../views/layouts/' . $layout . '.php';
        if (file_exists($layoutPath)) {
            require_once $layoutPath;
        } else {
            echo $content;
        }
    }
    
    /**
     * Redirect đến URL
     * @param string $url - URL cần chuyển đến
     * @return void
     */
    protected function redirect($url) {
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Trả về JSON response
     * @param mixed $data - Dữ liệu trả về
     * @param int $statusCode - HTTP status code
     * @return void
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Trả về JSON response (format chuẩn)
     * @param bool $success - Trạng thái thành công
     * @param string $message - Thông báo
     * @param array $data - Dữ liệu bổ sung
     * @return void
     */
    protected function jsonResponse($success, $message, $data = []) {
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    /**
     * Kiểm tra request có phải AJAX không
     * @return bool
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Load model
     * @param string $modelName - Tên model (vd: 'User', 'Product')
     * @return object
     */
    protected function loadModel($modelName) {
        $modelPath = __DIR__ . '/../models/' . $modelName . '.php';
        
        if (!file_exists($modelPath)) {
            die("Model không tồn tại: {$modelName}");
        }
        
        require_once $modelPath;
        
        return new $modelName();
    }
    
    /**
     * Kiểm tra request method
     * @param string $method - GET, POST, PUT, DELETE
     * @return bool
     */
    protected function isMethod($method) {
        return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
    }
    
    /**
     * Lấy input từ POST
     * @param string $key - Key của input
     * @param mixed $default - Giá trị mặc định
     * @return mixed
     */
    protected function input($key, $default = null) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }
    
    /**
     * Lấy tất cả input từ POST
     * @return array
     */
    protected function allInput() {
        return $_POST;
    }
    
    /**
     * Set flash message
     * @param string $type - success, error, warning, info
     * @param string $message - Nội dung message
     * @return void
     */
    protected function setFlashMessage($type, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['flash'][$type] = $message;
    }
    
    /**
     * Get flash message
     * @param string $type - success, error, warning, info
     * @return string|null
     */
    protected function getFlashMessage($type) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        
        return null;
    }
    
    /**
     * Validate dữ liệu
     * @param array $data - Dữ liệu cần validate
     * @param array $rules - Rules validate
     * @return array - ['valid' => bool, 'errors' => array]
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule) {
                // Parse rule
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($value) && $value !== '0') {
                            $errors[$field][] = ucfirst($field) . ' là bắt buộc';
                        }
                        break;
                        
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . ' không hợp lệ';
                        }
                        break;
                        
                    case 'min':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $errors[$field][] = ucfirst($field) . " phải có ít nhất {$ruleValue} ký tự";
                        }
                        break;
                        
                    case 'max':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $errors[$field][] = ucfirst($field) . " không được vượt quá {$ruleValue} ký tự";
                        }
                        break;
                        
                    case 'match':
                        if (!empty($value) && $value !== ($data[$ruleValue] ?? null)) {
                            $errors[$field][] = ucfirst($field) . " không khớp";
                        }
                        break;
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
