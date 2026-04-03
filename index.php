<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
ob_start();

require_once('config.php');
require_once('config/constants.php');
require_once('config/database.php');

// Router đơn giản
$url = isset($_GET['url']) ? $_GET['url'] : 'home';

// Xử lý routing
switch ($url) {
    case 'home':
    default:
        require_once 'app/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
        
    // Thêm các route khác ở đây
    case 'products':
        // require_once 'app/controllers/ProductController.php';
        echo "Trang sản phẩm - Đang phát triển";
        break;
        
    case 'cart':
        // require_once 'app/controllers/CartController.php';
        echo "Giỏ hàng - Đang phát triển";
        break;
}

ob_end_flush();