<?php
// config/database.php

// Nếu chạy trên Render, lấy thông tin từ Environment Variables
// Nếu không có, sẽ mặc định dùng thông số localhost (để bạn vẫn code được ở máy cá nhân)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'pharmacy_db');
// Đoạn code kết nối PDO cho Postgres
try {
    $host = DB_HOST;
    $db   = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;
    $port = "5432"; // Cổng mặc định của Postgres

    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Lỗi kết nối Postgres: " . $e->getMessage());
}
