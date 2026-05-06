<?php
// 1. Bật hiển thị lỗi để xử lý trang trắng
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    // Không nên die ở đây nếu bạn muốn thấy lỗi khác, nhưng để debug thì tạm ổn
    error_log("Autoloader failed to load PHPMailer");
}

// --- PHẦN XỬ LÝ URL (Giữ nguyên logic của bạn) ---
$scriptName = $_SERVER['SCRIPT_NAME']; 
$requestUri = $_SERVER['REQUEST_URI']; 
$path = parse_url($requestUri, PHP_URL_PATH);
$basePath = dirname($scriptName);
if ($basePath === DIRECTORY_SEPARATOR || $basePath === '.') $basePath = '';
$url = substr($path, strlen($basePath));
$url = trim($url, '/');

if ($url === 'index.php' || $url === '') {
    $url = isset($_GET['url']) ? trim($_GET['url'], '/') : 'home';
}

$urlParts = explode('/', $url);
$controllerName = $urlParts[0];
$actionName = isset($urlParts[1]) ? $urlParts[1] : 'index';

// --- PHẦN ROUTING ĐÃ SỬA LỖI CÚ PHÁP ---

if ($controllerName === 'home' || $controllerName === '') {
    require_once 'app/controllers/HomeController.php';
    $controller = new HomeController();
    $controller->index();
} elseif ($controllerName === 'auth') {
    require_once 'app/controllers/AuthController.php';
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
                if (method_exists($controller, $actionName)) {
                    $controller->$actionName();
                } else {
                    $controller->index();
                }
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
            if (method_exists($controller, $actionName)) {
                $controller->$actionName();
            } else {
                $controller->index();
            }
            break;

        case 'account':
            require_once 'app/controllers/AccountController.php';
            $controller = new AccountController();
            if (method_exists($controller, $actionName)) {
                $controller->$actionName();
            } else {
                $controller->index();
            }
            break;

        case 'admin':
            $subController = isset($urlParts[1]) ? $urlParts[1] : 'dashboard';
            $subAction = isset($urlParts[2]) ? $urlParts[2] : 'index';
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

            $className = ($subController === 'users') ? 'AdminUserController' : 'Admin' . ucfirst($subController) . 'Controller';
            
            if (class_exists($className)) {
                $controller = new $className();
                if (method_exists($controller, $subAction)) {
                    $controller->$subAction();
                } else {
                    echo "404 - Admin action not found!";
                }
            } else {
                echo "404 - Admin Class $className không tồn tại!";
            }
            break;

        case 'doctor':
            require_once 'app/controllers/PharmacistController.php';
            $controller = new PharmacistController();
            $method = ($actionName == 'dashboard' || $actionName == 'prescriptions') ? 'pendingPrescriptions' : $actionName;
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                echo "404 - Pharmacist action không tồn tại!";
            }
            break;

        case 'blog':
            require_once 'app/controllers/BlogController.php';
            $controller = new BlogController();
            $method = ($actionName == 'index' || $actionName == '') ? 'index' : $actionName;
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->index();
            }
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
