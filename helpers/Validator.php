<?php
/**
 * =====================================================
 * VALIDATOR - Kiểm tra và làm sạch dữ liệu
 * =====================================================
 * File: helpers/Validator.php
 * Mô tả: Các hàm validate và sanitize dữ liệu
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

/**
 * Validate email
 * @param string $email - Email cần kiểm tra
 * @return bool
 */
function validateEmail($email) {
    if (empty($email)) {
        return false;
    }
    
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate username
 * @param string $username - Username cần kiểm tra
 * @param int $minLength - Độ dài tối thiểu
 * @param int $maxLength - Độ dài tối đa
 * @return array - ['valid' => bool, 'message' => string]
 */
function validateUsername($username, $minLength = 3, $maxLength = 50) {
    if (empty($username)) {
        return [
            'valid' => false,
            'message' => 'Username không được để trống'
        ];
    }
    
    $length = strlen($username);
    
    if ($length < $minLength) {
        return [
            'valid' => false,
            'message' => "Username phải có ít nhất {$minLength} ký tự"
        ];
    }
    
    if ($length > $maxLength) {
        return [
            'valid' => false,
            'message' => "Username không được vượt quá {$maxLength} ký tự"
        ];
    }
    
    // Chỉ cho phép chữ cái, số, gạch dưới
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return [
            'valid' => false,
            'message' => 'Username chỉ được chứa chữ cái, số và dấu gạch dưới'
        ];
    }
    
    return [
        'valid' => true,
        'message' => ''
    ];
}

/**
 * Validate password
 * @param string $password - Password cần kiểm tra
 * @param int $minLength - Độ dài tối thiểu
 * @return array - ['valid' => bool, 'message' => string]
 */
function validatePassword($password, $minLength = 6) {
    if (empty($password)) {
        return [
            'valid' => false,
            'message' => 'Mật khẩu không được để trống'
        ];
    }
    
    if (strlen($password) < $minLength) {
        return [
            'valid' => false,
            'message' => "Mật khẩu phải có ít nhất {$minLength} ký tự"
        ];
    }
    
    return [
        'valid' => true,
        'message' => ''
    ];
}

/**
 * Validate password mạnh (có chữ hoa, chữ thường, số, ký tự đặc biệt)
 * @param string $password - Password cần kiểm tra
 * @return array - ['valid' => bool, 'message' => string]
 */
function validateStrongPassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Mật khẩu phải có ít nhất 1 chữ hoa';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Mật khẩu phải có ít nhất 1 chữ thường';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Mật khẩu phải có ít nhất 1 chữ số';
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt';
    }
    
    if (empty($errors)) {
        return [
            'valid' => true,
            'message' => ''
        ];
    }
    
    return [
        'valid' => false,
        'message' => implode(', ', $errors)
    ];
}

/**
 * Validate số điện thoại Việt Nam
 * @param string $phone - Số điện thoại
 * @return bool
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return false;
    }
    
    // Loại bỏ khoảng trắng, dấu gạch ngang
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
    
    // Kiểm tra format số điện thoại VN (10 số, bắt đầu bằng 0)
    return preg_match('/^(0|\+84)[0-9]{9}$/', $phone) === 1;
}

/**
 * Validate URL
 * @param string $url - URL cần kiểm tra
 * @return bool
 */
