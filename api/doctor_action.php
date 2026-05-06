<?php
// api/doctor_action.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/database_service.php';
require_once __DIR__ . '/notification_service.php';

if (!isset($_SESSION['user_id'])) { 
    // Trong thực tế cần có phân quyền: WHERE role = 'doctor'
    http_response_code(401); exit;
}

$doctorId = $_SESSION['user_id'];
$requestId = $_POST['request_id'] ?? null;
$action = $_POST['action'] ?? null; // 'accept' or 'reject'

if (!$requestId || !$action) {
    http_response_code(400); exit;
}

// Kiểm tra xem Request này có đang thực sự Giao cho bác sĩ này không (tránh Race Condition)
$stmt = $pdo->prepare("SELECT * FROM consultation_requests WHERE id = ? AND current_doctor_id = ? AND status = 'pending'");
$stmt->execute([$requestId, $doctorId]);
$request = $stmt->fetch();

if (!$request) {
    echo json_encode(['success' => false, 'error' => 'Yêu cầu không còn tồn tại hoặc đã bị quá hạn Timeout']);
    exit;
}

if ($action === 'accept') {
    $pdo->beginTransaction();
    try {
        // 1. Chốt Request
        $pdo->prepare("UPDATE consultation_requests SET status = 'accepted' WHERE id = ?")->execute([$requestId]);
        
        // 2. Chuyển trạng thái bác sĩ thành "Đang bận"
        $pdo->prepare("UPDATE doctor_status SET is_available = 0 WHERE doctor_id = ?")->execute([$doctorId]);
        
        // 3. Khởi tạo phòng Chat 1-1
        // Tạo phòng, kiểm tra có hay chưa nếu cần
        $pdo->prepare("INSERT INTO conversations (doctor_id, customer_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE id=id")->execute([$doctorId, $request['customer_id']]);
        
        // Lấy lại ID của phòng chat đó
        $stmtCon = $pdo->prepare("SELECT id FROM conversations WHERE doctor_id = ? AND customer_id = ?");
        $stmtCon->execute([$doctorId, $request['customer_id']]);
        $convoId = $stmtCon->fetchColumn();
        
        $pdo->commit();
        
        // 4. Báo tin vui cho Khách hàng
        notifyCustomerStatus($request['customer_id'], [
            'status' => 'accepted',
            'conversation_id' => $convoId,
            'doctor_id' => $doctorId
        ]);
        
        echo json_encode(['success' => true, 'conversation_id' => $convoId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Lỗi máy chủ nội bộ.']);
    }
} elseif ($action === 'reject') {
    // 1. Đánh dấu từ chối
    $pdo->prepare("INSERT INTO request_rejections (request_id, doctor_id, reason) VALUES (?, ?, 'rejected')")->execute([$requestId, $doctorId]);
    
    // 2. Luân chuyển tự động tới Bác sĩ tiếp theo
    $nextDoctorId = findNextAvailableDoctor($pdo, $requestId);
    if ($nextDoctorId) {
        notifyDoctorIncomingRequest($nextDoctorId, [
            'request_id' => $requestId,
            'customer_id' => $request['customer_id'],
            'message' => 'Bạn có yêu cầu tư vấn mới'
        ]);
    } else {
        $pdo->prepare("UPDATE consultation_requests SET status = 'exhausted' WHERE id = ?")->execute([$requestId]);
        notifyCustomerStatus($request['customer_id'], ['status' => 'exhausted']);
    }
    
    echo json_encode(['success' => true, 'status' => 'rejected_and_routed']);
}
?>
