<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/OrderModel.php';

class AccountController extends BaseController {
    private $userModel;
    private $orderModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . 'auth/login');
        }
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * Thông tin cá nhân
     */
    public function index() {
        $db = (new BaseModel())->db;
        $user = $db->query("SELECT * FROM users WHERE user_id = " . intval($_SESSION['user_id']))->fetch(PDO::FETCH_ASSOC);

        $this->loadView('user/profile', [
            'user' => $user,
            'pageTitle' => 'Thông tin tài khoản'
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $errors = [];

            if (empty($name)) {
                $errors[] = "Họ tên không được để trống.";
            }

            if (strlen($name) > 100) {
                $errors[] = "Họ tên không được quá 100 ký tự.";
            }

            if (!empty($phone) && !preg_match('/^(0|84)(3|5|7|8|9)([0-9]{8})$/', $phone)) {
                $errors[] = "Số điện thoại không hợp lệ.";
            }

            if (strlen($phone) > 20) {
                $errors[] = "Số điện thoại quá dài.";
            }

            if (empty($errors)) {
                $db = (new BaseModel())->db;
                $sql = "UPDATE users SET full_name = :name, phone = :phone, address = :address WHERE user_id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'name' => $name,
                    'phone' => $phone,
                    'address' => $address,
                    'id' => $_SESSION['user_id']
                ]);
                
                $_SESSION['full_name'] = $name;
                $_SESSION['user']['name'] = $name;
                $_SESSION['success_message'] = "Cập nhật thông tin thành công!";
            } else {
                $_SESSION['error_message'] = implode("<br>", $errors);
            }
            
            $this->redirect(BASE_URL . 'account');
        }
    }

    /**
     * Lịch sử đơn hàng
     */
    public function orders() {
        $orders = $this->orderModel->getOrdersByUserId($_SESSION['user_id']);
        
        $this->loadView('user/orders', [
            'orders' => $orders,
            'pageTitle' => 'Lịch sử đơn hàng'
        ]);
    }

    /**
     * Chi tiết đơn hàng
     */
    public function order_detail() {
        $orderId = $_GET['id'] ?? 0;
        $order = $this->orderModel->getOrderById($orderId);
        
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->redirect(BASE_URL . 'account/orders');
        }
        
        $db = (new BaseModel())->db;
        $details = $db->query("SELECT * FROM order_details WHERE order_id = " . intval($orderId))->fetchAll(PDO::FETCH_ASSOC);
        
        $this->loadView('user/order_detail', [
            'order' => $order,
            'details' => $details,
            'pageTitle' => 'Chi tiết đơn hàng #' . $orderId
        ]);
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel_order() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'];
            $order = $this->orderModel->getOrderById($orderId);
            
            if ($order && $order['user_id'] == $_SESSION['user_id'] && $order['status'] == 'pending') {
                $db = (new BaseModel())->db;
                $db->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = ?")->execute([$orderId]);
                $_SESSION['success_message'] = "Đã hủy đơn hàng thành công.";
            } else {
                $_SESSION['error_message'] = "Không thể hủy đơn hàng này.";
            }
            
            $this->redirect(BASE_URL . 'account/orders');
        }
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if ($newPassword !== $confirmPassword) {
                $_SESSION['error_message'] = "Mật khẩu xác nhận không khớp.";
                $this->redirect(BASE_URL . 'account');
                return;
            }
            
            $db = (new BaseModel())->db;
            $user = $db->query("SELECT password_hash FROM users WHERE user_id = " . intval($_SESSION['user_id']))->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($currentPassword, $user['password_hash'])) {
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $stmt->execute([$newHash, $_SESSION['user_id']]);
                $_SESSION['success_message'] = "Đổi mật khẩu thành công!";
            } else {
                $_SESSION['error_message'] = "Mật khẩu hiện tại không đúng.";
            }
            
            $this->redirect(BASE_URL . 'account');
        }
    }
}
