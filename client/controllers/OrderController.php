<?php
class OrderController {
    public function checkout() {
        if (!isset($_SESSION['email'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để mua hàng";
            header('Location: ?act=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý mua ngay
            if (isset($_POST['buy-now'])) {
                $san_pham_id = $_POST['san_pham_id'];
                $so_luong = $_POST['so_luong'];
                $gia = $_POST['gia'];
                $ram_id = $_POST['ram_id'];

                // Lấy thông tin sản phẩm
                $sql = "SELECT sp.*, hasp.hinh_sp, r.dung_luong 
                       FROM san_pham sp
                       JOIN hinh_anh_san_pham hasp ON sp.san_pham_id = hasp.san_pham_id
                       LEFT JOIN ram r ON r.ram_id = ?
                       WHERE sp.san_pham_id = ?
                       LIMIT 1";
                $product = pdo_query_one($sql, $ram_id, $san_pham_id);

                if ($product) {
                    $_SESSION['checkout_data'] = [
                        'cart_items' => [[
                            'san_pham_id' => $san_pham_id,
                            'so_luong' => $so_luong,
                            'gia' => $gia,
                            'ram_id' => $ram_id,
                            'ten_san_pham' => $product['ten_san_pham'],
                            'hinh_sp' => $product['hinh_sp'],
                            'dung_luong' => $product['dung_luong']
                        ]],
                        'total' => $gia * $so_luong,
                        'dia_chi' => $_SESSION['email']['dia_chi'],
                        'so_dien_thoai' => $_SESSION['email']['so_dien_thoai']
                    ];
                }
            }
            // Xử lý mua từ giỏ hàng 
            else if (isset($_POST['cart_items'])) {
                $cart_items = array_map(function($item) {
                    return json_decode(htmlspecialchars_decode($item), true);
                }, $_POST['cart_items']);

                $total = 0;
                foreach ($cart_items as $item) {
                    $total += $item['gia'] * $item['so_luong'];
                }

                $_SESSION['checkout_data'] = [
                    'cart_items' => $cart_items,
                    'total' => $total,
                    'dia_chi' => $_POST['dia_chi'],
                    'so_dien_thoai' => $_POST['so_dien_thoai']
                ];
            }

            include "./views/checkout.php";
            return;
        }
        
        header('Location: ?act=cart');
        exit;
    }

    public function placeOrder() {
        if (!isset($_SESSION['email']) || !isset($_SESSION['checkout_data'])) {
            $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại";
            header('Location: ?act=cart');
            exit;
        }

        try {
            $checkout_data = $_SESSION['checkout_data'];
            
            // Tạo đơn hàng mới
            $order_data = [
                'tai_khoan_id' => $_SESSION['email']['tai_khoan_id'],
                'ho_va_ten' => $_SESSION['email']['ho_va_ten'], 
                'email' => $_SESSION['email']['email'],
                'so_dien_thoai' => $checkout_data['so_dien_thoai'],
                'dia_chi' => $checkout_data['dia_chi'],
                'tong_tien' => $checkout_data['total'] + 30000,
                'phuong_thuc_thanh_toan' => 1,
                'trang_thai' => 1
            ];

            $order_id = createOrder($order_data);

            if ($order_id) {
                // Tạo mảng chứa các ID sản phẩm đã chọn
                $selected_items = [];
                
                foreach ($checkout_data['cart_items'] as $item) {
                    $order_detail = [
                        'don_hang_id' => $order_id,
                        'san_pham_id' => $item['san_pham_id'],
                        'so_luong' => $item['so_luong'],
                        'gia' => $item['gia'],
                        'ram_id' => $item['ram_id']
                    ];
                    createOrderDetail($order_detail);
                    
                    // Thêm vào mảng sản phẩm đã chọn
                    $selected_items[] = "tai_khoan_id = {$_SESSION['email']['tai_khoan_id']} AND san_pham_id = {$item['san_pham_id']} AND ram_id = {$item['ram_id']}";
                }

                // Xóa chỉ những sản phẩm đã chọn khỏi giỏ hàng
                if (!empty($selected_items)) {
                    $conditions = implode(' OR ', $selected_items);
                    $sql = "DELETE FROM gio_hang WHERE " . $conditions;
                    pdo_execute($sql);
                }

                unset($_SESSION['checkout_data']);
                $_SESSION['success'] = "Đặt hàng thành công!";
                header('Location: ?act=my-orders');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            header('Location: ?act=cart');
            exit;
        }
    }

    public function getMyOrders() {
        if (!isset($_SESSION['email'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để xem đơn hàng";
            header('Location: ?act=login');
            exit;
        }

        $tai_khoan_id = $_SESSION['email']['tai_khoan_id'];
        $orders = [];

        try {
            $pdo = pdo_get_connection();
            
            $sql = "SELECT dh.*, ct.san_pham_id, ct.so_luong, ct.gia, sp.ten_san_pham, hasp.hinh_sp     
                    FROM don_hang dh 
                    JOIN chi_tiet_don_hang ct ON dh.don_hang_id = ct.don_hang_id 
                    JOIN san_pham sp ON ct.san_pham_id = sp.san_pham_id 
                    LEFT JOIN hinh_anh_san_pham hasp ON sp.san_pham_id = hasp.san_pham_id 
                    WHERE dh.tai_khoan_id = ? 
                    GROUP BY ct.chi_tiet_don_hang_id
                    ORDER BY dh.ngay_dat DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$tai_khoan_id]);
            
            while ($row = $stmt->fetch()) {
                if (!isset($orders[$row['don_hang_id']])) {
                    $orders[$row['don_hang_id']] = [
                        'ma_don_hang' => $row['don_hang_id'],
                        'ngay_dat' => $row['ngay_dat'],
                        'trang_thai' => $row['trang_thai'],
                        'tong_tien' => $row['tong_tien'],
                        'products' => []
                    ];
                }
                
                $orders[$row['don_hang_id']]['products'][] = [
                    'ten_san_pham' => $row['ten_san_pham'],
                    'hinh_sp' => $row['hinh_sp'],
                    'so_luong' => $row['so_luong'],
                    'gia' => $row['gia']
                ];
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Có lỗi xảy ra khi lấy thông tin đơn hàng";
        }

        include "./views/my-orders.php";
    }

    public function deleteOrder() {
        if (!isset($_SESSION['email'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để thực hiện chức năng này";
            header('Location: ?act=login');
            exit;
        }

        if (isset($_GET['id'])) {
            $order_id = $_GET['id'];
            $tai_khoan_id = $_SESSION['email']['tai_khoan_id'];

            // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
            $order = getOrderById($order_id);
            
            if (!$order || $order['tai_khoan_id'] != $tai_khoan_id) {
                $_SESSION['error'] = "Không tìm thấy đơn hàng hoặc bạn không có quyền xóa đơn hàng này";
                header('Location: ?act=my-orders');
                exit;
            }
            if ($order['trang_thai'] != 1) {
                $_SESSION['error'] = "Chỉ có thể xóa đơn hàng ở trạng thái chờ xác nhận";
                header('Location: ?act=my-orders');
                exit;
            }

            if (deleteOrder($order_id)) {
                $_SESSION['success'] = "Đã xóa đơn hàng thành công";
            } else {
                $_SESSION['error'] = "Có li xảy ra khi xóa đơn hàng";
            }
        }

        header('Location: ?act=my-orders');
        exit;
    }

    public function processCartOrder() {
        if (!isset($_SESSION['email'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để mua hàng";
            header('Location: ?act=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $cart_items = array_map(function($item) {
                    return json_decode(htmlspecialchars_decode($item), true);
                }, $_POST['cart_items']);

                $total = $_POST['tong_tien'];
                
                // Tạo đơn hàng mới
                $order_data = [
                    'tai_khoan_id' => $_SESSION['email']['tai_khoan_id'],
                    'ho_va_ten' => $_SESSION['email']['ho_va_ten'],
                    'email' => $_SESSION['email']['email'],
                    'so_dien_thoai' => $_POST['so_dien_thoai'],
                    'dia_chi' => $_POST['dia_chi'],
                    'tong_tien' => $total,
                    'phuong_thuc_thanh_toan' => 1, // COD mặc định
                    'trang_thai' => 1 // Trạng thái chờ xử lý
                ];

                $order_id = createOrder($order_data);

                if ($order_id) {
                    foreach ($cart_items as $item) {
                        $order_detail = [
                            'don_hang_id' => $order_id,
                            'san_pham_id' => $item['san_pham_id'],
                            'so_luong' => $item['so_luong'],
                            'gia' => $item['gia'],
                            'ram_id' => $item['ram_id']
                        ];
                        createOrderDetail($order_detail);
                    }

                    // Xóa giỏ hàng sau khi đặt hàng thành công
                    $sql = "DELETE FROM gio_hang WHERE tai_khoan_id = ?";
                    pdo_execute($sql, $_SESSION['email']['tai_khoan_id']);

                    $_SESSION['success'] = "Đặt hàng thành công!";
                    header('Location: ?act=my-orders');
                    exit;
                }

                $_SESSION['error'] = "Có lỗi xảy ra khi đặt hàng!";
                header('Location: ?act=cart');
                exit;

            } catch (Exception $e) {
                $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
                header('Location: ?act=cart');
                exit;
            }
        }
    }
}
