<?php
// app/controllers/AuthController.php

class AuthController {
    private $userModel;

    public function __construct() {
        // Khởi tạo Model để sử dụng trong các phương thức
        require_once 'app/models/UserModel.php';
        $this->userModel = new UserModel();
    }
// app/controllers/AuthController.php
    public function register() {
    $pageTitle = 'Đăng ký tài khoản — Long Châu';
    
    // Lấy thông tin nhập cũ hoặc lỗi từ session (nếu có)
    $errors = $_SESSION['register_errors'] ?? null;
    $success = $_SESSION['success_message'] ?? null;
    $oldInput = $_SESSION['old_input'] ?? [];
    
    // Xóa session sau khi đã lấy dữ liệu để tránh hiển thị lại khi F5
    unset($_SESSION['register_errors'], $_SESSION['success_message'], $_SESSION['old_input']);

    // Nạp View (không dùng header/footer chung nếu bạn muốn dùng giao diện đơn giản riêng cho Auth)
    require_once 'app/views/auth/register.php';
}

// app/controllers/AuthController.php -> phương thức handleRegister
public function handleRegister() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];
        $fullname = $_POST['fullName'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // 1. Validate dữ liệu
        if (strlen($password) < 8) $errors[] = "Mật khẩu phải từ 8 ký tự.";
        
        // 2. Kiểm tra email tồn tại (gọi Model)
        if ($this->userModel->checkEmailExists($email)) {
            $errors[] = "Email này đã được đăng ký.";
        }

        if (empty($errors)) {
            // 3. Mã hóa mật khẩu và lưu
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $data = [
                'fullname' => $fullname,
                'email' => $email,
                'password' => $hashedPassword,
                'phone' => $phone,
                'gender' => $_POST['gender'] ?? 'other',
                'birthday' => $_POST['birthYear'].'-'.$_POST['birthMonth'].'-'.$_POST['birthDay']
            ];
            
            if ($this->userModel->createUser($data)) {
                $_SESSION['success_message'] = "Đăng ký thành công! Hãy đăng nhập.";
                header("Location: " . BASE_URL . "auth/login");
                exit();
            }
        } else {
            // 4. Trả về lỗi nếu có
            $_SESSION['register_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header("Location: " . BASE_URL . "auth/register");
            exit();
        }
    }
}
}