<?php
// config/database.php

// 1. Kiểm tra biến môi trường (Render thường dùng tên khác hoặc dùng chung DB_HOST)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'pharmacy_db');
define('DB_PORT', getenv('DB_PORT') ?: '3306'); // MySQL mặc định là 3306

try {
    // 2. Sửa "pgsql" thành "mysql" và cập nhật lại DSN
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (PDOException $e) {
    // Đổi thông báo lỗi cho đúng ngữ cảnh
    die("Lỗi kết nối MySQL: " . $e->getMessage());
}
