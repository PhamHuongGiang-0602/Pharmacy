<?php
require_once __DIR__ . '/BaseController.php';

class PrescriptionController extends BaseController {
    
    public function index() {
        $this->upload();
    }
    
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý upload
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error_message'] = "Vui lòng đăng nhập để gửi đơn thuốc!";
                $this->redirect('auth/login');
                return;
            }
            
            $note = trim($_POST['note'] ?? '');
            
            // Xử lý file ảnh
            $uploadDir = __DIR__ . '/../../app/storage/prescriptions/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            if (isset($_FILES['prescription_image']) && $_FILES['prescription_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['prescription_image']['tmp_name'];
                $fileName = $_FILES['prescription_image']['name'];
                $fileSize = $_FILES['prescription_image']['size'];
                $fileType = $_FILES['prescription_image']['type'];
                
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($fileExtension, $allowedExts)) {
                    // Tạo tên file mới
                    $newFileName = 'rx_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // Lưu thông tin vào database: Tạo một đơn hàng "chờ duyệt"
                        require_once __DIR__ . '/../Models/OrderModel.php';
                        $orderModel = new OrderModel();
                        
                        $orderData = [
                            'user_id' => $_SESSION['user_id'],
                            'receiver_name' => $_POST['receiver_name'] ?? $_SESSION['full_name'] ?? 'Khách hàng',
                            'receiver_phone' => $_POST['receiver_phone'] ?? '',
                            'shipping_address' => $_POST['shipping_address'] ?? '',
                            'note' => "Gửi từ trang Upload Đơn thuốc. Ghi chú: " . $note,
                            'payment_method' => 'cod', // Tạm định
                            'subtotal' => 0,
                            'total_amount' => 0, // Dược sĩ sẽ cập nhật sau
                            'status' => 'pending',
                            'has_prescription' => 1,
                            'prescription_image' => $newFileName,
                            'prescription_verified' => 0
                        ];
                        
                        $orderId = $orderModel->createPrescriptionOrder($orderData);
                        
                        if ($orderId) {
                            $_SESSION['success_message'] = "Gửi đơn thuốc thành công! Dược sĩ sẽ liên hệ lại với bạn trong thời gian sớm nhất.";
                            $this->redirect('prescription/success');
                            return;
                        } else {
                            $_SESSION['error_message'] = "Có lỗi xảy ra khi lưu vào cơ sở dữ liệu.";
                        }
                    } else {
                        $_SESSION['error_message'] = "Lỗi khi di chuyển file tải lên.";
                    }
                } else {
                    $_SESSION['error_message'] = "Chỉ chấp nhận các file ảnh: JPG, JPEG, PNG, GIF, WEBP.";
                }
            } else {
                $_SESSION['error_message'] = "Vui lòng chọn một file ảnh đơn thuốc hợp lệ.";
            }
            
            // Nếu có lỗi, load lại view kèm thông báo
            $this->loadView('prescription/upload', [
                'pageTitle' => 'Tải lên đơn thuốc - Nhà thuốc 1985',
                'error' => $_SESSION['error_message'] ?? null
            ]);
            unset($_SESSION['error_message']);
            
        } else {
            // GET request
            $this->loadView('prescription/upload', [
                'pageTitle' => 'Tải lên đơn thuốc - Nhà thuốc 1985'
            ]);
        }
    }
    
    public function success() {
        $this->loadView('prescription/success', [
            'pageTitle' => 'Thành công - Nhà thuốc 1985'
        ]);
    }
    
    /**
     * Hiển thị ảnh đơn thuốc an toàn
     */
    public function view() {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 403 Forbidden');
            exit('Forbidden');
        }
        
        $filename = $_GET['file'] ?? '';
        
        if (empty($filename) || strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
            header('HTTP/1.0 404 Not Found');
            exit('File not found');
        }
        
        $filepath = __DIR__ . '/../../app/storage/prescriptions/' . $filename;
        
        // Fallback cho ảnh cũ ở public/uploads/
        if (!file_exists($filepath)) {
            $fallbackPath = __DIR__ . '/../../public/uploads/prescriptions/' . $filename;
            if (file_exists($fallbackPath)) {
                $filepath = $fallbackPath;
            } else {
                header('HTTP/1.0 404 Not Found');
                exit('File not found: ' . $filename);
            }
        }
        
        $hasAccess = false;
        
        if ($_SESSION['role_id'] != 4) { // Admin, Pharmacist, Doctor
            $hasAccess = true;
        } else {
            // Customer: Only if they own the order
            require_once __DIR__ . '/../Models/BaseModel.php';
            $db = (new BaseModel())->db;
            $stmt = $db->prepare("SELECT order_id FROM orders WHERE prescription_image = ? AND user_id = ?");
            $stmt->execute([$filename, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $hasAccess = true;
            }
        }
        
        if (!$hasAccess) {
            header('HTTP/1.0 403 Forbidden');
            exit('Forbidden: You do not have permission to view this prescription.');
        }
        
        if (ob_get_level()) ob_clean();
        
        $mime = 'image/jpeg'; // Default
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($filepath) ?: 'image/jpeg';
        } else {
            $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
            $mimes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp'
            ];
            $mime = $mimes[$ext] ?? 'image/jpeg';
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=86400');
        readfile($filepath);
        exit;
    }
}
