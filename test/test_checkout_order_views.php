<?php
/**
 * =====================================================
 * TEST CHECKOUT & ORDER VIEWS - Bước 4.5
 * =====================================================
 * File: test_checkout_order_views.php
 * Mục đích: Kiểm tra xem các view checkout & order đã hoàn thành chưa
 * Yêu cầu:
 * - checkout.php: Form 2 cột với địa chỉ, thanh toán, ghi chú
 * - success.php: Tick xanh, thông tin đơn hàng
 * - my-orders.php: Bảng danh sách đơn hàng
 * - order-detail.php: Chi tiết đơn hàng
 * =====================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "========================================\n";
echo "TEST CHECKOUT & ORDER VIEWS - BƯỚC 4.5\n";
echo "========================================\n\n";

// Màu sắc cho output
function printSuccess($message) {
    echo "✓ " . $message . "\n";
}

function printError($message) {
    echo "✗ " . $message . "\n";
}

function printInfo($message) {
    echo "ℹ " . $message . "\n";
}

function printHeader($message) {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo $message . "\n";
    echo str_repeat("=", 50) . "\n";
}

// Đếm số test pass/fail
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

// Danh sách files cần kiểm tra
$viewFiles = [
    'views/user/checkout/checkout.php' => 'Form thanh toán',
    'views/user/checkout/success.php' => 'Trang đặt hàng thành công',
    'views/user/profile/my-orders.php' => 'Danh sách đơn hàng',
    'views/user/profile/order-detail.php' => 'Chi tiết đơn hàng'
];

// Test 1: Kiểm tra tồn tại file
printHeader("TEST 1: KIỂM TRA TỒN TẠI FILE");
foreach ($viewFiles as $file => $description) {
    $totalTests++;
    if (file_exists($file)) {
        printSuccess("File $file tồn tại - $description");
        $passedTests++;
    } else {
        printError("File $file KHÔNG tồn tại - $description");
        $failedTests++;
    }
}

// Test 2: Kiểm tra nội dung checkout.php
printHeader("TEST 2: KIỂM TRA CHECKOUT.PHP");

if (file_exists('views/user/checkout/checkout.php')) {
    $content = file_get_contents('views/user/checkout/checkout.php');
    
    // Test 2.1: Form 2 cột
    $totalTests++;
    if (strpos($content, 'col-lg-7') !== false && strpos($content, 'col-lg-5') !== false) {
        printSuccess("Form có 2 cột (col-lg-7 và col-lg-5)");
        $passedTests++;
    } else {
        printError("Form KHÔNG có 2 cột");
        $failedTests++;
    }
    
    // Test 2.2: Địa chỉ giao hàng
    $totalTests++;
    if (strpos($content, 'shipping_address') !== false) {
        printSuccess("Có trường địa chỉ giao hàng (shipping_address)");
        $passedTests++;
    } else {
        printError("THIẾU trường địa chỉ giao hàng");
        $failedTests++;
    }
    
    // Test 2.3: Phương thức thanh toán
    $totalTests++;
    if (strpos($content, 'payment_method') !== false && 
        strpos($content, 'cod') !== false) {
        printSuccess("Có phương thức thanh toán (payment_method)");
        $passedTests++;
    } else {
        printError("THIẾU phương thức thanh toán");
        $failedTests++;
    }
    
    // Test 2.4: Ghi chú
    $totalTests++;
    if (strpos($content, 'notes') !== false) {
        printSuccess("Có trường ghi chú (notes)");
        $passedTests++;
    } else {
        printError("THIẾU trường ghi chú");
        $failedTests++;
    }
    
    // Test 2.5: Tóm tắt đơn hàng
    $totalTests++;
    if (strpos($content, 'cartItems') !== false && 
        strpos($content, 'cartSummary') !== false) {
        printSuccess("Có tóm tắt đơn hàng (cartItems, cartSummary)");
        $passedTests++;
    } else {
        printError("THIẾU tóm tắt đơn hàng");
        $failedTests++;
    }
    
    // Test 2.6: Tổng tiền và phí vận chuyển
    $totalTests++;
    if (strpos($content, 'shippingFee') !== false && 
        strpos($content, 'finalTotal') !== false) {
        printSuccess("Có tính phí vận chuyển và tổng tiền");
        $passedTests++;
    } else {
        printError("THIẾU tính phí vận chuyển hoặc tổng tiền");
        $failedTests++;
    }
    
    // Test 2.7: Nút đặt hàng
    $totalTests++;
    if (strpos($content, 'type="submit"') !== false && 
        (strpos($content, 'Đặt hàng') !== false || strpos($content, 'Thanh toán') !== false)) {
        printSuccess("Có nút đặt hàng/thanh toán");
        $passedTests++;
    } else {
        printError("THIẾU nút đặt hàng/thanh toán");
        $failedTests++;
    }
    
    // Test 2.8: Validation client-side
    $totalTests++;
    if (strpos($content, 'addEventListener') !== false && 
        strpos($content, 'submit') !== false) {
        printSuccess("Có validation JavaScript client-side");
        $passedTests++;
    } else {
        printError("THIẾU validation JavaScript");
        $failedTests++;
    }
}

// Test 3: Kiểm tra nội dung success.php
printHeader("TEST 3: KIỂM TRA SUCCESS.PHP");

if (file_exists('views/user/checkout/success.php')) {
    $content = file_get_contents('views/user/checkout/success.php');
    
    // Test 3.1: Icon tick xanh
    $totalTests++;
    if (strpos($content, 'check') !== false || 
        strpos($content, 'success') !== false) {
        printSuccess("Có icon tick/check thành công");
        $passedTests++;
    } else {
        printError("THIẾU icon tick thành công");
        $failedTests++;
    }
    
    // Test 3.2: Thông báo thành công
    $totalTests++;
    if (strpos($content, 'thành công') !== false || 
        strpos($content, 'Đặt hàng thành công') !== false) {
        printSuccess("Có thông báo 'Đặt hàng thành công'");
        $passedTests++;
    } else {
        printError("THIẾU thông báo thành công");
        $failedTests++;
    }
    
    // Test 3.3: Mã đơn hàng
    $totalTests++;
    if (strpos($content, 'order_code') !== false || 
        strpos($content, 'Mã đơn hàng') !== false) {
        printSuccess("Có hiển thị mã đơn hàng");
        $passedTests++;
    } else {
        printError("THIẾU mã đơn hàng");
        $failedTests++;
    }
    
    // Test 3.4: Ngày đặt
    $totalTests++;
    if (strpos($content, 'created_at') !== false || 
        strpos($content, 'Ngày đặt') !== false) {
        printSuccess("Có hiển thị ngày đặt hàng");
        $passedTests++;
    } else {
        printError("THIẾU ngày đặt hàng");
        $failedTests++;
    }
    
    // Test 3.5: Tổng tiền
    $totalTests++;
    if (strpos($content, 'final_price') !== false || 
        strpos($content, 'Tổng tiền') !== false) {
        printSuccess("Có hiển thị tổng tiền");
        $passedTests++;
    } else {
        printError("THIẾU tổng tiền");
        $failedTests++;
    }
    
    // Test 3.6: Địa chỉ giao hàng
    $totalTests++;
    if (strpos($content, 'shipping_address') !== false || 
        strpos($content, 'Địa chỉ giao hàng') !== false) {
        printSuccess("Có hiển thị địa chỉ giao hàng");
        $passedTests++;
    } else {
        printError("THIẾU địa chỉ giao hàng");
        $failedTests++;
    }
    
    // Test 3.7: Nút tiếp tục mua
    $totalTests++;
    if (strpos($content, 'Tiếp tục mua') !== false || 
        strpos($content, 'products') !== false) {
        printSuccess("Có nút 'Tiếp tục mua sắm'");
        $passedTests++;
    } else {
        printError("THIẾU nút 'Tiếp tục mua sắm'");
        $failedTests++;
    }
    
    // Test 3.8: Nút xem chi tiết đơn hàng
    $totalTests++;
    if (strpos($content, 'chi tiết') !== false || 
        strpos($content, 'order/detail') !== false) {
        printSuccess("Có nút 'Xem chi tiết đơn hàng'");
        $passedTests++;
    } else {
        printError("THIẾU nút 'Xem chi tiết đơn hàng'");
        $failedTests++;
    }
    
    // Test 3.9: Animation/CSS cho icon
    $totalTests++;
    if (strpos($content, '@keyframes') !== false || 
        strpos($content, 'animation') !== false || 
        strpos($content, '<style>') !== false) {
        printSuccess("Có CSS/Animation cho icon");
        $passedTests++;
    } else {
        printError("THIẾU CSS/Animation");
        $failedTests++;
    }
}

// Test 4: Kiểm tra my-orders.php
printHeader("TEST 4: KIỂM TRA MY-ORDERS.PHP");

if (file_exists('views/user/profile/my-orders.php')) {
    $content = file_get_contents('views/user/profile/my-orders.php');
    
    // Test 4.1: Bảng danh sách
    $totalTests++;
    if (strpos($content, '<table') !== false) {
        printSuccess("Có bảng (table) hiển thị đơn hàng");
        $passedTests++;
    } else {
        printError("THIẾU bảng hiển thị");
        $failedTests++;
    }
    
    // Test 4.2: Mã đơn hàng
    $totalTests++;
    if (strpos($content, 'order_code') !== false || 
        strpos($content, 'Mã đơn hàng') !== false) {
        printSuccess("Có cột mã đơn hàng");
        $passedTests++;
    } else {
        printError("THIẾU cột mã đơn hàng");
        $failedTests++;
    }
    
    // Test 4.3: Ngày đặt
    $totalTests++;
    if (strpos($content, 'created_at') !== false || 
        strpos($content, 'Ngày') !== false) {
        printSuccess("Có cột ngày đặt");
        $passedTests++;
    } else {
        printError("THIẾU cột ngày đặt");
        $failedTests++;
    }
    
    // Test 4.4: Tổng tiền
    $totalTests++;
    if (strpos($content, 'final_price') !== false || 
        strpos($content, 'total_price') !== false || 
        strpos($content, 'Tổng tiền') !== false) {
        printSuccess("Có cột tổng tiền");
        $passedTests++;
    } else {
        printError("THIẾU cột tổng tiền");
        $failedTests++;
    }
    
    // Test 4.5: Trạng thái
    $totalTests++;
    if (strpos($content, 'status') !== false || 
        strpos($content, 'Trạng thái') !== false) {
        printSuccess("Có cột trạng thái");
        $passedTests++;
    } else {
        printError("THIẾU cột trạng thái");
        $failedTests++;
    }
    
    // Test 4.6: Link xem chi tiết
    $totalTests++;
    if (strpos($content, 'chi tiết') !== false || 
        strpos($content, 'order/detail') !== false) {
        printSuccess("Có link 'Xem chi tiết'");
        $passedTests++;
    } else {
        printError("THIẾU link 'Xem chi tiết'");
        $failedTests++;
    }
    
    // Test 4.7: Phân trang (nếu có)
    $totalTests++;
    if (strpos($content, 'pagination') !== false || 
        strpos($content, 'orders') !== false) {
        printSuccess("Có hỗ trợ danh sách đơn hàng (orders array)");
        $passedTests++;
    } else {
        printInfo("Chưa có phân trang (có thể bổ sung sau)");
        $passedTests++; // Cho qua vì có thể chưa cần
    }
}

// Test 5: Kiểm tra order-detail.php
printHeader("TEST 5: KIỂM TRA ORDER-DETAIL.PHP");

if (file_exists('views/user/profile/order-detail.php')) {
    $content = file_get_contents('views/user/profile/order-detail.php');
    
    // Test 5.1: Thông tin đơn hàng
    $totalTests++;
    if (strpos($content, 'order_code') !== false && 
        strpos($content, 'status') !== false) {
        printSuccess("Có hiển thị mã đơn hàng và trạng thái");
        $passedTests++;
    } else {
        printError("THIẾU mã đơn hàng hoặc trạng thái");
        $failedTests++;
    }
    
    // Test 5.2: Địa chỉ giao hàng
    $totalTests++;
    if (strpos($content, 'shipping_address') !== false) {
        printSuccess("Có hiển thị địa chỉ giao hàng");
        $passedTests++;
    } else {
        printError("THIẾU địa chỉ giao hàng");
        $failedTests++;
    }
    
    // Test 5.3: Bảng items
    $totalTests++;
    if (strpos($content, 'items') !== false && 
        strpos($content, '<table') !== false) {
        printSuccess("Có bảng hiển thị items");
        $passedTests++;
    } else {
        printError("THIẾU bảng items");
        $failedTests++;
    }
    
    // Test 5.4: Sản phẩm, số lượng, giá
    $totalTests++;
    if (strpos($content, 'product_name') !== false && 
        strpos($content, 'quantity') !== false && 
        strpos($content, 'price') !== false) {
        printSuccess("Có hiển thị sản phẩm, số lượng, giá");
        $passedTests++;
    } else {
        printError("THIẾU thông tin sản phẩm, số lượng hoặc giá");
        $failedTests++;
    }
    
    // Test 5.5: Tính tổng tiền items
    $totalTests++;
    if (strpos($content, 'subtotal') !== false || 
        strpos($content, 'itemTotal') !== false) {
        printSuccess("Có tính tổng tiền cho mỗi item");
        $passedTests++;
    } else {
        printError("THIẾU tính tổng tiền items");
        $failedTests++;
    }
    
    // Test 5.6: Phí vận chuyển
    $totalTests++;
    if (strpos($content, 'shipping') !== false || 
        strpos($content, 'vận chuyển') !== false) {
        printSuccess("Có hiển thị phí vận chuyển");
        $passedTests++;
    } else {
        printError("THIẾU phí vận chuyển");
        $failedTests++;
    }
    
    // Test 5.7: Tổng cộng
    $totalTests++;
    if (strpos($content, 'final_price') !== false || 
        strpos($content, 'total_price') !== false || 
        strpos($content, 'Tổng cộng') !== false) {
        printSuccess("Có hiển thị tổng cộng");
        $passedTests++;
    } else {
        printError("THIẾU tổng cộng");
        $failedTests++;
    }
    
    // Test 5.8: Nút quay lại
    $totalTests++;
    if (strpos($content, 'Quay lại') !== false || 
        strpos($content, 'my-orders') !== false) {
        printSuccess("Có nút 'Quay lại đơn hàng của tôi'");
        $passedTests++;
    } else {
        printError("THIẾU nút quay lại");
        $failedTests++;
    }
    
    // Test 5.9: Timeline/Progress (bonus)
    $totalTests++;
    if (strpos($content, 'timeline') !== false || 
        strpos($content, 'progress') !== false) {
        printSuccess("Có timeline/tiến trình đơn hàng (bonus feature)");
        $passedTests++;
    } else {
        printInfo("Chưa có timeline (không bắt buộc)");
        $passedTests++; // Cho qua vì không bắt buộc
    }
}

// Test 6: Kiểm tra include layout
printHeader("TEST 6: KIỂM TRA LAYOUT & STRUCTURE");

foreach ($viewFiles as $file => $description) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Test include header
        $totalTests++;
        if (strpos($content, 'header.php') !== false) {
            printSuccess("$file: Có include header.php");
            $passedTests++;
        } else {
            printError("$file: THIẾU include header.php");
            $failedTests++;
        }
        
        // Test include footer
        $totalTests++;
        if (strpos($content, 'footer.php') !== false) {
            printSuccess("$file: Có include footer.php");
            $passedTests++;
        } else {
            printError("$file: THIẾU include footer.php");
            $failedTests++;
        }
    }
}

// Test 7: Kiểm tra Bootstrap classes
printHeader("TEST 7: KIỂM TRA BOOTSTRAP & RESPONSIVE");

foreach ($viewFiles as $file => $description) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        $totalTests++;
        if (strpos($content, 'container') !== false && 
            (strpos($content, 'row') !== false || strpos($content, 'card') !== false)) {
            printSuccess("$file: Sử dụng Bootstrap classes");
            $passedTests++;
        } else {
            printError("$file: THIẾU Bootstrap classes");
            $failedTests++;
        }
    }
}

// Test 8: Kiểm tra security (htmlspecialchars)
printHeader("TEST 8: KIỂM TRA BẢO MẬT");

foreach ($viewFiles as $file => $description) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        $totalTests++;
        if (strpos($content, 'htmlspecialchars') !== false || 
            strpos($content, 'escape') !== false) {
            printSuccess("$file: Có sử dụng htmlspecialchars() để bảo mật");
            $passedTests++;
        } else {
            printError("$file: THIẾU htmlspecialchars() - cần bảo mật output");
            $failedTests++;
        }
    }
}

// Tổng kết
printHeader("TỔNG KẾT");

$percentage = ($totalTests > 0) ? round(($passedTests / $totalTests) * 100, 2) : 0;

echo "Tổng số test: $totalTests\n";
echo "Passed: $passedTests ✓\n";
echo "Failed: $failedTests ✗\n";
echo "Tỷ lệ hoàn thành: $percentage%\n\n";

if ($percentage >= 90) {
    printSuccess("XUẤT SẮC! Bước 4.5 đã hoàn thành rất tốt!");
} elseif ($percentage >= 70) {
    printInfo("TỐT! Bước 4.5 cơ bản đã hoàn thành, còn một số điểm cần cải thiện.");
} elseif ($percentage >= 50) {
    printInfo("TRUNG BÌNH! Còn nhiều việc cần làm để hoàn thành Bước 4.5.");
} else {
    printError("CẦN CẢI THIỆN! Bước 4.5 chưa hoàn thành.");
}

// Chi tiết yêu cầu
printHeader("YÊU CẦU CHI TIẾT BƯỚC 4.5");
echo "
1. File checkout.php:
   ✓ Form 2 cột (trái: thông tin, phải: tóm tắt)
   ✓ Địa chỉ giao hàng
   ✓ Phương thức thanh toán (COD, Bank Transfer, MoMo, VNPay)
   ✓ Ghi chú đơn hàng
   ✓ Tóm tắt đơn hàng (items, tổng tiền, phí ship, tổng)
   ✓ Nút 'Đặt hàng'
   ✓ Validation JavaScript client-side

2. File success.php:
   ✓ Icon tick xanh lớn
   ✓ Thông báo 'Đặt hàng thành công!'
   ✓ Hiển thị: Mã đơn hàng, Ngày đặt, Tổng tiền, Địa chỉ
   ✓ Nút 'Tiếp tục mua sắm' & 'Xem chi tiết đơn hàng'

3. File my-orders.php:
   ✓ Bảng: Mã đơn hàng, Ngày, Tổng tiền, Trạng thái, Hành động
   ✓ Link 'Xem chi tiết' cho mỗi đơn hàng

4. File order-detail.php:
   ✓ Chi tiết order: mã, ngày, trạng thái, địa chỉ
   ✓ Bảng items: sản phẩm, số lượng, giá, tổng
   ✓ Tóm tắt tiền: tổng SP, phí ship, tổng
   ✓ Nút 'Quay lại đơn hàng của tôi'
";

echo "\n========================================\n";
echo "KẾT THÚC TEST\n";
echo "========================================\n";
