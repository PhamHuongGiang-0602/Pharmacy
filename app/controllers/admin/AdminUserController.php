<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../Models/BaseModel.php';

class AdminUserController extends BaseController {
    
    public function __construct() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) { // 1 is Admin
            $this->redirect(BASE_URL . 'auth/login');
        }
    }
    
    /**
     * Danh sách người dùng
     */
    public function index() {
        $db = (new BaseModel())->db;
        
        $q = $_GET['q'] ?? '';
        $role_id = $_GET['role_id'] ?? '';
        
        $where = ["1=1"];
        $params = [];
        
        if ($q !== '') {
            $where[] = "(username LIKE :q OR full_name LIKE :q OR email LIKE :q OR phone LIKE :q)";
            $params['q'] = "%$q%";
        }
        
        if ($role_id !== '') {
            $where[] = "role_id = :role_id";
            $params['role_id'] = $role_id;
        }
        
        $whereSql = implode(' AND ', $where);
        
        $sql = "SELECT user_id, username, email, full_name, phone, role_id, is_active, created_at, last_login 
                FROM users 
                WHERE $whereSql
                ORDER BY user_id DESC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $roles = [
            1 => 'Admin',
            2 => 'Dược sĩ',
            3 => 'Bác sĩ',
            4 => 'Khách hàng'
        ];
        
        $this->loadView('admin/users/index', [
            'users' => $users,
            'roles' => $roles,
            'q' => $q,
            'role_id' => $role_id,
            'pageTitle' => 'Quản lý người dùng'
        ]);
    }
    
    /**
     * Cập nhật trạng thái khóa/mở khóa tài khoản
     */
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? 0;
            $db = (new BaseModel())->db;
            
            // Không cho phép khóa chính mình
            if ($userId == $_SESSION['user_id']) {
                $_SESSION['error_message'] = "Không thể tự khóa tài khoản của bạn.";
                $this->redirect(BASE_URL . 'admin/users');
                return;
            }
            
            $stmt = $db->prepare("SELECT is_active FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $currentStatus = $stmt->fetchColumn();
            
            $newStatus = $currentStatus ? 0 : 1;
            
            $updateStmt = $db->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
            $updateStmt->execute([$newStatus, $userId]);
            
            $_SESSION['success_message'] = $newStatus ? "Đã mở khóa tài khoản." : "Đã khóa tài khoản.";
            $this->redirect(BASE_URL . 'admin/users');
        }
    }
    
    /**
     * Đổi Role của user
     */
    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? 0;
            $roleId = $_POST['role_id'] ?? 4;
            $db = (new BaseModel())->db;
            
            if ($userId == $_SESSION['user_id']) {
                $_SESSION['error_message'] = "Không thể tự đổi quyền của bạn.";
                $this->redirect(BASE_URL . 'admin/users');
                return;
            }
            
            $updateStmt = $db->prepare("UPDATE users SET role_id = ? WHERE user_id = ?");
            $updateStmt->execute([$roleId, $userId]);
            
            $_SESSION['success_message'] = "Đã phân quyền tài khoản thành công.";
            $this->redirect(BASE_URL . 'admin/users');
        }
    }
}
