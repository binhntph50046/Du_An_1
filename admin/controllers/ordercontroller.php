<?php
class OrderController {
    private $orderModel;
    public function __construct() {
        $this->orderModel = new OrderModel();
    }
    // Hiển thị danh sách đơn hàng
    public function index() {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        if (!empty($keyword)) {
            $orders = $this->orderModel->searchOrders($keyword);
        } else {
            $orders = $this->orderModel->getAllOrders();
        }
        include 'views/orders/list.php';
    }
    // Xem chi tiết đơn hàng
    public function view($id) {
        $orderModel = new OrderModel();
        $orderData = $orderModel->getOrderDetail($id);
        if ($orderData) {
            // Truyền dữ liệu sang view
            $data = [
                'order' => $orderData['order'],
                'items' => $orderData['items'],
                'currentStatus' => $orderData['order']['trang_thai']
            ];
            include_once 'views/orders/view.php';
        } else {
            echo "Không tìm thấy đơn hàng";
        }
    }
    public function updateStatus() {
        if(isset($_POST['order_id']) && isset($_POST['status'])) {
            $orderId = $_POST['order_id'];
            $status = $_POST['status'];
            
            $this->orderModel->updateOrderStatus($orderId, $status);
        }
        // Chuyển hướng về trang chi tiết đơn hàng
        header("Location: index.php?act=view-order&id=" . $orderId);
        exit();
    }
}
  