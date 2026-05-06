<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/../Models/CartModel.php';
class OrderController extends BaseController {
    private $orderModel;
    private $cartModel;
    private $userModel;
    
    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->cartModel = new CartModel();
        require_once __DIR__ . '/../Models/UserModel.php';
        $this->userModel = new UserModel();
    }
    
    /**
     * Trang checkout
     */
    public function checkout() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . 'auth/login');
        }
        
        $cartId = $this->cartModel->getCartByUserId($_SESSION['user_id']);
        $cartItems = $this->cartModel->getCartItems($cartId);
        
        if (empty($cartItems)) {
            $this->redirect(BASE_URL . 'cart');
        }
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        $total = 0;
        $hasRx = false;
        foreach ($cartItems as $item) {
            $total += $item['current_price'] * $item['quantity'];
            if (isset($item['is_prescription_required']) && ($item['is_prescription_required'] == 1 || $item['is_prescription_required'] === true)) {
                $hasRx = true;
            }
        }
        
        $this->loadView('cart/checkout', [
            'cartItems' => $cartItems,
            'user' => $user,
            'total' => $total,
            'hasRx' => $hasRx,
            'pageTitle' => 'Thanh toán - Nhà thuốc 1985'
        ]);
    }
    
    /**
     * Xử lý đặt hàng
     */
    public function placeOrder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $userId = $_SESSION['user_id'];
        $note = $_POST['note'] ?? '';

        $shippingData = [
            'name' => $_POST['full_name'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'note' => $note,
            'payment_method' => 'cod'
        ];
        
        $cartId = $this->cartModel->getCartByUserId($userId);
        $cartItems = $this->cartModel->getCartItems($cartId);
        
        $hasRx = false;
        foreach ($cartItems as $item) {
            if (isset($item['is_prescription_required']) && ($item['is_prescription_required'] == 1 || $item['is_prescription_required'] === true)) {
                $hasRx = true;
                break;
            }
        }

        // Xử lý upload đơn thuốc nếu cần
        $prescriptionImage = null;
        if ($hasRx) {
            if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../app/storage/prescriptions/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $fileExtension = pathinfo($_FILES['prescription']['name'], PATHINFO_EXTENSION);
                $prescriptionImage = 'rx_' . time() . '_' . $userId . '.' . $fileExtension;
                
                if (!move_uploaded_file($_FILES['prescription']['tmp_name'], $uploadDir . $prescriptionImage)) {
                    $_SESSION['error_message'] = "Không thể tải lên ảnh đơn thuốc.";
                    $this->redirect(BASE_URL . 'cart/checkout');
                }
            } else {
                $_SESSION['error_message'] = "Đơn hàng chứa thuốc kê đơn. Vui lòng tải lên ảnh đơn thuốc của bác sĩ.";
                $this->redirect(BASE_URL . 'cart/checkout');
            }
        }

        $shippingData = [
            'name' => $_POST['full_name'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'note' => $note,
            'payment_method' => 'cod',
            'has_prescription' => $hasRx,
            'prescription_image' => $prescriptionImage
        ];
        
        $orderId = $this->orderModel->createOrder($userId, $shippingData, $cartItems);
        
        if ($orderId) {
            // Lấy thông tin đơn hàng đầy đủ để gửi mail
            $order = $this->orderModel->getOrderById($orderId);
            $user = $this->userModel->getUserById($userId);
            
            if ($order && $user && !empty($user['email'])) {
                require_once __DIR__ . '/../Services/MailerService.php';
                require_once __DIR__ . '/../Services/Emails/BaseEmail.php';
                require_once __DIR__ . '/../Services/Emails/OrderConfirmationEmail.php';
                
                $emailService = new \App\Services\Emails\OrderConfirmationEmail($order, $user['full_name']);
                $emailService->send($user['email']);
            }

            $_SESSION['success_message'] = "Đặt hàng thành công! Mã đơn hàng của bạn là #" . $orderId;
            $this->redirect(BASE_URL . 'order/success?id=' . $orderId);
        } else {
            $_SESSION['error_message'] = $_SESSION['checkout_error'] ?? "Có lỗi xảy ra trong quá trình đặt hàng hoặc hàng trong kho không đủ.";
            unset($_SESSION['checkout_error']);
            $this->redirect(BASE_URL . 'cart/checkout');
        }
    }

    public function success() {
        $orderId = $_GET['id'] ?? null;
        $order = null;
        
        if ($orderId) {
            $order = $this->orderModel->getOrderById($orderId);
            if ($order && isset($_SESSION['user_id']) && $order['user_id'] != $_SESSION['user_id']) {
                $order = null;
            }
        }
        
        $this->loadView('order/success', [
            'orderId' => $orderId,
            'order' => $order,
            'pageTitle' => 'Đặt hàng thành công - Nhà thuốc 1985'
        ]);
    }

    /**
     * Hủy thanh toán
     */
    public function cancel() {
        $_SESSION['error_message'] = "Thanh toán đã bị hủy.";
        $this->redirect(BASE_URL . 'cart/checkout');
    }
}
