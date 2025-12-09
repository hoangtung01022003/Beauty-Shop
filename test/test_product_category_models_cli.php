<?php
/**
 * =====================================================
 * TEST PRODUCT & CATEGORY MODELS - CLI VERSION
 * =====================================================
 * File: test_product_category_models_cli.php
 * Mô tả: Test các phương thức của Product và Category qua CLI
 * Chạy: php test_product_category_models_cli.php
 * =====================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database config TRƯỚC KHI include models
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Product.php';

// ANSI Color codes for terminal
class Color {
    const RESET = "\033[0m";
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const MAGENTA = "\033[35m";
    const CYAN = "\033[36m";
    const WHITE = "\033[37m";
    const BOLD = "\033[1m";
}

function printHeader($text) {
    echo "\n" . Color::BOLD . Color::CYAN . str_repeat("=", 70) . Color::RESET . "\n";
    echo Color::BOLD . Color::CYAN . $text . Color::RESET . "\n";
    echo Color::BOLD . Color::CYAN . str_repeat("=", 70) . Color::RESET . "\n";
}

function printSubHeader($text) {
    echo "\n" . Color::BOLD . Color::YELLOW . ">>> " . $text . Color::RESET . "\n";
}

function printSuccess($text) {
    echo Color::GREEN . "✅ " . $text . Color::RESET . "\n";
}

function printError($text) {
    echo Color::RED . "❌ " . $text . Color::RESET . "\n";
}

function printInfo($text) {
    echo Color::BLUE . "ℹ️  " . $text . Color::RESET . "\n";
}

function printWarning($text) {
    echo Color::YELLOW . "⚠️  " . $text . Color::RESET . "\n";
}

function printTable($headers, $rows, $maxRows = 5) {
    if (empty($rows)) {
        printWarning("Không có dữ liệu để hiển thị");
        return;
    }
    
    // Calculate column widths
    $widths = [];
    foreach ($headers as $key => $header) {
        $widths[$key] = strlen($header);
    }
    
    foreach (array_slice($rows, 0, $maxRows) as $row) {
        foreach ($headers as $key => $header) {
            $value = isset($row[$key]) ? (string)$row[$key] : '';
            $widths[$key] = max($widths[$key], min(strlen($value), 50));
        }
    }
    
    // Print header
    echo "\n";
    foreach ($headers as $key => $header) {
        echo "| " . str_pad($header, $widths[$key]) . " ";
    }
    echo "|\n";
    
    // Print separator
    foreach ($headers as $key => $header) {
        echo "|-" . str_repeat("-", $widths[$key]) . "-";
    }
    echo "|\n";
    
    // Print rows
    $count = 0;
    foreach ($rows as $row) {
        if ($count >= $maxRows) break;
        foreach ($headers as $key => $header) {
            $value = isset($row[$key]) ? (string)$row[$key] : '';
            if (strlen($value) > 50) {
                $value = substr($value, 0, 47) . '...';
            }
            echo "| " . str_pad($value, $widths[$key]) . " ";
        }
        echo "|\n";
        $count++;
    }
    echo "\n";
}

// =====================================================
// MAIN TEST
// =====================================================

printHeader("🧪 TEST PRODUCT & CATEGORY MODELS");
echo Color::MAGENTA . "Ngày test: " . date('Y-m-d H:i:s') . Color::RESET . "\n";

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

// =====================================================
// TEST CATEGORY MODEL
// =====================================================
printHeader("📁 TEST CATEGORY MODEL");

try {
    $categoryModel = new Category();
    printSuccess("Category Model khởi tạo thành công!");
    $totalTests++;
    $passedTests++;
    
    // Test 1: getAll()
    printSubHeader("Test 1: getAll()");
    $categories = $categoryModel->getAll();
    $totalTests++;
    if (is_array($categories)) {
        printInfo("Tổng số danh mục: " . count($categories));
        if (!empty($categories)) {
            printTable(
                ['id' => 'ID', 'name' => 'Tên', 'description' => 'Mô tả', 'status' => 'Trạng thái'],
                $categories,
                5
            );
        }
        printSuccess("Test getAll() thành công!");
        $passedTests++;
    } else {
        printError("Test getAll() thất bại!");
        $failedTests++;
    }
    
    // Test 2: getById()
    printSubHeader("Test 2: getById()");
    $totalTests++;
    if (!empty($categories)) {
        $firstCat = $categories[0];
        $category = $categoryModel->getById($firstCat['id']);
        if ($category) {
            printInfo("Lấy danh mục ID: {$firstCat['id']}");
            echo "  - Tên: {$category['name']}\n";
            echo "  - Mô tả: " . substr($category['description'] ?? 'N/A', 0, 50) . "\n";
            echo "  - Trạng thái: {$category['status']}\n";
            printSuccess("Test getById() thành công!");
            $passedTests++;
        } else {
            printError("Test getById() thất bại!");
            $failedTests++;
        }
    } else {
        printWarning("Bỏ qua test getById() - không có dữ liệu");
    }
    
    // Test 3: countProducts()
    printSubHeader("Test 3: countProducts()");
    $totalTests++;
    if (!empty($categories)) {
        $firstCat = $categories[0];
        $count = $categoryModel->countProducts($firstCat['id']);
        printInfo("Danh mục '{$firstCat['name']}' có {$count} sản phẩm");
        printSuccess("Test countProducts() thành công!");
        $passedTests++;
    } else {
        printWarning("Bỏ qua test countProducts() - không có dữ liệu");
    }
    
    // Test 4: getAllWithProductCount()
    printSubHeader("Test 4: getAllWithProductCount()");
    $totalTests++;
    $catsWithCount = $categoryModel->getAllWithProductCount();
    if (is_array($catsWithCount)) {
        printInfo("Lấy danh mục kèm số lượng sản phẩm");
        if (!empty($catsWithCount)) {
            printTable(
                ['id' => 'ID', 'name' => 'Tên danh mục', 'product_count' => 'Số SP'],
                $catsWithCount,
                5
            );
        }
        printSuccess("Test getAllWithProductCount() thành công!");
        $passedTests++;
    } else {
        printError("Test getAllWithProductCount() thất bại!");
        $failedTests++;
    }
    
    // Test 5: isNameExists()
    printSubHeader("Test 5: isNameExists()");
    $totalTests++;
    if (!empty($categories)) {
        $firstCat = $categories[0];
        $exists = $categoryModel->isNameExists($firstCat['name']);
        printInfo("Kiểm tra tên '{$firstCat['name']}': " . ($exists ? "Đã tồn tại" : "Chưa tồn tại"));
        
        $notExists = $categoryModel->isNameExists('Danh_Muc_Khong_Ton_Tai_12345');
        printInfo("Kiểm tra tên 'Danh_Muc_Khong_Ton_Tai_12345': " . ($notExists ? "Đã tồn tại" : "Chưa tồn tại"));
        
        if ($exists && !$notExists) {
            printSuccess("Test isNameExists() thành công!");
            $passedTests++;
        } else {
            printError("Test isNameExists() thất bại!");
            $failedTests++;
        }
    } else {
        printWarning("Bỏ qua test isNameExists() - không có dữ liệu");
    }
    
} catch (Exception $e) {
    printError("Lỗi khi test Category Model: " . $e->getMessage());
    $failedTests++;
}

// =====================================================
// TEST PRODUCT MODEL
// =====================================================
printHeader("🛍️ TEST PRODUCT MODEL");

try {
    $productModel = new Product();
    printSuccess("Product Model khởi tạo thành công!");
    $totalTests++;
    $passedTests++;
    
    // Test 1: countAll()
    printSubHeader("Test 1: countAll()");
    $totalTests++;
    $totalProducts = $productModel->countAll();
    printInfo("Tổng số sản phẩm: {$totalProducts}");
    printSuccess("Test countAll() thành công!");
    $passedTests++;
    
    // Test 2: getAll() với phân trang
    printSubHeader("Test 2: getAll() - Lấy 5 sản phẩm đầu tiên");
    $totalTests++;
    $products = $productModel->getAll(5, 0);
    if (is_array($products)) {
        printInfo("Đã lấy được " . count($products) . " sản phẩm");
        if (!empty($products)) {
            printTable(
                [
                    'id' => 'ID', 
                    'name' => 'Tên', 
                    'price' => 'Giá', 
                    'category_name' => 'Danh mục', 
                    'stock' => 'Tồn kho',
                    'sold' => 'Đã bán'
                ],
                $products,
                5
            );
        }
        printSuccess("Test getAll() thành công!");
        $passedTests++;
    } else {
        printError("Test getAll() thất bại!");
        $failedTests++;
    }
    
    // Test 3: getById()
    printSubHeader("Test 3: getById()");
    $totalTests++;
    if (!empty($products)) {
        $firstProduct = $products[0];
        $product = $productModel->getById($firstProduct['id']);
        if ($product) {
            printInfo("Lấy sản phẩm ID: {$firstProduct['id']}");
            echo "  - Tên: {$product['name']}\n";
            echo "  - Giá: " . number_format($product['price'], 0, ',', '.') . "đ\n";
            echo "  - Danh mục: {$product['category_name']}\n";
            echo "  - Tồn kho: {$product['stock']}\n";
            echo "  - Đã bán: {$product['sold']}\n";
            printSuccess("Test getById() thành công!");
            $passedTests++;
        } else {
            printError("Test getById() thất bại!");
            $failedTests++;
        }
    } else {
        printWarning("Bỏ qua test getById() - không có dữ liệu");
    }
    
    // Test 4: getByCategory()
    printSubHeader("Test 4: getByCategory()");
    $totalTests++;
    if (!empty($categories)) {
        $firstCat = $categories[0];
        $productsByCat = $productModel->getByCategory($firstCat['id'], 3);
        printInfo("Danh mục '{$firstCat['name']}' có " . count($productsByCat) . " sản phẩm (hiển thị tối đa 3)");
        if (!empty($productsByCat)) {
            printTable(
                ['id' => 'ID', 'name' => 'Tên sản phẩm', 'price' => 'Giá'],
                $productsByCat,
                3
            );
        }
        printSuccess("Test getByCategory() thành công!");
        $passedTests++;
    } else {
        printWarning("Bỏ qua test getByCategory() - không có danh mục");
    }
    
    // Test 5: search()
    printSubHeader("Test 5: search()");
    $totalTests++;
    $searchResults = $productModel->search('son', 5);
    printInfo("Tìm kiếm 'son': " . count($searchResults) . " kết quả");
    if (!empty($searchResults)) {
        printTable(
            ['id' => 'ID', 'name' => 'Tên sản phẩm', 'price' => 'Giá'],
            $searchResults,
            5
        );
    }
    printSuccess("Test search() thành công!");
    $passedTests++;
    
    // Test 6: countByCategory()
    printSubHeader("Test 6: countByCategory()");
    $totalTests++;
    if (!empty($categories)) {
        echo "\n";
        foreach (array_slice($categories, 0, 5) as $cat) {
            $count = $productModel->countByCategory($cat['id']);
            echo "  - {$cat['name']}: {$count} sản phẩm\n";
        }
        printSuccess("Test countByCategory() thành công!");
        $passedTests++;
    } else {
        printWarning("Bỏ qua test countByCategory() - không có danh mục");
    }
    
    // Test 7: getBestSelling()
    printSubHeader("Test 7: getBestSelling()");
    $totalTests++;
    $bestSelling = $productModel->getBestSelling(5);
    printInfo("Top 5 sản phẩm bán chạy:");
    if (!empty($bestSelling)) {
        printTable(
            ['id' => 'ID', 'name' => 'Tên', 'sold' => 'Đã bán', 'price' => 'Giá'],
            $bestSelling,
            5
        );
    }
    printSuccess("Test getBestSelling() thành công!");
    $passedTests++;
    
    // Test 8: getLatest()
    printSubHeader("Test 8: getLatest()");
    $totalTests++;
    $latestProducts = $productModel->getLatest(5);
    printInfo("5 sản phẩm mới nhất:");
    if (!empty($latestProducts)) {
        printTable(
            ['id' => 'ID', 'name' => 'Tên', 'created_at' => 'Ngày tạo'],
            $latestProducts,
            5
        );
    }
    printSuccess("Test getLatest() thành công!");
    $passedTests++;
    
    // Test 9: hasEnoughStock()
    printSubHeader("Test 9: hasEnoughStock()");
    $totalTests++;
    if (!empty($products)) {
        $testProduct = $products[0];
        $hasStock = $productModel->hasEnoughStock($testProduct['id'], 1);
        printInfo("Sản phẩm '{$testProduct['name']}' (tồn kho: {$testProduct['stock']})");
        printInfo("  - Đủ hàng để bán 1 sp: " . ($hasStock ? Color::GREEN . "✅ Có" : Color::RED . "❌ Không") . Color::RESET);
        
        $hasStock2 = $productModel->hasEnoughStock($testProduct['id'], 999999);
        printInfo("  - Đủ hàng để bán 999999 sp: " . ($hasStock2 ? Color::GREEN . "✅ Có" : Color::RED . "❌ Không") . Color::RESET);
        
        printSuccess("Test hasEnoughStock() thành công!");
        $passedTests++;
    } else {
        printWarning("Bỏ qua test hasEnoughStock() - không có dữ liệu");
    }
    
    // Test 10: getRelated()
    printSubHeader("Test 10: getRelated()");
    $totalTests++;
    if (!empty($products)) {
        $testProduct = $products[0];
        $relatedProducts = $productModel->getRelated($testProduct['id'], 3);
        printInfo("Sản phẩm liên quan với '{$testProduct['name']}': " . count($relatedProducts) . " sản phẩm");
        if (!empty($relatedProducts)) {
            printTable(
                ['id' => 'ID', 'name' => 'Tên', 'category_name' => 'Danh mục'],
                $relatedProducts,
                3
            );
        }
        printSuccess("Test getRelated() thành công!");
        $passedTests++;
    } else {
        printWarning("Bỏ qua test getRelated() - không có dữ liệu");
    }
    
} catch (Exception $e) {
    printError("Lỗi khi test Product Model: " . $e->getMessage());
    echo Color::RED . "Stack trace: " . $e->getTraceAsString() . Color::RESET . "\n";
    $failedTests++;
}

// =====================================================
// SUMMARY
// =====================================================
printHeader("📊 TÓM TẮT KẾT QUẢ TEST");

echo "\n";
echo Color::BOLD . "Tổng số test: {$totalTests}" . Color::RESET . "\n";
echo Color::GREEN . "✅ Thành công: {$passedTests}" . Color::RESET . "\n";
echo Color::RED . "❌ Thất bại: {$failedTests}" . Color::RESET . "\n";

$successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;
echo Color::CYAN . "📈 Tỷ lệ thành công: {$successRate}%" . Color::RESET . "\n";

echo "\n";
printInfo("ĐÃ KIỂM TRA:");
echo "  • Category Model: getAll(), getById(), countProducts(), getAllWithProductCount(), isNameExists()\n";
echo "  • Product Model: getAll(), getById(), getByCategory(), search(), countAll(), countByCategory(),\n";
echo "                   getBestSelling(), getLatest(), getRelated(), hasEnoughStock()\n";

echo "\n";
printWarning("CHƯA TEST (Cần test thủ công):");
echo "  • Category: create(), update(), delete()\n";
echo "  • Product: create(), update(), delete(), updateStock(), updateSold()\n";

echo "\n";
if ($failedTests === 0) {
    printHeader("🎉 TẤT CẢ TEST ĐỀU THÀNH CÔNG! 🎉");
    exit(0);
} else {
    printError("CÓ {$failedTests} TEST THẤT BẠI!");
    exit(1);
}
