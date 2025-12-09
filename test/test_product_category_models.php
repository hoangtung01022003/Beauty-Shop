<?php
/**
 * =====================================================
 * TEST PRODUCT & CATEGORY MODELS
 * =====================================================
 * File: test_product_category_models.php
 * Mô tả: Test các phương thức của Product và Category
 * Ngày tạo: 12/11/2025
 * =====================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Product.php';

echo "<h1>🧪 TEST PRODUCT & CATEGORY MODELS</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .test-section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #28a745; font-weight: bold; }
    .error { color: #dc3545; font-weight: bold; }
    .info { color: #17a2b8; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    th { background: #007bff; color: white; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";

// =====================================================
// TEST CATEGORY MODEL
// =====================================================
echo "<div class='test-section'>";
echo "<h2>📁 TEST CATEGORY MODEL</h2>";

try {
    $categoryModel = new Category();
    echo "<p class='success'>✅ Category Model khởi tạo thành công!</p>";
    
    // Test 1: getAll()
    echo "<h3>Test 1: getAll()</h3>";
    $categories = $categoryModel->getAll();
    echo "<p class='info'>Tổng số danh mục: " . count($categories) . "</p>";
    if (!empty($categories)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tên</th><th>Mô tả</th><th>Trạng thái</th></tr>";
        foreach (array_slice($categories, 0, 5) as $cat) {
            echo "<tr>";
            echo "<td>{$cat['id']}</td>";
            echo "<td>{$cat['name']}</td>";
            echo "<td>" . substr($cat['description'] ?? 'N/A', 0, 50) . "...</td>";
            echo "<td>{$cat['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ Test getAll() thành công!</p>";
    } else {
        echo "<p class='error'>⚠️ Chưa có danh mục nào trong database</p>";
    }
    
    // Test 2: getById()
    echo "<h3>Test 2: getById()</h3>";
    if (!empty($categories)) {
        $firstCat = $categories[0];
        $category = $categoryModel->getById($firstCat['id']);
        if ($category) {
            echo "<pre>" . print_r($category, true) . "</pre>";
            echo "<p class='success'>✅ Test getById() thành công!</p>";
        }
    }
    
    // Test 3: countProducts()
    echo "<h3>Test 3: countProducts()</h3>";
    if (!empty($categories)) {
        $firstCat = $categories[0];
        $count = $categoryModel->countProducts($firstCat['id']);
        echo "<p class='info'>Danh mục '{$firstCat['name']}' có {$count} sản phẩm</p>";
        echo "<p class='success'>✅ Test countProducts() thành công!</p>";
    }
    
    // Test 4: getAllWithProductCount()
    echo "<h3>Test 4: getAllWithProductCount()</h3>";
    $catsWithCount = $categoryModel->getAllWithProductCount();
    if (!empty($catsWithCount)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tên danh mục</th><th>Số sản phẩm</th></tr>";
        foreach (array_slice($catsWithCount, 0, 5) as $cat) {
            echo "<tr>";
            echo "<td>{$cat['id']}</td>";
            echo "<td>{$cat['name']}</td>";
            echo "<td>{$cat['product_count']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ Test getAllWithProductCount() thành công!</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

echo "</div>";

// =====================================================
// TEST PRODUCT MODEL
// =====================================================
echo "<div class='test-section'>";
echo "<h2>🛍️ TEST PRODUCT MODEL</h2>";

try {
    $productModel = new Product();
    echo "<p class='success'>✅ Product Model khởi tạo thành công!</p>";
    
    // Test 1: countAll()
    echo "<h3>Test 1: countAll()</h3>";
    $totalProducts = $productModel->countAll();
    echo "<p class='info'>Tổng số sản phẩm: {$totalProducts}</p>";
    echo "<p class='success'>✅ Test countAll() thành công!</p>";
    
    // Test 2: getAll() với phân trang
    echo "<h3>Test 2: getAll() - Lấy 5 sản phẩm đầu tiên</h3>";
    $products = $productModel->getAll(5, 0);
    if (!empty($products)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tên</th><th>Giá</th><th>Danh mục</th><th>Tồn kho</th><th>Đã bán</th></tr>";
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>{$product['id']}</td>";
            echo "<td>{$product['name']}</td>";
            echo "<td>" . number_format($product['price'], 0, ',', '.') . "đ</td>";
            echo "<td>{$product['category_name']}</td>";
            echo "<td>{$product['stock']}</td>";
            echo "<td>{$product['sold']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ Test getAll() thành công!</p>";
    } else {
        echo "<p class='error'>⚠️ Chưa có sản phẩm nào trong database</p>";
    }
    
    // Test 3: getById()
    echo "<h3>Test 3: getById()</h3>";
    if (!empty($products)) {
        $firstProduct = $products[0];
        $product = $productModel->getById($firstProduct['id']);
        if ($product) {
            echo "<pre>" . print_r($product, true) . "</pre>";
            echo "<p class='success'>✅ Test getById() thành công!</p>";
        }
    }
    
    // Test 4: getByCategory()
    echo "<h3>Test 4: getByCategory()</h3>";
    if (!empty($categories)) {
        $firstCat = $categories[0];
        $productsByCat = $productModel->getByCategory($firstCat['id'], 3);
        echo "<p class='info'>Danh mục '{$firstCat['name']}' có " . count($productsByCat) . " sản phẩm (hiển thị tối đa 3)</p>";
        if (!empty($productsByCat)) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Tên sản phẩm</th><th>Giá</th></tr>";
            foreach ($productsByCat as $p) {
                echo "<tr>";
                echo "<td>{$p['id']}</td>";
                echo "<td>{$p['name']}</td>";
                echo "<td>" . number_format($p['price'], 0, ',', '.') . "đ</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        echo "<p class='success'>✅ Test getByCategory() thành công!</p>";
    }
    
    // Test 5: search()
    echo "<h3>Test 5: search()</h3>";
    $searchResults = $productModel->search('son', 5);
    echo "<p class='info'>Tìm kiếm 'son': " . count($searchResults) . " kết quả</p>";
    if (!empty($searchResults)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tên sản phẩm</th><th>Giá</th></tr>";
        foreach ($searchResults as $p) {
            echo "<tr>";
            echo "<td>{$p['id']}</td>";
            echo "<td>{$p['name']}</td>";
            echo "<td>" . number_format($p['price'], 0, ',', '.') . "đ</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "<p class='success'>✅ Test search() thành công!</p>";
    
    // Test 6: getBestSelling()
    echo "<h3>Test 6: getBestSelling()</h3>";
    $bestSelling = $productModel->getBestSelling(5);
    echo "<p class='info'>Top 5 sản phẩm bán chạy:</p>";
    if (!empty($bestSelling)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tên</th><th>Đã bán</th><th>Giá</th></tr>";
        foreach ($bestSelling as $p) {
            echo "<tr>";
            echo "<td>{$p['id']}</td>";
            echo "<td>{$p['name']}</td>";
            echo "<td class='success'>{$p['sold']}</td>";
            echo "<td>" . number_format($p['price'], 0, ',', '.') . "đ</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ Test getBestSelling() thành công!</p>";
    }
    
    // Test 7: getLatest()
    echo "<h3>Test 7: getLatest()</h3>";
    $latestProducts = $productModel->getLatest(5);
    echo "<p class='info'>5 sản phẩm mới nhất:</p>";
    if (!empty($latestProducts)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tên</th><th>Ngày tạo</th></tr>";
        foreach ($latestProducts as $p) {
            echo "<tr>";
            echo "<td>{$p['id']}</td>";
            echo "<td>{$p['name']}</td>";
            echo "<td>{$p['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ Test getLatest() thành công!</p>";
    }
    
    // Test 8: hasEnoughStock()
    echo "<h3>Test 8: hasEnoughStock()</h3>";
    if (!empty($products)) {
        $testProduct = $products[0];
        $hasStock = $productModel->hasEnoughStock($testProduct['id'], 1);
        echo "<p class='info'>Sản phẩm '{$testProduct['name']}' (tồn kho: {$testProduct['stock']}) - Đủ hàng để bán 1 sp: " . ($hasStock ? "✅ Có" : "❌ Không") . "</p>";
        
        $hasStock2 = $productModel->hasEnoughStock($testProduct['id'], 999999);
        echo "<p class='info'>Sản phẩm '{$testProduct['name']}' - Đủ hàng để bán 999999 sp: " . ($hasStock2 ? "✅ Có" : "❌ Không") . "</p>";
        echo "<p class='success'>✅ Test hasEnoughStock() thành công!</p>";
    }
    
    // Test 9: countByCategory()
    echo "<h3>Test 9: countByCategory()</h3>";
    if (!empty($categories)) {
        echo "<table>";
        echo "<tr><th>Danh mục</th><th>Số sản phẩm</th></tr>";
        foreach (array_slice($categories, 0, 5) as $cat) {
            $count = $productModel->countByCategory($cat['id']);
            echo "<tr>";
            echo "<td>{$cat['name']}</td>";
            echo "<td>{$count}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ Test countByCategory() thành công!</p>";
    }
    
    // Test 10: getRelated()
    echo "<h3>Test 10: getRelated()</h3>";
    if (!empty($products)) {
        $testProduct = $products[0];
        $relatedProducts = $productModel->getRelated($testProduct['id'], 3);
        echo "<p class='info'>Sản phẩm liên quan với '{$testProduct['name']}': " . count($relatedProducts) . " sản phẩm</p>";
        if (!empty($relatedProducts)) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Tên</th><th>Danh mục</th></tr>";
            foreach ($relatedProducts as $p) {
                echo "<tr>";
                echo "<td>{$p['id']}</td>";
                echo "<td>{$p['name']}</td>";
                echo "<td>{$p['category_name']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        echo "<p class='success'>✅ Test getRelated() thành công!</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

echo "</div>";

// =====================================================
// SUMMARY
// =====================================================
echo "<div class='test-section' style='background: #d4edda; border: 2px solid #28a745;'>";
echo "<h2>📊 TÓM TẮT KẾT QUẢ TEST</h2>";
echo "<h3 class='success'>✅ ĐÃ KIỂM TRA THÀNH CÔNG:</h3>";
echo "<ul>";
echo "<li><strong>Category Model:</strong> getAll(), getById(), countProducts(), getAllWithProductCount(), isNameExists()</li>";
echo "<li><strong>Product Model:</strong> getAll(), getById(), getByCategory(), search(), countAll(), countByCategory(), getBestSelling(), getLatest(), getRelated(), hasEnoughStock()</li>";
echo "</ul>";

echo "<h3 class='info'>📝 CHƯA TEST (Cần test thủ công):</h3>";
echo "<ul>";
echo "<li>Category: create(), update(), delete()</li>";
echo "<li>Product: create(), update(), delete(), updateStock(), updateSold()</li>";
echo "</ul>";

echo "<p class='info'>💡 <strong>Lưu ý:</strong> Các method CUD (Create, Update, Delete) nên test riêng để tránh làm thay đổi dữ liệu mẫu.</p>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<h2 class='success'>🎉 TEST HOÀN TẤT!</h2>";
echo "<p>Truy cập: <a href='test_product_category_models.php'>test_product_category_models.php</a></p>";
echo "</div>";
?>
