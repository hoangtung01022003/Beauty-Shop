<?php
/**
 * =====================================================
 * ORDER ADMIN CONTROLLER
 * =====================================================
 * File: controllers/Admin/OrderAdminController.php
 * Mô tả: Quản lý đơn hàng cho admin
 * =====================================================
 */

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/OrderItem.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../config/database.php';

class OrderAdminController extends BaseController
{
    private $orderModel;
    private $orderItemModel;
    private $userModel;

    public function __construct()
    {
        // Kiểm tra quyền admin
        requireAdmin();

        $this->orderModel = new Order();
        $this->orderItemModel = new OrderItem();
        $this->userModel = new User();
    }

    /**
     * Danh sách đơn hàng - có phân trang, tìm kiếm, lọc
     */
    public function index()
    {
        try {
            // Lấy tham số
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            // Tìm kiếm và lọc
            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
            $status = isset($_GET['status']) ? trim($_GET['status']) : '';
            $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
            $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

            // Lấy danh sách đơn hàng
            if (!empty($keyword) || !empty($status) || !empty($dateFrom)) {
                // Tìm kiếm có điều kiện
                $orders = $this->orderModel->searchOrders($keyword, $status, $dateFrom, $dateTo, $perPage, $offset);
                $totalOrders = $this->orderModel->countSearchOrders($keyword, $status, $dateFrom, $dateTo);
            } else {
                // Lấy tất cả
                $orders = $this->orderModel->getAllWithDetails($perPage, $offset);
                $totalOrders = $this->orderModel->count();
            }

            // Tính tổng số trang
            $totalPages = ceil($totalOrders / $perPage);

            // Lấy thống kê
            $stats = [
                'total' => $this->orderModel->count(),
                'pending' => $this->orderModel->countByStatus('pending'),
                'processing' => $this->orderModel->countByStatus('processing'),
                'completed' => $this->orderModel->countByStatus('completed'),
                'cancelled' => $this->orderModel->countByStatus('cancelled'),
                'today_revenue' => $this->orderModel->getTodayRevenue(),
                'total_revenue' => $this->orderModel->getTotalRevenue()
            ];

            $data = [
                'pageTitle' => 'Quản Lý Đơn Hàng',
                'orders' => $orders,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalOrders' => $totalOrders,
                'keyword' => $keyword,
                'selectedStatus' => $status,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'stats' => $stats
            ];

            $this->view('admin/orders/list', $data);

        } catch (Exception $e) {
            error_log("Error in OrderAdminController::index(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect(base_url('admin/dashboard'));
        }
    }

    /**
     * Chi tiết đơn hàng
     */
    public function detail($id = null)
    {
        try {
            if (!$id) {
                $this->setFlashMessage('error', 'ID đơn hàng không hợp lệ');
                redirect(base_url('admin/orders'));
                return;
            }

            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getById($id);

            if (!$order) {
                $this->setFlashMessage('error', 'Không tìm thấy đơn hàng');
                redirect(base_url('admin/orders'));
                return;
            }

            // Lấy thông tin khách hàng
            $user = $this->userModel->findById($order['user_id']);

            // Lấy chi tiết items
            $orderItems = $this->orderItemModel->getByOrderId($id);

            $data = [
                'pageTitle' => 'Chi Tiết Đơn Hàng #' . $order['order_code'],
                'order' => $order,
                'user' => $user,
                'orderItems' => $orderItems
            ];

            $this->view('admin/orders/detail', $data);

        } catch (Exception $e) {
            error_log("Error in OrderAdminController::detail(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url('admin/orders'));
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng (AJAX)
     */
    public function updateStatus($id = null)
    {
        try {
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID đơn hàng không hợp lệ']);
                return;
            }

            // Chỉ nhận POST request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            // Lấy dữ liệu JSON
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!$data) {
                // Fallback to POST data
                $data = $_POST;
            }

            $newStatus = isset($data['status']) ? trim($data['status']) : '';

            // Validate status - CẬP NHẬT TRẠNG THÁI MỚI
            $validStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($newStatus, $validStatuses)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
                return;
            }

            // Kiểm tra đơn hàng tồn tại
            $order = $this->orderModel->getById($id);
            if (!$order) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
                return;
            }

            // Cập nhật trạng thái
            $result = $this->orderModel->updateStatus($id, $newStatus);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thành công',
                    'new_status' => $newStatus,
                    'status_text' => $this->getStatusText($newStatus)
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái']);
            }

        } catch (Exception $e) {
            error_log("Error in OrderAdminController::updateStatus(): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa đơn hàng (với xác nhận)
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                $this->setFlashMessage('error', 'ID đơn hàng không hợp lệ');
                redirect(base_url('admin/orders'));
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->setFlashMessage('error', 'Invalid request method');
                redirect(base_url('admin/orders'));
                return;
            }

            $order = $this->orderModel->getById($id);
            if (!$order) {
                $this->setFlashMessage('error', 'Không tìm thấy đơn hàng');
                redirect(base_url('admin/orders'));
                return;
            }

            // Chỉ cho phép xóa đơn hàng đã hủy hoặc hoàn thành (tùy logic nghiệp vụ)
            if (!in_array($order['status'], ['cancelled'])) {
                $this->setFlashMessage('error', 'Chỉ có thể xóa đơn hàng đã hủy');
                redirect(base_url('admin/orders'));
                return;
            }

            // Xóa order items trước
            $this->orderItemModel->deleteByOrderId($id);
            
            // Xóa order
            $result = $this->orderModel->delete($id);

            if ($result) {
                $this->setFlashMessage('success', 'Xóa đơn hàng thành công');
            } else {
                $this->setFlashMessage('error', 'Không thể xóa đơn hàng');
            }

            redirect(base_url('admin/orders'));

        } catch (Exception $e) {
            error_log("Error in OrderAdminController::delete(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra');
            redirect(base_url('admin/orders'));
        }
    }