function validateUrl($url) {
    if (empty($url)) {
        return false;
    }
    
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate số nguyên
 * @param mixed $value - Giá trị cần kiểm tra
 * @param int $min - Giá trị tối thiểu
 * @param int $max - Giá trị tối đa
 * @return bool
 */
function validateInteger($value, $min = null, $max = null) {
    if (!is_numeric($value)) {
        return false;
    }
    
    $intValue = (int)$value;
    
    if ($min !== null && $intValue < $min) {
        return false;
    }
    
    if ($max !== null && $intValue > $max) {
        return false;
    }
    
    return true;
}

/**
 * Validate số thực (float/decimal)
 * @param mixed $value - Giá trị cần kiểm tra
 * @param float $min - Giá trị tối thiểu
 * @param float $max - Giá trị tối đa
 * @return bool
 */
function validateFloat($value, $min = null, $max = null) {
    if (!is_numeric($value)) {
        return false;
    }
    
    $floatValue = (float)$value;
    
    if ($min !== null && $floatValue < $min) {
        return false;
    }
    
    if ($max !== null && $floatValue > $max) {
        return false;
    }
    
    return true;
}

/**
 * Validate ngày tháng
 * @param string $date - Ngày cần kiểm tra
 * @param string $format - Format ngày (mặc định: Y-m-d)
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d') {
    if (empty($date)) {
        return false;
    }
    
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Sanitize (làm sạch) dữ liệu - Loại bỏ HTML tags và ký tự đặc biệt
 * @param mixed $data - Dữ liệu cần sanitize
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    
    if (is_string($data)) {
        // Loại bỏ khoảng trắng thừa
        $data = trim($data);
        
        // Loại bỏ HTML tags
        $data = strip_tags($data);
        
        // Escape ký tự đặc biệt
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Sanitize HTML - Cho phép một số thẻ HTML an toàn
 * @param string $html - HTML cần sanitize
 * @param array $allowedTags - Các thẻ được phép
 * @return string
 */
function sanitizeHtml($html, $allowedTags = ['p', 'br', 'b', 'i', 'u', 'strong', 'em', 'a']) {
    if (empty($html)) {
        return '';
    }
    
    $allowedTagsString = '<' . implode('><', $allowedTags) . '>';
    return strip_tags($html, $allowedTagsString);
}

/**
 * Validate file upload
 * @param array $file - $_FILES['fieldname']
 * @param array $allowedTypes - Mảng MIME types cho phép
 * @param int $maxSize - Kích thước tối đa (bytes)
 * @return array - ['valid' => bool, 'message' => string]
 */
function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
    // Kiểm tra có lỗi upload không
    if (!isset($file['error']) || is_array($file['error'])) {
        return [
            'valid' => false,
            'message' => 'Có lỗi khi upload file'
        ];
    }
    
    // Kiểm tra mã lỗi
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return [
                'valid' => false,
                'message' => 'Không có file được upload'
            ];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return [
                'valid' => false,
                'message' => 'File quá lớn'
            ];
        default:
            return [
                'valid' => false,
                'message' => 'Lỗi không xác định khi upload'
            ];
    }
    
    // Kiểm tra kích thước file
    if ($file['size'] > $maxSize) {
        $maxSizeMB = round($maxSize / 1048576, 2);
        return [
            'valid' => false,
            'message' => "File không được vượt quá {$maxSizeMB}MB"
        ];
    }
    
    // Kiểm tra MIME type
    if (!empty($allowedTypes)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return [
                'valid' => false,
                'message' => 'Định dạng file không được hỗ trợ'
            ];
        }
    }
    
    return [
        'valid' => true,
        'message' => ''
    ];
}

/**
 * Validate image upload
 * @param array $file - $_FILES['fieldname']
 * @param int $maxSize - Kích thước tối đa (bytes)
 * @return array - ['valid' => bool, 'message' => string]
 */
function validateImageUpload($file, $maxSize = 5242880) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    return validateFileUpload($file, $allowedTypes, $maxSize);
}

/**
 * Kiểm tra required fields
 * @param array $data - Mảng dữ liệu cần kiểm tra
 * @param array $requiredFields - Mảng các field bắt buộc
 * @return array - ['valid' => bool, 'missing' => array]
 */
function validateRequired($data, $requiredFields) {
    $missing = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missing[] = $field;
        }
    }
    
    return [
        'valid' => empty($missing),
        'missing' => $missing
    ];
}

/**
 * Validate dữ liệu theo rules
 * @param array $data - Dữ liệu cần validate
 * @param array $rules - Rules validate
 * @return array - ['valid' => bool, 'errors' => array]
 * 
 * Ví dụ sử dụng:
 * $rules = [
 *     'email' => ['required', 'email'],
 *     'age' => ['required', 'integer', 'min:18', 'max:100']
 * ];
 */
function validate($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $fieldRules) {
        $value = $data[$field] ?? null;
        
        foreach ($fieldRules as $rule) {
            // Parse rule (ví dụ: "min:18" => ['min', '18'])
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
                    if (!empty($value) && !validateEmail($value)) {
                        $errors[$field][] = ucfirst($field) . ' không hợp lệ';
                    }
                    break;
                    
                case 'integer':
                    if (!empty($value) && !validateInteger($value)) {
                        $errors[$field][] = ucfirst($field) . ' phải là số nguyên';
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
            }
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

