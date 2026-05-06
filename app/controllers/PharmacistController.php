<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/OrderModel.php';

class PharmacistController extends BaseController {
    private $orderModel;
    
    public function __construct() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) { // Role 2 is Pharmacist
            $this->redirect(BASE_URL . 'auth/login');
        }
        $this->orderModel = new OrderModel();
    }
    
    /**
     * Danh sách đơn hàng cần duyệt đơn thuốc
     */
    public function pendingPrescriptions() {
        // Lấy các đơn hàng có ảnh toa thuốc, ưu tiên đơn chưa duyệt
        $sql = "SELECT o.*, u.full_name as customer_name, u.phone as customer_phone 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                WHERE o.has_prescription = 1 
                ORDER BY o.order_id DESC 
                LIMIT 50";
        $db = (new BaseModel())->db;
        $orders = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        $this->loadView('doctor/prescriptions', [
            'orders' => $orders,
            'pageTitle' => 'Duyệt đơn thuốc - Dược sĩ'
        ]);
    }
    
    /**
     * Duyệt đơn thuốc
     */
    public function verify() {
        $orderId = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? 'approved';
        
        if ($orderId) {
            $db = (new BaseModel())->db;
            if ($status == 'approved') {
                $sql = "UPDATE orders SET prescription_verified = TRUE, verified_by = :user_id, verified_at = NOW(), status = 'confirmed' 
                        WHERE order_id = :order_id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['user_id' => $_SESSION['user_id'], 'order_id' => $orderId]);
            } else {
                $sql = "UPDATE orders SET status = 'cancelled', admin_note = 'Đơn thuốc không hợp lệ' WHERE order_id = :order_id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['order_id' => $orderId]);
            }
        }
        
        $this->redirect(BASE_URL . 'doctor/prescriptions');
    }

    /**
     * Viết đơn thuốc online (UC-M03)
     */
    public function createPrescription() {
        $customerId = $_GET['user_id'] ?? null;
        
        $db = (new BaseModel())->db;
        $products = $db->query("SELECT product_id, product_name, price, unit FROM products WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);
        
        $customer = null;
        if ($customerId) {
            $customer = $db->query("SELECT user_id, full_name, phone, address FROM users WHERE user_id = " . intval($customerId))->fetch(PDO::FETCH_ASSOC);
        }
        
        $this->loadView('doctor/create_prescription', [
            'products' => $products,
            'customer' => $customer,
            'pageTitle' => 'Kê đơn thuốc trực tuyến'
        ]);
    }
    
    /**
     * Lưu đơn thuốc và tạo đơn hàng (UC-M04)
     */
    public function storePrescription() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = $_POST['customer_id'] ?? null;
            $customerName = $_POST['customer_name'] ?? '';
            $customerPhone = $_POST['customer_phone'] ?? '';
            $customerAddress = $_POST['customer_address'] ?? '';
            $notes = $_POST['notes'] ?? '';
            
            $productIds = $_POST['products'] ?? [];
            $quantities = $_POST['quantities'] ?? [];
            $dosages = $_POST['dosages'] ?? [];
            
            if (empty($productIds)) {
                $_SESSION['error_message'] = "Vui lòng chọn ít nhất 1 loại thuốc.";
                $this->redirect(BASE_URL . 'doctor/createPrescription' . ($customerId ? '?user_id='.$customerId : ''));
                return;
            }
            
            $db = (new BaseModel())->db;
            
            try {
                $db->beginTransaction();
                
                // Chuẩn bị ghi chú chi tiết
                $fullNotes = "Chỉ định y khoa: " . $notes . "\n\nHDSD chi tiết:\n";
                foreach ($productIds as $i => $pid) {
                    // (Sẽ được xây dựng lại trong vòng lặp dưới)
                }

                $subtotal = 0;
                $items = [];
                foreach ($productIds as $i => $pid) {
                    $qty = $quantities[$i];
                    $dosage = $dosages[$i];
                    
                    $product = $db->query("SELECT product_name, price FROM products WHERE product_id = " . intval($pid))->fetch(PDO::FETCH_ASSOC);
                    if ($product) {
                        $subtotal += $product['price'] * $qty;
                        $items[] = [
                            'product_id' => $pid,
                            'product_name' => $product['product_name'],
                            'quantity' => $qty,
                            'unit_price' => $product['price'],
                            'dosage_instruction' => $dosage
                        ];
                        $fullNotes .= "- " . $product['product_name'] . ": " . $dosage . "\n";
                    }
                }
                
                $orderId = $_POST['order_id'] ?? null;
                
                if ($orderId) {
                    // Cập nhật đơn hàng hiện tại (đơn mà khách đã gửi ảnh toa)
                    $sql = "UPDATE orders SET subtotal = :subtotal, total_amount = :total, admin_note = :admin_note, 
                                             prescription_verified = 1, verified_by = :doctor_id, verified_at = NOW(), 
                                             status = 'confirmed' 
                            WHERE order_id = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        'subtotal' => $subtotal,
                        'total' => $subtotal,
                        'admin_note' => $fullNotes,
                        'doctor_id' => $_SESSION['user_id'],
                        'id' => $orderId
                    ]);
                    
                    // Xóa các chi tiết cũ nếu có (thường là trống)
                    $db->prepare("DELETE FROM order_details WHERE order_id = ?")->execute([$orderId]);
                } else {
                    // Tạo Order mới hoàn toàn
                    $sql = "INSERT INTO orders (user_id, shipping_name, shipping_phone, shipping_address, shipping_note, 
                                             subtotal, total_amount, payment_method, status, 
                                             has_prescription, prescription_verified, verified_by, verified_at, admin_note) 
                            VALUES (:user_id, :name, :phone, :address, :note, :subtotal, :total, 'cod', 'pending', 
                                    1, 1, :doctor_id, NOW(), :admin_note)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        'user_id' => $customerId ?: 6,
                        'name' => $customerName,
                        'phone' => $customerPhone,
                        'address' => $customerAddress,
                        'note' => '',
                        'subtotal' => $subtotal,
                        'total' => $subtotal,
                        'doctor_id' => $_SESSION['user_id'],
                        'admin_note' => $fullNotes
                    ]);
                    $orderId = $db->lastInsertId();
                }
                
                // 3. Tạo Order Details (Không dùng cột notes nữa)
                foreach ($items as $item) {
                    $sql = "INSERT INTO order_details (order_id, product_id, product_name, quantity, unit_price) 
                            VALUES (:order_id, :product_id, :p_name, :qty, :price)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'],
                        'p_name' => $item['product_name'],
                        'qty' => $item['quantity'],
                        'price' => $item['unit_price']
                    ]);
                }
                
                $db->commit();
                $_SESSION['success_message'] = "Đã kê đơn và tạo đơn hàng thành công (Mã ĐH: #$orderId).";
                
                // Redirect back to dashboard or prescriptions list
                $this->redirect(BASE_URL . 'doctor/dashboard');
                
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error_message'] = "Lỗi khi tạo đơn: " . $e->getMessage();
                $this->redirect(BASE_URL . 'doctor/createPrescription');
            }
        }
    }
}