    /**
     * Form sửa đơn hàng (chỉ cho phép sửa đơn chờ xử lý)
     */
    public function edit($id = null)
    {
        try {
            if (!$id) {
                $_SESSION['flash_message'] = 'ID đơn hàng không hợp lệ';
                $_SESSION['flash_type'] = 'error';
                redirect(base_url('admin/orders'));
                return;
            }

            $order = $this->orderModel->getById($id);

            if (!$order) {
                $_SESSION['flash_message'] = 'Không tìm thấy đơn hàng';
                $_SESSION['flash_type'] = 'error';
                redirect(base_url('admin/orders'));
                return;
            }

            // Chỉ cho phép sửa đơn chờ xử lý
            if ($order['status'] !== 'pending') {
                $_SESSION['flash_message'] = 'Chỉ có thể sửa đơn hàng ở trạng thái "Chờ xử lý"';
                $_SESSION['flash_type'] = 'warning';
                redirect(base_url('admin/orders/detail/' . $id));
                return;
            }

            // Lấy thông tin khách hàng
            $user = $this->userModel->findById($order['user_id']);

            // Lấy chi tiết items
            $orderItems = $this->orderItemModel->getByOrderId($id);
            
            // KIỂM TRA: Nếu đơn hàng không có sản phẩm nào thì không cho sửa
            if (empty($orderItems)) {
                $_SESSION['flash_message'] = 'Đơn hàng này không có sản phẩm nào. Không thể chỉnh sửa!';
                $_SESSION['flash_type'] = 'error';
                redirect(base_url('admin/orders/detail/' . $id));
                return;
            }

            $data = [
                'pageTitle' => 'Sửa Đơn Hàng #' . $order['order_code'],
                'order' => $order,
                'user' => $user,
                'orderItems' => $orderItems
            ];

            $this->view('admin/orders/edit', $data);

        } catch (Exception $e) {
            error_log("Error in OrderAdminController::edit(): " . $e->getMessage());
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'error';
            redirect(base_url('admin/orders'));
        }
    }

