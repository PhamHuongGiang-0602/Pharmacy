<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
ob_start();

// Định tuyến tĩnh cho built-in server
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($path && is_file($path)) {
        return false;
    }
}

require_once('config.php');
require_once('config/constants.php');
require_once('config/database.php');
require_once __DIR__ . '/vendor/autoload.php';

if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    die("Autoloader failed to load PHPMailer");
}

// ... (Giữ nguyên phần xử lý URL của bạn)

// PHẦN SỬA LỖI ĐƯỜNG DẪN (CHỮ THƯỜNG KHỚP VỚI GITHUB)
if ($controllerName === 'home' || $controllerName === '') {
    require_once 'app/controllers/HomeController.php'; // Đã sửa thành chữ thường
    $controller = new HomeController();
    $controller->index();
} elseif ($controllerName === 'auth') {
    require_once 'app/controllers/AuthController.php'; // Đã sửa thành chữ thường
    $controller = new AuthController();
    
    if (method_exists($controller, $actionName)) {
        $controller->$actionName();
    } else {
        echo "404 - Phương thức xác thực không tồn tại!";
    }
} else {
    switch ($controllerName) {
        case 'products':
        case 'product':
            require_once 'app/controllers/ProductController.php';
            $controller = new ProductController();
            if ($controllerName === 'product') {
                $_GET['id'] = $actionName;
                $controller->detail();
            } else {
                $controller->$actionName();
            }
            break;
        case 'cart':
            require_once 'app/controllers/CartController.php';
            $controller = new CartController();
            $controller->$actionName();
            break;
        case 'order':
            require_once 'app/controllers/OrderController.php';
            $controller = new OrderController();
            $controller->$actionName();
            break;
        case 'prescription':
            require_once 'app/controllers/PrescriptionController.php';
            $controller = new PrescriptionController();
            method_exists($controller, $actionName) ? $controller->$actionName() : $controller->index();
            break;
        case 'account':
            require_once 'app/controllers/AccountController.php';
            $controller = new AccountController();
            method_exists($controller, $actionName) ? $controller->$actionName() : $controller->index();
            break;
        case 'admin':
            $subController = isset($urlParts[1]) ? $urlParts[1] : 'dashboard';
            $subAction = isset($urlParts[2]) ? $urlParts[2] : 'index';
            
            // Lưu ý: Kiểm tra thư mục 'app/controllers/admin' trên GitHub có viết hoa chữ 'a' không nhé.
            // Nếu trên GitHub là 'admin' (viết thường), hãy dùng đường dẫn bên dưới:
            $adminPath = 'app/controllers/admin/'; 

            if ($subController === 'dashboard') require_once $adminPath . 'AdminDashboardController.php';
            elseif ($subController === 'chatbot') require_once $adminPath . 'AdminChatbotController.php';
            elseif ($subController === 'inventory') require_once $adminPath . 'AdminInventoryController.php';
            elseif ($subController === 'product') require_once $adminPath . 'AdminProductController.php';
            elseif ($subController === 'order') require_once $adminPath . 'AdminOrderController.php';
            elseif ($subController === 'users') require_once $adminPath . 'AdminUserController.php';
            elseif ($subController === 'general') require_once $adminPath . 'AdminGeneralController.php';
            elseif ($subController === 'settings') require_once $adminPath . 'AdminSettingsController.php';
            elseif ($subController === 'emaillog') require_once $adminPath . 'AdminEmailLogController.php';
            else { echo "404 - Admin module not found!"; exit(); }

            // Tạo class name tương ứng (Ví dụ: AdminDashboardController)
            $className = ucfirst($subController) == 'Users' ? 'AdminUserController' : 'Admin' . ucfirst($subController) . 'Controller';
            // Lưu ý: Bạn cần kiểm tra chính xác tên Class trong file có khớp với cách gọi này không.
            $controller = new $className();
            method_exists($controller, $subAction) ? $controller->$subAction() : echo "404 - Action not found!";
            break;
        case 'doctor':
            require_once 'app/controllers/PharmacistController.php';
            $controller = new PharmacistController();
            $method = ($actionName == 'dashboard' || $actionName == 'prescriptions') ? 'pendingPrescriptions' : $actionName;
            method_exists($controller, $method) ? $controller->$method() : echo "404 - Pharmacist action not found!";
            break;
        case 'blog':
            require_once 'app/controllers/BlogController.php';
            $controller = new BlogController();
            $method = ($actionName == 'index' || $actionName == '') ? 'index' : $actionName;
            method_exists($controller, $method) ? $controller->$method() : $controller->index();
            break;
        case 'about':
        case 'stores':
        case 'careers':
        case 'franchise':
        case 'news':
        case 'help':
        case 'privacy':
        case 'terms':
        case 'cookie':
            require_once 'app/controllers/PageController.php';
            $controller = new PageController();
            $controller->show($controllerName, $actionName);
            break;
        case 'consult':
            require_once 'public/chat.php';
            break;
        default:
            echo "404 - Trang không tồn tại!";
            break;
    }
}

ob_end_flush();

