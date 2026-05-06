<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../Models/BaseModel.php';

class AdminEmailLogController extends BaseController {
    
    public function __construct() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) { // Chỉ Admin
            $this->redirect(BASE_URL . 'auth/login');
        }
    }
    
    /**
     * Danh sách email đã gửi
     */
    public function index() {
        $db = (new BaseModel())->db;
        
        $sql = "SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 100";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tính toán thống kê
        $stats = [
            'total' => count($logs),
            'opened' => 0,
            'failed' => 0
        ];
        
        foreach ($logs as $log) {
            if ($log['status'] === 'opened') $stats['opened']++;
            if ($log['status'] === 'failed') $stats['failed']++;
        }
        
        $this->loadView('admin/emaillog/index', [
            'logs' => $logs,
            'stats' => $stats,
            'pageTitle' => 'Quản lý Logs Gửi Email'
        ]);
    }
}
