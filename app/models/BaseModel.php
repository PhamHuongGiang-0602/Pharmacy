<?php
// app/models/BaseModel.php

class BaseModel {
    protected $db;

    public function __construct() {
        // Nạp thông số từ file config
        $config = require __DIR__ . '/../../config/database.php';
        
        $dsn = "mysql:host={$config['host']};dbname={$config['db_name']};charset={$config['charset']}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Đẩy lỗi ra để dễ sửa
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Trả về mảng kết hợp
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->db = new PDO($dsn, $config['user'], $config['pass'], $options);
        } catch (PDOException $e) {
            // Nếu lỗi kết nối, sẽ hiển thị thông báo thay vì trắng trang
            die("Lỗi kết nối Database: " . $e->getMessage());
        }
    }
}