<?php
// Tắt báo lỗi để tránh làm hỏng cấu trúc ảnh
error_reporting(0);

require_once __DIR__ . '/../config/db_connect.php';

if (isset($_GET['id'])) {
    $trackingId = $_GET['id'];
    
    global $pdo;
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE email_logs 
                SET status = 'opened', opened_at = NOW() 
                WHERE tracking_id = ? AND status = 'sent'
            ");
            $stmt->execute([$trackingId]);
        } catch (\PDOException $e) {
            // Bỏ qua lỗi DB, vẫn hiển thị ảnh
        }
    }
}

// Trả về ảnh pixel trong suốt (1x1 GIF)
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
exit;
