<?php
// Bật hiển thị lỗi tối đa để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Nạp file kết nối
require_once 'app/models/BaseModel.php';

try {
    // 2. Khởi tạo đối tượng kết nối
    $test = new BaseModel();
    
    // 3. Nếu chạy đến đây mà không chết, nghĩa là kết nối PDO thành công
    echo "<h2 style='color: green;'>✅ Kết nối Database THÀNH CÔNG!</h2>";
    
    // 4. Test thử truy vấn một bảng (ví dụ bảng roles hoặc users)
    // Thay 'users' bằng tên bảng bất kỳ bạn đã tạo trong SQL
    /*
    $query = $test->db->query("SELECT DATABASE()");
    $dbName = $query->fetchColumn();
    echo "Bạn đang kết nối tới Database: <strong>" . $dbName . "</strong>";
    */

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Kết nối THẤT BẠI!</h2>";
    echo "Lỗi: " . $e->getMessage();
}