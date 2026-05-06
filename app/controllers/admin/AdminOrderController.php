<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../Models/OrderModel.php';

class AdminOrderController extends BaseController {
    private $orderModel;
    
    public function __construct() {
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 2])) { // Admin or Pharmacist
            $this->redirect(BASE_URL . 'auth/login');
        }
        $this->orderModel = new OrderModel();
    }
    
    /**
     * Danh sách tất cả đơn hàng
     */
    public function index() {
        $db = (new BaseModel())->db;
        
        $q = $_GET['q'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $where = ["1=1"];
        $params = [];
        
        if ($q !== '') {
            $where[] = "(o.order_id = :qid OR u.full_name LIKE :q OR o.shipping_phone LIKE :q)";
            $params['qid'] = $q;
            $params['q'] = "%$q%";
        }
        
        if ($status !== '') {
            $where[] = "o.status = :status";
            $params['status'] = $status;
        }
        
        $whereSql = implode(' AND ', $where);
        
        $sql = "SELECT o.*, u.full_name as customer_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                WHERE $whereSql
                ORDER BY o.order_date DESC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->loadView('admin/order/index', [
            'orders' => $orders,
            'pageTitle' => 'Quản lý đơn hàng',
            'q' => $q,
            'status' => $status
        ]);
    }
    
    /**
     * Chi tiết đơn hàng
     */
    public function detail() {
        $id = $_GET['id'] ?? 0;
        $db = (new BaseModel())->db;
        
        $order = $db->query("SELECT o.*, u.full_name as customer_name, u.email as customer_email 
                            FROM orders o 
                            JOIN users u ON o.user_id = u.user_id 
                            WHERE o.order_id = " . intval($id))->fetch(PDO::FETCH_ASSOC);
                            
        if (!$order) {
            $this->redirect(BASE_URL . 'admin/order');
        }
        
        $details = $db->query("SELECT od.*, b.batch_number 
                              FROM order_details od 
                              LEFT JOIN batches b ON od.batch_id = b.batch_id 
                              WHERE od.order_id = " . intval($id))->fetchAll(PDO::FETCH_ASSOC);
                              
        $this->loadView('admin/order/detail', [
            'order' => $order,
            'details' => $details,
            'pageTitle' => 'Chi tiết đơn hàng #' . $order['order_id']
        ]);
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'];
            $status = $_POST['status'];
            
            $db = (new BaseModel())->db;
            
            $sql = "UPDATE orders SET status = :status";
            if ($status === 'completed') {
                $sql .= ", payment_status = 'paid'";
            }
            $sql .= " WHERE order_id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['status' => $status, 'id' => $orderId]);
            
            // Gửi email khi đơn hàng hoàn thành
            if ($status === 'completed') {
                $order = $db->query("SELECT o.*, u.full_name as customer_name, u.email as customer_email 
                                    FROM orders o JOIN users u ON o.user_id = u.user_id 
                                    WHERE o.order_id = " . intval($orderId))->fetch(\PDO::FETCH_ASSOC);
                                    
                if ($order && !empty($order['customer_email'])) {
                    require_once __DIR__ . '/../../Services/MailerService.php';
                    require_once __DIR__ . '/../../Services/Emails/BaseEmail.php';
                    require_once __DIR__ . '/../../Services/Emails/OrderCompletedEmail.php';
                    
                    $emailObj = new \App\Services\Emails\OrderCompletedEmail($order, $order['customer_name']);
                    $emailObj->send($order['customer_email']);
                }
            }
            
            $this->redirect(BASE_URL . 'admin/order/detail?id=' . $orderId);
        }
    }
    
    /**
     * Xác nhận đơn thuốc
     */
    public function verifyPrescription() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'];
            
            $db = (new BaseModel())->db;
            $sql = "UPDATE orders SET prescription_verified = 1, verified_by = :user_id, verified_at = NOW() WHERE order_id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $_SESSION['user_id'], 'id' => $orderId]);
            
            $_SESSION['success_message'] = "Đã xác nhận đơn thuốc.";
            $this->redirect(BASE_URL . 'admin/order/detail?id=' . $orderId);
        }
    }
}
