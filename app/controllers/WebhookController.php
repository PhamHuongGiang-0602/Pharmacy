<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/../Services/MoMoService.php';

class WebhookController extends BaseController {
    private $orderModel;
    private $momoService;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->momoService = new MoMoService();
    }


    /**
     * Handle MoMo IPN
     */
    public function momo() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) return;

        // Trong môi trường thực tế cần verify signature ở đây tương tự PayOS
        
        $orderId = $data['orderId'];
        $resultCode = $data['resultCode'];

        if ($resultCode == 0) {
            $this->orderModel->updatePaymentStatus($orderId, 'paid');
            error_log("MoMo IPN: Order #$orderId paid successfully.");
        }

        // MoMo yêu cầu trả về HTTP 204 hoặc JSON trống
        header("HTTP/1.1 204 No Content");
    }
    /**
     * Handle SePay Webhook
     */
    public function sepay() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Ghi log ra file để kiểm tra (vào thư mục logs của dự án)
        $logFile = __DIR__ . '/../../sepay_log.txt';
        $logData = date('Y-m-d H:i:s') . " - Data: " . json_encode($data) . " - Headers: " . json_encode($headers) . "\n";
        file_put_contents($logFile, $logData, FILE_APPEND);

        /* Tạm thời tắt kiểm tra API Key để test nhanh
        if (!defined('SEPAY_API_KEY') || strpos($authHeader, SEPAY_API_KEY) === false) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        */

        if (!$data) return;

        $content = $data['content'] ?? ''; 
        $amount = $data['transferAmount'] ?? 0;

        // Tìm mã đơn hàng (DH123 hoặc chỉ 123 nếu nội dung chỉ có số)
        $orderId = null;
        if (preg_match('/DH(\d+)/i', $content, $matches)) {
            $orderId = $matches[1];
        } elseif (is_numeric(trim($content))) {
            $orderId = trim($content);
        }

        if ($orderId) {
            $order = $this->orderModel->getOrderById($orderId);
            if ($order && ($order['payment_status'] ?? '') !== 'paid') {
                $this->orderModel->updatePaymentStatus($orderId, 'paid');
                error_log("SePay: Đơn hàng #$orderId đã được cập nhật thành công.");
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }
}
