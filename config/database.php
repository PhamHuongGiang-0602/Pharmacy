<?php
// config/database.php

// Nếu chạy trên Render, lấy thông tin từ Environment Variables
// Nếu không có, sẽ mặc định dùng thông số localhost (để bạn vẫn code được ở máy cá nhân)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'pharmacy_db');
