<?php
// config/database.php
<<<<<<< HEAD
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'pharmacy_db');
=======

define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_PORT', getenv('DB_PORT') ?: '27580'); // Lấy từ Render hoặc mặc định Aiven

try {
    // Chuỗi kết nối MySQL với Port cụ thể
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Một số gói Aiven yêu cầu SSL, nếu vẫn lỗi hãy thử thêm dòng dưới:
        // PDO::MYSQL_ATTR_SSL_CA => true, 
    ]);
} catch (PDOException $e) {
    die("Lỗi kết nối MySQL: " . $e->getMessage());
}
>>>>>>> 5334814bd38a7c82f4343da8c763297056024d87
