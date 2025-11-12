<?php
/**
 * =====================================================
 * TEST FILE UPLOAD HELPER - Upload thực tế
 * =====================================================
 * File: test_upload_real.php
 * Mô tả: Test upload file thật với form HTML
 * =====================================================
 */

require_once 'config/constants.php';
require_once 'helpers/FileUpload.php';
require_once 'helpers/Validator.php';
require_once 'helpers/Helper.php';

// Tạo thư mục uploads nếu chưa tồn tại
$uploadDir = 'uploads/test';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Test Upload File Thực Tế</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px dashed #4CAF50;
            border-radius: 5px;
            background: #f9f9f9;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background: #45a049;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
        }
        .preview {
            margin-top: 20px;
            text-align: center;
        }
        .preview img {
            max-width: 100%;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
        }
        .file-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .file-info p {
            margin: 5px 0;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 TEST UPLOAD FILE THỰC TẾ</h1>

        <?php
        // Xử lý upload khi submit form
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
            echo "<h2>📊 KẾT QUẢ UPLOAD</h2>";
            
            $file = $_FILES['image'];
            
            // Hiển thị thông tin file
            echo "<div class='file-info'>";
            echo "<h3>📄 Thông tin file gốc:</h3>";
            echo "<p><strong>Tên file:</strong> " . e($file['name']) . "</p>";
            echo "<p><strong>Loại file:</strong> " . e($file['type']) . "</p>";
            echo "<p><strong>Kích thước:</strong> " . formatFileSize($file['size']) . "</p>";
            echo "<p><strong>Error code:</strong> " . $file['error'] . "</p>";
            echo "</div>";
            
            // Test 1: Validate file
            echo "<h3>1️⃣ Test validateImageUpload()</h3>";
            $validation = validateImageUpload($file, 5 * 1024 * 1024); // 5MB
            
            if ($validation['valid']) {
                echo "<div class='success'>✅ Validation: HỢP LỆ</div>";
            } else {
                echo "<div class='error'>❌ Validation: " . e($validation['message']) . "</div>";
            }
            
            // Test 2: Upload file (không resize)
            if ($validation['valid']) {
                echo "<h3>2️⃣ Test uploadImage() - Không resize</h3>";
                $result1 = uploadImage($file, $uploadDir, 5 * 1024 * 1024, false);
                
                if ($result1['success']) {
                    echo "<div class='success'>";
                    echo "✅ Upload thành công!<br>";
                    echo "<strong>Tên file:</strong> " . e($result1['filename']) . "<br>";
                    echo "<strong>Đường dẫn:</strong> " . $uploadDir . "/" . e($result1['filename']);
                    echo "</div>";
                    
                    $filepath1 = $uploadDir . "/" . $result1['filename'];
                    if (file_exists($filepath1)) {
                        echo "<div class='preview'>";
                        echo "<h4>🖼️ Preview ảnh gốc:</h4>";
                        echo "<img src='{$filepath1}' alt='Uploaded Image'>";
                        
                        list($width, $height) = getimagesize($filepath1);
                        echo "<p><strong>Kích thước:</strong> {$width} × {$height} px</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='error'>❌ Upload thất bại: " . e($result1['message']) . "</div>";
                }
            }
            
            // Test 3: Upload và resize
            if ($validation['valid']) {
                echo "<hr>";
                echo "<h3>3️⃣ Test uploadImage() - Có resize (800×800)</h3>";
                
                // Upload lại file (cần reset tmp_name)
                $result2 = uploadImage($file, $uploadDir, 5 * 1024 * 1024, true, 800, 800);
                
                if ($result2['success']) {
                    echo "<div class='success'>";
                    echo "✅ Upload + Resize thành công!<br>";
                    echo "<strong>Tên file:</strong> " . e($result2['filename']) . "<br>";
                    echo "<strong>Đường dẫn:</strong> " . $uploadDir . "/" . e($result2['filename']);
                    echo "</div>";
                    
                    $filepath2 = $uploadDir . "/" . $result2['filename'];
                    if (file_exists($filepath2)) {
                        echo "<div class='preview'>";
                        echo "<h4>🖼️ Preview ảnh đã resize:</h4>";
                        echo "<img src='{$filepath2}' alt='Resized Image'>";
                        
                        list($width, $height) = getimagesize($filepath2);
                        echo "<p><strong>Kích thước:</strong> {$width} × {$height} px (Max: 800×800)</p>";
                        echo "</div>";
                    }
                }
            }
            
            // Test 4: Tạo thumbnail
            if (isset($filepath1) && file_exists($filepath1)) {
                echo "<hr>";
                echo "<h3>4️⃣ Test createThumbnail() - 300×300px</h3>";
                
                $thumbPath = $uploadDir . "/thumb_" . $result1['filename'];
                $thumbResult = createThumbnail($filepath1, $thumbPath, 300, 300);
                
                if ($thumbResult) {
                    echo "<div class='success'>✅ Tạo thumbnail thành công!</div>";
                    echo "<div class='preview'>";
                    echo "<h4>🖼️ Preview thumbnail:</h4>";
                    echo "<img src='{$thumbPath}' alt='Thumbnail'>";
                    
                    list($width, $height) = getimagesize($thumbPath);
                    echo "<p><strong>Kích thước:</strong> {$width} × {$height} px</p>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>❌ Không thể tạo thumbnail</div>";
                }
            }
            
            // Test 5: Kiểm tra file có phải ảnh không
            if (isset($filepath1)) {
                echo "<hr>";
                echo "<h3>5️⃣ Test isImage()</h3>";
                $isImg = isImage($filepath1);
                echo "<p>✅ isImage('{$filepath1}'): " . ($isImg ? "✅ ĐÚng, là ảnh" : "❌ KHÔNG phải ảnh") . "</p>";
            }
            
            echo "<hr>";
        }
        ?>

        <!-- Form upload -->
        <h2>📤 Upload Ảnh Để Test</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Chọn ảnh để upload (JPG, PNG, GIF, WEBP):</label>
                <input type="file" name="image" id="image" accept="image/*" required>
            </div>
            <button type="submit">🚀 Upload và Test</button>
        </form>

        <div class="info">
            <h3>ℹ️ Thông tin test:</h3>
            <ul>
                <li>✅ Chỉ chấp nhận file ảnh: JPG, PNG, GIF, WEBP</li>
                <li>✅ Kích thước tối đa: 5MB</li>
                <li>✅ Test sẽ tạo 3 file:
                    <ul>
                        <li>File gốc (không resize)</li>
                        <li>File resize (max 800×800px)</li>
                        <li>Thumbnail (300×300px, crop center)</li>
                    </ul>
                </li>
                <li>✅ Tất cả file sẽ được lưu trong thư mục: <code><?php echo $uploadDir; ?></code></li>
            </ul>
        </div>

        <hr>

        <h2>🧪 Test Các Hàm Helper Khác</h2>
        
        <h3>Test formatFileSize()</h3>
        <?php
        $sizes = [1024, 1048576, 5242880, 1073741824, 5368709120];
        foreach ($sizes as $size) {
            echo "<p>✅ formatFileSize({$size}): <strong>" . formatFileSize($size) . "</strong></p>";
        }
        ?>

        <h3>Test getFileExtension()</h3>
        <?php
        $files = ['image.jpg', 'document.PDF', 'video.MP4', 'archive.ZIP'];
        foreach ($files as $file) {
            echo "<p>✅ getFileExtension('{$file}'): <strong>" . getFileExtension($file) . "</strong></p>";
        }
        ?>

        <h3>Test isImage() với các file</h3>
        <?php
        // Kiểm tra các file trong thư mục uploads/test
        if (file_exists($uploadDir)) {
            $testFiles = glob($uploadDir . "/*.*");
            if (!empty($testFiles)) {
                echo "<p><strong>Các file trong thư mục test:</strong></p>";
                foreach ($testFiles as $testFile) {
                    $isImg = isImage($testFile);
                    $icon = $isImg ? "🖼️" : "📄";
                    $status = $isImg ? "✅ Là ảnh" : "❌ Không phải ảnh";
                    echo "<p>{$icon} " . basename($testFile) . ": <strong>{$status}</strong></p>";
                }
            } else {
                echo "<p><em>Chưa có file nào trong thư mục test. Hãy upload ảnh để test!</em></p>";
            }
        }
        ?>
    </div>
</body>
</html>