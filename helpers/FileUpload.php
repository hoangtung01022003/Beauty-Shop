<?php
/**
 * =====================================================
 * FILE UPLOAD HELPER - Hỗ trợ upload file
 * =====================================================
 * File: helpers/FileUpload.php
 * Mô tả: Các hàm xử lý upload file, hình ảnh
 * Ngày tạo: 11/11/2025
 * =====================================================
 */

/**
 * Upload file lên server
 * @param array $file - $_FILES['fieldname']
 * @param string $destination - Thư mục đích
 * @param array $allowedTypes - Mảng MIME types cho phép
 * @param int $maxSize - Kích thước tối đa (bytes)
 * @return array - ['success' => bool, 'message' => string, 'filename' => string]
 */
function uploadFile($file, $destination, $allowedTypes = [], $maxSize = 5242880) {
    // Validate file
    $validation = validateFileUpload($file, $allowedTypes, $maxSize);
    
    if (!$validation['valid']) {
        return [
            'success' => false,
            'message' => $validation['message'],
            'filename' => null
        ];
    }
    
    // Tạo tên file unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = rtrim($destination, '/') . '/' . $filename;
    
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Di chuyển file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'message' => 'Upload file thành công',
            'filename' => $filename
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Không thể upload file',
        'filename' => null
    ];
}

/**
 * Upload hình ảnh
 * @param array $file - $_FILES['fieldname']
 * @param string $destination - Thư mục đích
 * @param int $maxSize - Kích thước tối đa (bytes)
 * @param bool $resize - Có resize ảnh không
 * @param int $maxWidth - Chiều rộng tối đa (nếu resize)
 * @param int $maxHeight - Chiều cao tối đa (nếu resize)
 * @return array - ['success' => bool, 'message' => string, 'filename' => string]
 */
function uploadImage($file, $destination, $maxSize = 5242880, $resize = false, $maxWidth = 1200, $maxHeight = 1200) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    
    // Upload file
    $result = uploadFile($file, $destination, $allowedTypes, $maxSize);
    
    if (!$result['success']) {
        return $result;
    }
    
    // Resize ảnh nếu cần
    if ($resize) {
        $filepath = rtrim($destination, '/') . '/' . $result['filename'];
        $resized = resizeImage($filepath, $maxWidth, $maxHeight);
        
        if (!$resized) {
            return [
                'success' => false,
                'message' => 'Không thể resize ảnh',
                'filename' => null
            ];
        }
    }
    
    return $result;
}

/**
 * Resize hình ảnh
 * @param string $filepath - Đường dẫn file ảnh
 * @param int $maxWidth - Chiều rộng tối đa
 * @param int $maxHeight - Chiều cao tối đa
 * @return bool
 */
function resizeImage($filepath, $maxWidth = 1200, $maxHeight = 1200) {
    if (!file_exists($filepath)) {
        return false;
    }
    
    // Lấy thông tin ảnh
    list($width, $height, $type) = getimagesize($filepath);
    
    // Nếu ảnh nhỏ hơn kích thước tối đa thì không cần resize
    if ($width <= $maxWidth && $height <= $maxHeight) {
        return true;
    }
    
    // Tính tỷ lệ resize
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = (int)($width * $ratio);
    $newHeight = (int)($height * $ratio);
    
    // Tạo ảnh từ file gốc
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($filepath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($filepath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($filepath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($filepath);
            break;
        default:
            return false;
    }
    
    // Tạo ảnh mới với kích thước đã resize
    $destination = imagecreatetruecolor($newWidth, $newHeight);
    
    // Giữ transparency cho PNG và GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Copy và resize
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Lưu ảnh đã resize
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($destination, $filepath, 90);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($destination, $filepath, 9);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($destination, $filepath);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($destination, $filepath, 90);
            break;
        default:
            $result = false;
    }
    
    // Giải phóng bộ nhớ
    imagedestroy($source);
    imagedestroy($destination);
    
    return $result;
}

/**
 * Xóa file
 * @param string $filepath - Đường dẫn file cần xóa
 * @return bool
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Tạo thumbnail từ ảnh
 * @param string $sourcePath - Đường dẫn ảnh gốc
 * @param string $thumbPath - Đường dẫn lưu thumbnail
 * @param int $thumbWidth - Chiều rộng thumbnail
 * @param int $thumbHeight - Chiều cao thumbnail
 * @return bool
 */
function createThumbnail($sourcePath, $thumbPath, $thumbWidth = 300, $thumbHeight = 300) {
    if (!file_exists($sourcePath)) {
        return false;
    }
    
    // Lấy thông tin ảnh
    list($width, $height, $type) = getimagesize($sourcePath);
    
    // Tạo ảnh từ file gốc
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    // Tính tỷ lệ crop
    $sourceRatio = $width / $height;
    $thumbRatio = $thumbWidth / $thumbHeight;
    
    if ($sourceRatio > $thumbRatio) {
        // Ảnh gốc rộng hơn
        $newHeight = $height;
        $newWidth = (int)($height * $thumbRatio);
        $cropX = (int)(($width - $newWidth) / 2);
        $cropY = 0;
    } else {
        // Ảnh gốc cao hơn
        $newWidth = $width;
        $newHeight = (int)($width / $thumbRatio);
        $cropX = 0;
        $cropY = (int)(($height - $newHeight) / 2);
    }
    
    // Tạo thumbnail
    $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    
    // Giữ transparency
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $thumbWidth, $thumbHeight, $transparent);
    }
    
    // Copy và resize
    imagecopyresampled($thumb, $source, 0, 0, $cropX, $cropY, $thumbWidth, $thumbHeight, $newWidth, $newHeight);
    
    // Tạo thư mục nếu chưa tồn tại
    $thumbDir = dirname($thumbPath);
    if (!file_exists($thumbDir)) {
        mkdir($thumbDir, 0755, true);
    }
    
    // Lưu thumbnail
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($thumb, $thumbPath, 90);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($thumb, $thumbPath, 9);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($thumb, $thumbPath);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($thumb, $thumbPath, 90);
            break;
        default:
            $result = false;
    }
    
    // Giải phóng bộ nhớ
    imagedestroy($source);
    imagedestroy($thumb);
    
    return $result;
}

/**
 * Upload nhiều file
 * @param array $files - $_FILES['fieldname'] (multiple)
 * @param string $destination - Thư mục đích
 * @param array $allowedTypes - Mảng MIME types cho phép
 * @param int $maxSize - Kích thước tối đa (bytes)
 * @return array - Mảng kết quả upload
 */
function uploadMultipleFiles($files, $destination, $allowedTypes = [], $maxSize = 5242880) {
    $results = [];
    
    // Kiểm tra format của $_FILES multiple
    if (!isset($files['name']) || !is_array($files['name'])) {
        return $results;
    }
    
    $fileCount = count($files['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        // Tạo lại format chuẩn cho mỗi file
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];
        
        $results[] = uploadFile($file, $destination, $allowedTypes, $maxSize);
    }
    
    return $results;
}

/**
 * Lấy kích thước file theo định dạng dễ đọc
 * @param int $bytes - Kích thước file (bytes)
 * @param int $precision - Số chữ số thập phân
 * @return string
 */
function formatFileSize($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Lấy extension từ tên file
 * @param string $filename - Tên file
 * @return string
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Kiểm tra file có phải ảnh không
 * @param string $filepath - Đường dẫn file
 * @return bool
 */
function isImage($filepath) {
    if (!file_exists($filepath)) {
        return false;
    }
    
    $imageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
    $imageInfo = getimagesize($filepath);
    
    return $imageInfo !== false && in_array($imageInfo[2], $imageTypes);
}

