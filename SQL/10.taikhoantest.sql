-- Đảm bảo đang sử dụng đúng database
USE pharmacy_db;

-- Xóa tài khoản cũ nếu bị trùng tên đăng nhập
DELETE FROM users WHERE username IN ('admin', 'duocsi');

-- Thêm tài khoản mới với mật khẩu: Check@123
INSERT INTO users (role_id, username, password_hash, full_name, email, phone, address) VALUES
(1, 'admin', '$2y$10$NNtRsIZ9MmXx1x93czII3OKYVA0EmkrLiKEgaLAPan3RAqeR/JWDy', 'Quản trị viên', 'admin@pharmacy.vn', '0901234567', 'Hệ thống'),
(2, 'duocsi', '$2y$10$NNtRsIZ9MmXx1x93czII3OKYVA0EmkrLiKEgaLAPan3RAqeR/JWDy', 'Dược sĩ chuyên môn', 'duocsi@pharmacy.vn', '0907654321', 'Nhà thuốc 1985');
