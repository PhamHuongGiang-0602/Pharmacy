-- Script tạo các bảng hỗ trợ Chat và Video Call

-- Cập nhật bảng users nếu chưa có cột role (bỏ qua nếu đã có)
-- ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('customer', 'doctor') NOT NULL DEFAULT 'customer';

CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    customer_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_chat (doctor_id, customer_id)
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS call_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    caller_id INT NOT NULL,
    receiver_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NULL,
    status ENUM('missed', 'answered', 'rejected') NOT NULL DEFAULT 'missed',
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
);

-- Tracks Doctor availability for routing
CREATE TABLE IF NOT EXISTS doctor_status (
    doctor_id INT PRIMARY KEY,
    is_online BOOLEAN DEFAULT FALSE,
    is_available BOOLEAN DEFAULT TRUE,
    last_assigned_at DATETIME DEFAULT NULL,
    last_heartbeat DATETIME DEFAULT NULL,
    INDEX(is_online, is_available, last_assigned_at)
);

-- Master table for the Customer's Request Queue
CREATE TABLE IF NOT EXISTS consultation_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    current_doctor_id INT NULL,
    status ENUM('pending', 'accepted', 'exhausted', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Logs rejections or timeouts to prevent circular routing
CREATE TABLE IF NOT EXISTS request_rejections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    doctor_id INT NOT NULL,
    reason ENUM('rejected', 'timeout') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES consultation_requests(id) ON DELETE CASCADE
);