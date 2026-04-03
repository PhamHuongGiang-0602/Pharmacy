<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
ob_start();

require_once('config.php');
require_once('config/constants.php');
require_once 'config/database.php';

// Giả sử dùng Router đơn giản qua biến $_GET['url']
$url = isset($_GET['url']) ? $_GET['url'] : 'home';

// Nếu là home thì load giao diện Long Châu
if ($url == 'home') {
    require_once 'app/views/home.php';
} else {
    echo "Trang 404 hoặc xử lý Controller tại đây";
}