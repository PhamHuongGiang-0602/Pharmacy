<?php
// config/database.php

// Lấy thông tin từ Environment Variables trên Render, nếu không có sẽ dùng mặc định (localhost)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'pharmacy_db');
define('DB_PORT', getenv('DB_PORT') ?: '3306');

try {
    // Cấu hình chuỗi kết nối DSN cho MySQL
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Đẩy lỗi ra ngoại lệ để dễ debug
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Trả về dữ liệu dạng mảng kết hợp
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",    // Hỗ trợ tiếng Việt có dấu
        PDO::ATTR_TIMEOUT            => 5                       // Thời gian chờ kết nối tối đa 5s
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // In ra lỗi chi tiết để bạn biết chính xác host/port nào đang bị từ chối
    error_log("Connection Error: " . $e->getMessage());
    die("Lỗi kết nối cơ sở dữ liệu. Vui lòng kiểm tra lại cấu hình Host/Port.");
}
