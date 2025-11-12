<?php
/**
 * =====================================================
 * HELPER FUNCTIONS - Hàm tiện ích chung
 * =====================================================
 * File: helpers/Helper.php
 * Mô tả: Các hàm hỗ trợ tái sử dụng trong toàn bộ dự án
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

/**
 * Chuyển hướng đến URL
 * @param string $url - URL cần chuyển đến
 * @param int $statusCode - Mã HTTP status code
 * @return void
 */
function redirect($url, $statusCode = 302) {
    header("Location: " . $url, true, $statusCode);
    exit();
}

/**
 * Lấy URL gốc của website
 * @param string $path - Đường dẫn bổ sung
 * @return string
 */
function base_url($path = '') {
    $baseUrl = BASE_URL ?? 'http://localhost/WebBanMyPham';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Lấy đường dẫn tài nguyên (CSS, JS, Images)
 * @param string $path - Đường dẫn file tài nguyên
 * @return string
 */
function asset($path) {
    return base_url('public/' . ltrim($path, '/'));
}

/**
 * Debug Dump - In ra dữ liệu và dừng chương trình
 * @param mixed $data - Dữ liệu cần debug
 * @return void
 */
function dd($data) {
    echo '<pre style="background: #1e1e1e; color: #dcdcdc; padding: 20px; border-radius: 5px; margin: 20px; font-family: monospace;">';
    print_r($data);
    echo '</pre>';
    die();
}

/**
 * Debug Dump nhưng không dừng chương trình
 * @param mixed $data - Dữ liệu cần debug
 * @return void
 */
function dump($data) {
    echo '<pre style="background: #1e1e1e; color: #dcdcdc; padding: 20px; border-radius: 5px; margin: 20px; font-family: monospace;">';
    print_r($data);
    echo '</pre>';
}

/**
 * Định dạng tiền tệ (VNĐ)
 * @param float|int $price - Số tiền
 * @param string $currency - Ký hiệu tiền tệ
 * @return string
 */
function formatPrice($price, $currency = 'đ') {
    return number_format($price, 0, ',', '.') . $currency;
}

/**
 * Định dạng ngày tháng
 * @param string $date - Ngày cần format
 * @param string $format - Định dạng mong muốn
 * @return string
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (empty($date)) {
        return '';
    }
    
    try {
        $datetime = new DateTime($date);
        return $datetime->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Tạo slug từ chuỗi tiếng Việt
 * @param string $text - Chuỗi cần chuyển thành slug
 * @return string
 */
function slugify($text) {
    // Chuyển thành chữ thường
    $text = mb_strtolower($text, 'UTF-8');
    
    // Bảng chuyển đổi ký tự có dấu
    $vietnameseMap = [
        'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
        'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
        'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
        'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
        'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
        'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
        'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
        'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
        'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
        'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
        'đ' => 'd'
    ];
    
    // Thay thế ký tự có dấu
    $text = strtr($text, $vietnameseMap);
    
    // Xóa ký tự đặc biệt, chỉ giữ a-z, 0-9, khoảng trắng
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    
    // Thay khoảng trắng bằng dấu gạch ngang
    $text = preg_replace('/[\s-]+/', '-', $text);
    
    // Xóa dấu gạch ngang ở đầu và cuối
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Lấy thông báo flash từ session
 * @param string $key - Key của flash message
 * @return string|null
 */
function getFlash($key) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    return null;
}

/**
 * Đặt thông báo flash vào session
 * @param string $key - Key của flash message
 * @param string $message - Nội dung thông báo
 * @return void
 */
function setFlash($key, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['flash'][$key] = $message;
}

/**
 * Escape HTML để tránh XSS
 * @param string $text - Chuỗi cần escape
 * @return string
 */
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Lấy giá trị từ mảng, trả về default nếu không tồn tại
 * @param array $array - Mảng cần lấy giá trị
 * @param string $key - Key cần lấy
 * @param mixed $default - Giá trị mặc định
 * @return mixed
 */
function array_get($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Cắt ngắn chuỗi
 * @param string $text - Chuỗi cần cắt
 * @param int $length - Độ dài tối đa
 * @param string $suffix - Chuỗi thêm vào cuối
 * @return string
 */
function str_limit($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

/**
 * Kiểm tra request method
 * @param string $method - Method cần kiểm tra (GET, POST, PUT, DELETE)
 * @return bool
 */
function isMethod($method) {
    return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
}

/**
 * Lấy giá trị từ $_POST
 * @param string $key - Key cần lấy
 * @param mixed $default - Giá trị mặc định
 * @return mixed
 */
function post($key, $default = null) {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * Lấy giá trị từ $_GET
 * @param string $key - Key cần lấy
 * @param mixed $default - Giá trị mặc định
 * @return mixed
 */
function get($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

/**
 * Tạo URL với query string
 * @param string $url - URL gốc
 * @param array $params - Mảng tham số
 * @return string
 */
function url_with_query($url, $params = []) {
    if (empty($params)) {
        return $url;
    }
    
    $query = http_build_query($params);
    $separator = strpos($url, '?') !== false ? '&' : '?';
    
    return $url . $separator . $query;
}

/**
 * Tạo URL ảnh placeholder SVG
 * @param int $width - Chiều rộng
 * @param int $height - Chiều cao
 * @param string $text - Text hiển thị
 * @param string $bgColor - Màu nền (hex không cần #)
 * @param string $textColor - Màu chữ (hex không cần #)
 * @return string - Data URL của SVG
 */
function placeholder_image($width = 300, $height = 300, $text = 'No Image', $bgColor = 'cccccc', $textColor = '666666') {
    $svg = <<<SVG
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="#{$bgColor}"/>
    <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="18" fill="#{$textColor}" text-anchor="middle" dominant-baseline="middle">{$text}</text>
</svg>
SVG;
    
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/**
 * Lấy URL ảnh sản phẩm (fallback sang placeholder nếu không có)
 * @param string|null $imagePath - Đường dẫn ảnh
 * @param string $alt - Alt text cho placeholder
 * @return string - URL ảnh
 */
function product_image($imagePath, $alt = 'Product') {
    if (empty($imagePath) || !file_exists($imagePath)) {
        return placeholder_image(300, 300, $alt);
    }
    return base_url($imagePath);
}

/**
 * HTML img tag với placeholder fallback
 * @param string|null $imagePath
 * @param string $alt
 * @param string $class
 * @param int $width
 * @param int $height
 * @return string
 */
function image_tag($imagePath, $alt = 'Image', $class = '', $width = 300, $height = 300) {
    $src = empty($imagePath) || !file_exists($imagePath) 
        ? placeholder_image($width, $height, $alt)
        : base_url($imagePath);
    
    return '<img src="' . $src . '" alt="' . htmlspecialchars($alt) . '" class="' . $class . '">';
}

/**
 * Kiểm tra file ảnh có tồn tại không
 * @param string|null $imagePath - Đường dẫn tương đối (vd: 'public/images/products/abc.jpg')
 * @return bool
 */
function image_exists($imagePath) {
    if (empty($imagePath)) {
        return false;
    }
    
    // Loại bỏ BASE_URL nếu có trong path
    $cleanPath = str_replace(BASE_URL, '', $imagePath);
    $cleanPath = ltrim($cleanPath, '/');
    
    // Tạo đường dẫn tuyệt đối từ root project
    $absolutePath = __DIR__ . '/../' . $cleanPath;
    
    return file_exists($absolutePath) && is_file($absolutePath);
}

/**
 * Lấy URL ảnh với fallback placeholder SVG
 * @param string|null $imagePath - Đường dẫn ảnh
 * @param string $placeholderText - Text hiển thị trên placeholder
 * @param int $width - Chiều rộng placeholder
 * @param int $height - Chiều cao placeholder
 * @return string - URL ảnh hoặc data URL của SVG
 */
function get_image_url($imagePath, $placeholderText = 'No Image', $width = 300, $height = 300) {
    if (image_exists($imagePath)) {
        return base_url($imagePath);
    }
    return placeholder_image($width, $height, $placeholderText);
}