    /**
     * Xử lý cập nhật đơn hàng
     */
    public function update($id = null)
    {
        try {
            if (!$id) {
                $_SESSION['flash_message'] = 'ID đơn hàng không hợp lệ';
                $_SESSION['flash_type'] = 'error';
                redirect(base_url('admin/orders'));
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['flash_message'] = 'Invalid request method';
                $_SESSION['flash_type'] = 'error';
                redirect(base_url('admin/orders'));
                return;
            }

            $order = $this->orderModel->getById($id);
            if (!$order) {
                $_SESSION['flash_message'] = 'Không tìm thấy đơn hàng';
                $_SESSION['flash_type'] = 'error';
                redirect(base_url('admin/orders'));
                return;
            }

            // Chỉ cho phép sửa đơn chờ xử lý
            if ($order['status'] !== 'pending') {
                $_SESSION['flash_message'] = 'Chỉ có thể sửa đơn hàng ở trạng thái "Chờ xử lý"';
                $_SESSION['flash_type'] = 'warning';
                redirect(base_url('admin/orders/detail/' . $id));
                return;
            }

            // Validate input
            $errors = [];
            $status = isset($_POST['status']) ? trim($_POST['status']) : '';
            $paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
            $shippingAddress = isset($_POST['shipping_address']) ? trim($_POST['shipping_address']) : '';
            $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
            $discount = isset($_POST['discount']) ? (float)$_POST['discount'] : 0;
            $items = isset($_POST['items']) ? $_POST['items'] : [];

            if (empty($status)) {
                $errors[] = 'Vui lòng chọn trạng thái';
            }
            if (empty($paymentMethod)) {
                $errors[] = 'Vui lòng chọn phương thức thanh toán';
            }
            if (empty($shippingAddress)) {
                $errors[] = 'Vui lòng nhập địa chỉ giao hàng';
            }
            if (empty($items)) {
                $errors[] = 'Đơn hàng phải có ít nhất 1 sản phẩm';
            }

            if (!empty($errors)) {
                $user = $this->userModel->findById($order['user_id']);
                $orderItems = $this->orderItemModel->getByOrderId($id);
                
                $data = [
                    'pageTitle' => 'Sửa Đơn Hàng #' . $order['order_code'],
                    'order' => $order,
                    'user' => $user,
                    'orderItems' => $orderItems,
                    'errors' => $errors
                ];
                
                $this->view('admin/orders/edit', $data);
                return;
            }

            // Tính tổng tiền mới
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += (float)$item['price'] * (int)$item['quantity'];
            }

            $finalPrice = $totalAmount - $discount;

            // Bắt đầu transaction
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            try {
                // Cập nhật order
                $sql = "UPDATE orders SET 
                        status = :status,
                        payment_method = :payment_method,
                        shipping_address = :shipping_address,
                        notes = :notes,
                        total_amount = :total_amount,
                        discount = :discount,
                        final_price = :final_price,
                        updated_at = NOW()
                        WHERE id = :id";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'status' => $status,
                    'payment_method' => $paymentMethod,
                    'shipping_address' => $shippingAddress,
                    'notes' => $notes,
                    'total_amount' => $totalAmount,
                    'discount' => $discount,
                    'final_price' => $finalPrice,
                    'id' => $id
                ]);

                // Xóa order items cũ
                $this->orderItemModel->deleteByOrderId($id);

                // Thêm order items mới
                foreach ($items as $item) {
                    $this->orderItemModel->create([
                        'order_id' => $id,
                        'product_id' => (int)$item['product_id'],
                        'quantity' => (int)$item['quantity'],
                        'price' => (float)$item['price']
                    ]);
                }

                $db->commit();

                $_SESSION['flash_message'] = 'Cập nhật đơn hàng thành công!';
                $_SESSION['flash_type'] = 'success';
                redirect(base_url('admin/orders/detail/' . $id));

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Error in OrderAdminController::update(): " . $e->getMessage());
            $_SESSION['flash_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
            redirect(base_url('admin/orders/edit/' . $id));
        }
    }

    /**
     * Lấy text hiển thị của status
     */
    private function getStatusText($status = '')
    {
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipped' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy'
        ];

        return $statusMap[$status] ?? $status;
    }

    /**
     * Export đơn hàng ra CSV (bonus feature)
     */
    public function export()
    {
        try {
            $orders = $this->orderModel->getAllWithDetails();

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=orders_' . date('Y-m-d_His') . '.csv');

            $output = fopen('php://output', 'w');

            // BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($output, ['ID', 'Mã đơn', 'Khách hàng', 'Email', 'Số điện thoại', 'Tổng tiền', 'Trạng thái', 'Ngày đặt']);

            // Data rows
            foreach ($orders as $order) {
                fputcsv($output, [
                    $order['id'],
                    $order['order_code'],
                    $order['customer_name'] ?? $order['username'],
                    $order['customer_email'] ?? $order['email'],
                    $order['customer_phone'] ?? '',
                    number_format($order['total_amount'], 0, ',', '.'),
                    $this->getStatusText($order['status']),
                    date('d/m/Y H:i', strtotime($order['created_at']))
                ]);
            }

            fclose($output);
            exit;

        } catch (Exception $e) {
            error_log("Error in OrderAdminController::export(): " . $e->getMessage());
            $this->setFlashMessage('error', 'Không thể export dữ liệu');
            redirect(base_url('admin/orders'));
        }
    }
}

