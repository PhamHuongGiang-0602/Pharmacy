-- =====================================================
-- DATABASE QUẢN LÝ NHÀ THUỐC - CHUYÊN NGHIỆP
-- Phiên bản: 1.0
-- Hỗ trợ: MySQL 5.7+ / MariaDB 10.3+
-- Tính năng: Quản lý lô hàng (FEFO), Phân quyền, Đơn thuốc
-- =====================================================

DROP DATABASE IF EXISTS pharmacy_db;
CREATE DATABASE pharmacy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pharmacy_db;

-- Tắt kiểm tra khóa ngoại để chèn dữ liệu mẫu
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. BẢNG PHÂN QUYỀN & NGƯỜI DÙNG
-- =====================================================

-- Bảng vai trò (roles)
CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE COMMENT 'Admin, Dược sĩ, Nhân viên kho, Khách hàng',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng người dùng
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL COMMENT 'Mã hóa bằng password_hash() của PHP',
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role_id)
) ENGINE=InnoDB;

-- Bảng quyền (permissions)
CREATE TABLE permissions (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(100) NOT NULL UNIQUE COMMENT 'view_products, manage_inventory, approve_orders',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng phân quyền cho từng vai trò
CREATE TABLE role_permissions (
    role_id INT,
    permission_id INT,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 2. BẢNG DANH MỤC & SẢN PHẨM
-- =====================================================

-- Bảng nhà sản xuất
CREATE TABLE manufacturers (
    manufacturer_id INT PRIMARY KEY AUTO_INCREMENT,
    manufacturer_name VARCHAR(200) NOT NULL,
    country VARCHAR(100),
    website VARCHAR(255),
    contact_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng danh mục sản phẩm
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_category_id INT DEFAULT NULL COMMENT 'Hỗ trợ danh mục con',
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    INDEX idx_parent (parent_category_id)
) ENGINE=InnoDB;

-- Bảng sản phẩm (thuốc)
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    manufacturer_id INT,
    product_name VARCHAR(255) NOT NULL,
    generic_name VARCHAR(255) COMMENT 'Tên hoạt chất chính',
    dosage_form VARCHAR(100) COMMENT 'Viên nén, Viên nang, Siro, Ống tiêm...',
    strength VARCHAR(100) COMMENT 'Nồng độ: 500mg, 10ml...',
    unit VARCHAR(50) DEFAULT 'Viên' COMMENT 'Đơn vị tính: Viên, Hộp, Chai, Tuýp',
    
    -- Thông tin y tế
    active_ingredients TEXT COMMENT 'Danh sách hoạt chất, phân tách bằng dấu ;',
    indications TEXT COMMENT 'Công dụng, chỉ định',
    contraindications TEXT COMMENT 'Chống chỉ định',
    side_effects TEXT COMMENT 'Tác dụng phụ',
    dosage_instructions TEXT COMMENT 'Liều dùng, cách dùng',
    storage_conditions TEXT COMMENT 'Điều kiện bảo quản',
    
    -- Phân loại
    is_prescription_required BOOLEAN DEFAULT FALSE COMMENT 'Có cần đơn thuốc không',
    is_otc BOOLEAN DEFAULT TRUE COMMENT 'Thuốc không kê đơn (OTC)',
    
    -- Giá & hình ảnh
    price DECIMAL(12, 2) NOT NULL DEFAULT 0,
    discount_percent DECIMAL(5, 2) DEFAULT 0,
    image_url VARCHAR(255),
    additional_images TEXT COMMENT 'Lưu nhiều ảnh, phân tách bằng dấu ;',
    
    -- Trạng thái
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,
    FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(manufacturer_id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_name (product_name),
    INDEX idx_generic (generic_name),
    INDEX idx_prescription (is_prescription_required),
    FULLTEXT idx_search (product_name, generic_name, active_ingredients)
) ENGINE=InnoDB;

-- =====================================================
-- 3. QUẢN LÝ KHO & LÔ HÀNG (FEFO)
-- =====================================================

-- Bảng nhà cung cấp
CREATE TABLE suppliers (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    tax_code VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng phiếu nhập kho
CREATE TABLE stock_receipts (
    receipt_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_id INT,
    user_id INT COMMENT 'Nhân viên nhập kho',
    receipt_date DATE NOT NULL,
    invoice_number VARCHAR(100),
    total_amount DECIMAL(15, 2),
    notes TEXT,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_date (receipt_date),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Bảng lô hàng (QUAN TRỌNG - HỖ TRỢ FEFO)
CREATE TABLE batches (
    batch_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    receipt_id INT,
    batch_number VARCHAR(100) NOT NULL COMMENT 'Số lô do nhà sản xuất cung cấp',
    manufacture_date DATE NOT NULL,
    expiry_date DATE NOT NULL COMMENT 'Hạn sử dụng - QUAN TRỌNG cho FEFO',
    
    quantity_received INT NOT NULL DEFAULT 0 COMMENT 'Số lượng nhập vào',
    quantity_remaining INT NOT NULL DEFAULT 0 COMMENT 'Số lượng còn lại',
    
    purchase_price DECIMAL(12, 2) COMMENT 'Giá nhập',
    selling_price DECIMAL(12, 2) COMMENT 'Giá bán',
    
    storage_location VARCHAR(100) COMMENT 'Vị trí lưu kho: Kệ A1, Ngăn B2...',
    
    -- Cảnh báo hạn sử dụng
    expiry_alert_days INT DEFAULT 90 COMMENT 'Cảnh báo trước X ngày hết hạn',
    is_expired BOOLEAN DEFAULT FALSE,
    days_to_expiry INT DEFAULT 0,
    
    status ENUM('active', 'near_expiry', 'expired', 'recalled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (receipt_id) REFERENCES stock_receipts(receipt_id) ON DELETE SET NULL,
    INDEX idx_product (product_id),
    INDEX idx_expiry (expiry_date),
    INDEX idx_batch_number (batch_number),
    INDEX idx_status (status),
    UNIQUE KEY unique_batch (product_id, batch_number)
) ENGINE=InnoDB;

-- Bảng chi tiết phiếu nhập
CREATE TABLE stock_receipt_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    receipt_id INT NOT NULL,
    batch_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(15, 2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    FOREIGN KEY (receipt_id) REFERENCES stock_receipts(receipt_id) ON DELETE CASCADE,
    FOREIGN KEY (batch_id) REFERENCES batches(batch_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Bảng tương tác thuốc (DRUG INTERACTIONS)
CREATE TABLE drug_interactions (
    interaction_id INT PRIMARY KEY AUTO_INCREMENT,
    drug_a_id INT NOT NULL COMMENT 'Thuốc thứ nhất',
    drug_b_id INT NOT NULL COMMENT 'Thuốc thứ hai',
    severity ENUM('mild', 'moderate', 'severe', 'contraindicated') DEFAULT 'moderate',
    description TEXT COMMENT 'Mô tả tương tác',
    recommendation TEXT COMMENT 'Khuyến nghị cho bác sĩ/dược sĩ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drug_a_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (drug_b_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_interaction (drug_a_id, drug_b_id)
) ENGINE=InnoDB;

-- =====================================================
-- 5. GIỎ HÀNG & ĐơN HÀNG
-- =====================================================

-- Bảng giỏ hàng
CREATE TABLE carts (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_cart (user_id)
) ENGINE=InnoDB;

-- Bảng chi tiết giỏ hàng
CREATE TABLE cart_items (
    cart_item_id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_product (cart_id, product_id)
) ENGINE=InnoDB;

-- Bảng mã giảm giá (vouchers)
CREATE TABLE vouchers (
    voucher_id INT PRIMARY KEY AUTO_INCREMENT,
    voucher_code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    discount_type ENUM('percent', 'fixed') DEFAULT 'percent',
    discount_value DECIMAL(10, 2) NOT NULL COMMENT 'Phần trăm hoặc số tiền cố định',
    min_order_amount DECIMAL(12, 2) DEFAULT 0 COMMENT 'Giá trị đơn hàng tối thiểu',
    max_discount_amount DECIMAL(12, 2) COMMENT 'Giảm tối đa (nếu là %)',
    usage_limit INT COMMENT 'Số lượt sử dụng tối đa',
    used_count INT DEFAULT 0,
    valid_from DATE,
    valid_to DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (voucher_code),
    INDEX idx_validity (valid_from, valid_to)
) ENGINE=InnoDB;

-- Bảng đơn hàng
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    voucher_id INT,
    
    -- Thông tin giao hàng
    shipping_name VARCHAR(100) NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_note TEXT,
    
    -- Giá trị đơn hàng
    subtotal DECIMAL(15, 2) NOT NULL COMMENT 'Tổng tiền trước giảm giá',
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    shipping_fee DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(15, 2) NOT NULL COMMENT 'Tổng tiền sau giảm giá + ship',
    
    -- Đơn thuốc
    has_prescription BOOLEAN DEFAULT FALSE COMMENT 'Có đính kèm đơn thuốc không',
    prescription_image VARCHAR(255) COMMENT 'Đường dẫn ảnh đơn thuốc',
    prescription_verified BOOLEAN DEFAULT FALSE COMMENT 'Dược sĩ đã duyệt đơn',
    verified_by INT COMMENT 'Dược sĩ duyệt đơn',
    verified_at DATETIME,
    
    -- Trạng thái
    status ENUM('pending', 'confirmed', 'preparing', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    payment_method ENUM('cod', 'bank_transfer', 'e_wallet') DEFAULT 'cod',
    
    -- Ghi chú
    admin_note TEXT COMMENT 'Ghi chú nội bộ của admin',
    cancel_reason TEXT,
    
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(voucher_id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_date (order_date),
    INDEX idx_prescription (has_prescription, prescription_verified)
) ENGINE=InnoDB;

-- Bảng chi tiết đơn hàng
CREATE TABLE order_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    batch_id INT COMMENT 'Lô hàng được xuất (FEFO)',
    
    product_name VARCHAR(255) NOT NULL COMMENT 'Lưu lại tên sản phẩm tại thời điểm mua',
    quantity INT NOT NULL,
    unit_price DECIMAL(12, 2) NOT NULL COMMENT 'Giá tại thời điểm mua',
    discount_percent DECIMAL(5, 2) DEFAULT 0,
    subtotal DECIMAL(15, 2) GENERATED ALWAYS AS (quantity * unit_price * (1 - discount_percent/100)) STORED,
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    FOREIGN KEY (batch_id) REFERENCES batches(batch_id) ON DELETE SET NULL,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- =====================================================
-- 6. ĐÁNH GIÁ & PHẢN HỒI
-- =====================================================

CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT COMMENT 'Chỉ cho phép đánh giá sau khi mua',
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    admin_reply TEXT,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE SET NULL,
    INDEX idx_product (product_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB;

-- =====================================================
-- 7. THỐNG KÊ & LOG HỆ THỐNG
-- =====================================================

-- Bảng lịch sử giá sản phẩm
CREATE TABLE price_history (
    history_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    old_price DECIMAL(12, 2),
    new_price DECIMAL(12, 2) NOT NULL,
    changed_by INT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Bảng log hoạt động
CREATE TABLE activity_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL COMMENT 'login, logout, create_order, update_product...',
    table_name VARCHAR(50),
    record_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user_action (user_id, action),
    INDEX idx_date (created_at)
) ENGINE=InnoDB;

-- =====================================================
-- 8. TRIGGERS & STORED PROCEDURES
-- =====================================================

-- Trigger: Tự động cập nhật số lượng lô hàng khi có đơn hàng
DELIMITER $$

CREATE TRIGGER after_order_detail_insert
AFTER INSERT ON order_details
FOR EACH ROW
BEGIN
    -- Cập nhật số lượng còn lại trong lô hàng
    IF NEW.batch_id IS NOT NULL THEN
        UPDATE batches 
        SET quantity_remaining = quantity_remaining - NEW.quantity
        WHERE batch_id = NEW.batch_id;
    END IF;
END$$

-- Trigger: Tự động cập nhật trạng thái lô hàng theo hạn sử dụng
CREATE TRIGGER before_batch_insert
BEFORE INSERT ON batches
FOR EACH ROW
BEGIN
    SET NEW.is_expired = (NEW.expiry_date < CURDATE());
    SET NEW.days_to_expiry = DATEDIFF(NEW.expiry_date, CURDATE());
    
    -- Nếu đã hết hạn
    IF NEW.expiry_date < CURDATE() THEN
        SET NEW.status = 'expired';
    -- Nếu sắp hết hạn (trong vòng 90 ngày)
    ELSEIF DATEDIFF(NEW.expiry_date, CURDATE()) <= NEW.expiry_alert_days THEN
        SET NEW.status = 'near_expiry';
    ELSE
        SET NEW.status = 'active';
    END IF;
END$$

CREATE TRIGGER before_batch_update
BEFORE UPDATE ON batches
FOR EACH ROW
BEGIN
    SET NEW.is_expired = (NEW.expiry_date < CURDATE());
    SET NEW.days_to_expiry = DATEDIFF(NEW.expiry_date, CURDATE());
    
    -- Nếu đã hết hạn
    IF NEW.expiry_date < CURDATE() THEN
        SET NEW.status = 'expired';
    -- Nếu sắp hết hạn (trong vòng 90 ngày)
    ELSEIF DATEDIFF(NEW.expiry_date, CURDATE()) <= NEW.expiry_alert_days THEN
        SET NEW.status = 'near_expiry';
    ELSE
        SET NEW.status = 'active';
    END IF;
END$$

-- Trigger: Log thay đổi giá sản phẩm
CREATE TRIGGER after_product_price_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF OLD.price != NEW.price THEN
        INSERT INTO price_history (product_id, old_price, new_price)
        VALUES (NEW.product_id, OLD.price, NEW.price);
    END IF;
END$$

-- Stored Procedure: Lấy lô hàng theo FEFO (First Expired, First Out)
CREATE PROCEDURE get_batch_fefo(
    IN p_product_id INT,
    IN p_quantity_needed INT
)
BEGIN
    SELECT 
        batch_id,
        batch_number,
        quantity_remaining,
        expiry_date,
        selling_price
    FROM batches
    WHERE product_id = p_product_id
        AND quantity_remaining > 0
        AND status = 'active'
        AND expiry_date > CURDATE()
    ORDER BY expiry_date ASC, manufacture_date ASC
    LIMIT 5;
END$$

-- Stored Procedure: Kiểm tra tương tác thuốc trong giỏ hàng
CREATE PROCEDURE check_cart_interactions(
    IN p_cart_id INT
)
BEGIN
    SELECT DISTINCT
        di.interaction_id,
        p1.product_name AS drug_1,
        p2.product_name AS drug_2,
        di.severity,
        di.description,
        di.recommendation
    FROM cart_items ci1
    JOIN cart_items ci2 ON ci1.cart_id = ci2.cart_id AND ci1.product_id < ci2.product_id
    JOIN drug_interactions di ON (
        (di.drug_a_id = ci1.product_id AND di.drug_b_id = ci2.product_id) OR
        (di.drug_a_id = ci2.product_id AND di.drug_b_id = ci1.product_id)
    )
    JOIN products p1 ON p1.product_id = ci1.product_id
    JOIN products p2 ON p2.product_id = ci2.product_id
    WHERE ci1.cart_id = p_cart_id;
END$$

-- Stored Procedure: Thống kê doanh thu theo tháng
CREATE PROCEDURE get_monthly_revenue(
    IN p_year INT,
    IN p_month INT
)
BEGIN
    SELECT 
        DATE(order_date) as order_date,
        COUNT(order_id) as total_orders,
        SUM(total_amount) as daily_revenue,
        SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as completed_revenue
    FROM orders
    WHERE YEAR(order_date) = p_year 
        AND MONTH(order_date) = p_month
    GROUP BY DATE(order_date)
    ORDER BY order_date;
END$$

-- Function: Tính tuổi lô hàng (ngày)
CREATE FUNCTION get_batch_age(p_batch_id INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE batch_age INT;
    
    SELECT DATEDIFF(CURDATE(), manufacture_date)
    INTO batch_age
    FROM batches
    WHERE batch_id = p_batch_id;
    
    RETURN IFNULL(batch_age, 0);
END$$

DELIMITER ;

-- =====================================================
-- 9. VIEWS (Khung nhìn tiện ích)
-- =====================================================

-- View: Tồn kho chi tiết theo sản phẩm
CREATE VIEW v_inventory_summary AS
SELECT 
    p.product_id,
    p.product_name,
    p.generic_name,
    c.category_name,
    COUNT(DISTINCT b.batch_id) as total_batches,
    SUM(b.quantity_remaining) as total_quantity,
    MIN(b.expiry_date) as earliest_expiry,
    SUM(CASE WHEN b.status = 'near_expiry' THEN b.quantity_remaining ELSE 0 END) as near_expiry_quantity,
    SUM(CASE WHEN b.status = 'expired' THEN b.quantity_remaining ELSE 0 END) as expired_quantity,
    p.price,
    SUM(b.quantity_remaining) * p.price as inventory_value
FROM products p
LEFT JOIN batches b ON p.product_id = b.product_id
LEFT JOIN categories c ON p.category_id = c.category_id
WHERE p.is_active = TRUE
GROUP BY p.product_id, p.product_name, p.generic_name, c.category_name, p.price;

-- View: Đơn hàng cần duyệt đơn thuốc
CREATE VIEW v_pending_prescriptions AS
SELECT 
    o.order_id,
    o.order_date,
    u.full_name as customer_name,
    u.phone as customer_phone,
    o.prescription_image,
    o.shipping_address,
    o.total_amount,
    o.status
FROM orders o
JOIN users u ON o.user_id = u.user_id
WHERE o.has_prescription = TRUE 
    AND o.prescription_verified = FALSE
    AND o.status NOT IN ('cancelled', 'completed')
ORDER BY o.order_date DESC;

-- View: Sản phẩm bán chạy
CREATE VIEW v_bestsellers AS
SELECT 
    p.product_id,
    p.product_name,
    p.image_url,
    p.price,
    COUNT(od.detail_id) as times_ordered,
    SUM(od.quantity) as total_sold,
    SUM(od.subtotal) as total_revenue,
    AVG(r.rating) as avg_rating,
    COUNT(DISTINCT r.review_id) as review_count
FROM products p
LEFT JOIN order_details od ON p.product_id = od.product_id
LEFT JOIN orders o ON od.order_id = o.order_id AND o.status = 'completed'
LEFT JOIN reviews r ON p.product_id = r.product_id
WHERE p.is_active = TRUE
GROUP BY p.product_id, p.product_name, p.image_url, p.price
HAVING total_sold > 0
ORDER BY total_sold DESC;

-- View: Cảnh báo lô hàng sắp hết hạn
CREATE VIEW v_expiry_alerts AS
SELECT 
    b.batch_id,
    p.product_name,
    b.batch_number,
    b.manufacture_date,
    b.expiry_date,
    b.days_to_expiry,
    b.quantity_remaining,
    b.storage_location,
    b.status,
    CASE 
        WHEN b.days_to_expiry <= 0 THEN 'danger'
        WHEN b.days_to_expiry <= 30 THEN 'warning'
        WHEN b.days_to_expiry <= 90 THEN 'info'
        ELSE 'normal'
    END as alert_level
FROM batches b
JOIN products p ON b.product_id = p.product_id
WHERE b.quantity_remaining > 0 
    AND b.days_to_expiry <= 90
ORDER BY b.expiry_date ASC;

-- =====================================================
-- 10. DỮ LIỆU MẪU (SAMPLE DATA)
-- =====================================================

-- 10.1 Vai trò
INSERT INTO roles (role_name, description) VALUES
('Admin', 'Quản trị viên hệ thống - toàn quyền'),
('Dược sĩ', 'Dược sĩ - duyệt đơn thuốc, tư vấn khách hàng'),
('Nhân viên kho', 'Nhân viên quản lý kho - nhập xuất hàng'),
('Khách hàng', 'Khách hàng - mua hàng trực tuyến');

-- 10.2 Quyền
INSERT INTO permissions (permission_name, description) VALUES
('view_dashboard', 'Xem dashboard thống kê'),
('manage_products', 'Quản lý sản phẩm (CRUD)'),
('manage_inventory', 'Quản lý kho hàng'),
('manage_orders', 'Quản lý đơn hàng'),
('approve_prescriptions', 'Duyệt đơn thuốc'),
('manage_users', 'Quản lý người dùng'),
('view_reports', 'Xem báo cáo'),
('manage_suppliers', 'Quản lý nhà cung cấp');

-- Phân quyền cho Admin
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, permission_id FROM permissions;

-- Phân quyền cho Dược sĩ
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, 1), (2, 4), (2, 5), (2, 7);

-- Phân quyền cho Nhân viên kho
INSERT INTO role_permissions (role_id, permission_id) VALUES
(3, 3), (3, 8);

-- 10.3 Người dùng (Mật khẩu: "123456")
INSERT INTO users (role_id, username, password_hash, full_name, email, phone, address) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Admin', 'admin@pharmacy.com', '0901234567', '123 Nguyễn Huệ, Q.1, TP.HCM'),
(2, 'duocsi01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Hoa', 'duocsi@pharmacy.com', '0907654321', '456 Lê Lợi, Q.1, TP.HCM'),
(3, 'kho01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn Kho', 'kho@pharmacy.com', '0909876543', '789 Trần Hưng Đạo, Q.5, TP.HCM'),
(4, 'khach01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Lan', 'lan@gmail.com', '0912345678', '321 Võ Văn Tần, Q.3, TP.HCM'),
(4, 'khach02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Văn Nam', 'nam@gmail.com', '0923456789', '654 Cách Mạng Tháng 8, Q.10, TP.HCM');

-- 10.4 Nhà sản xuất
INSERT INTO manufacturers (manufacturer_name, country, website) VALUES
('Công ty Dược Hậu Giang', 'Việt Nam', 'https://www.dhhg.com.vn'),
('Công ty Dược Traphaco', 'Việt Nam', 'https://www.traphaco.com.vn'),
('Pfizer Inc.', 'Hoa Kỳ', 'https://www.pfizer.com'),
('Sanofi', 'Pháp', 'https://www.sanofi.com'),
('Abbott Laboratories', 'Hoa Kỳ', 'https://www.abbott.com'),
('Roche', 'Thụy Sĩ', 'https://www.roche.com');

-- 10.5 Danh mục
INSERT INTO categories (category_name, description, parent_category_id) VALUES
('Thuốc kê đơn', 'Thuốc chỉ bán theo đơn của bác sĩ', NULL),
('Thuốc không kê đơn', 'Thuốc OTC - bán tự do', NULL),
('Thực phẩm chức năng', 'Vitamin, khoáng chất, thảo dược', NULL),
('Chăm sóc cá nhân', 'Sản phẩm vệ sinh, làm đẹp', NULL),
('Thiết bị y tế', 'Nhiệt kế, huyết áp, đường huyết', NULL),
('Kháng sinh', 'Thuốc kháng sinh', 1),
('Giảm đau hạ sốt', 'Thuốc giảm đau, hạ sốt', 2),
('Vitamin', 'Vitamin tổng hợp, vitamin đơn', 3);

-- 10.6 Sản phẩm
INSERT INTO products (category_id, manufacturer_id, product_name, generic_name, dosage_form, strength, unit, 
    active_ingredients, indications, contraindications, side_effects, dosage_instructions, storage_conditions,
    is_prescription_required, is_otc, price, image_url) VALUES

(6, 1, 'Amoxicillin 500mg DHG', 'Amoxicillin', 'Viên nang', '500mg', 'Viên',
    'Amoxicillin trihydrate 500mg',
    'Nhiễm khuẩn đường hô hấp, tai mũi họng, da',
    'Dị ứng với Penicillin',
    'Buồn nôn, tiêu chảy, phát ban da',
    'Người lớn: 500mg x 3 lần/ngày. Uống sau ăn.',
    'Nơi khô mát, tránh ánh sáng. Nhiệt độ dưới 30°C',
    TRUE, FALSE, 45000, 'amoxicillin.jpg'),

(7, 1, 'Paracetamol 500mg', 'Paracetamol', 'Viên nén', '500mg', 'Viên',
    'Paracetamol 500mg',
    'Giảm đau, hạ sốt',
    'Suy gan nặng',
    'Hiếm gặp: Phát ban, rối loạn tiêu hóa',
    'Người lớn: 1-2 viên x 3-4 lần/ngày. Không quá 4g/ngày.',
    'Bảo quản nơi khô, tránh ánh sáng',
    FALSE, TRUE, 15000, 'paracetamol.jpg'),

(7, 3, 'Aspirin 100mg', 'Aspirin', 'Viên nén bao phim', '100mg', 'Viên',
    'Acid acetylsalicylic 100mg',
    'Dự phòng đột quỵ, nhồi máu cơ tim',
    'Loét dạ dày, xuất huyết tiêu hóa',
    'Đau dạ dày, buồn nôn',
    '1 viên/ngày, uống sau ăn',
    'Nơi khô mát, nhiệt độ dưới 25°C',
    TRUE, FALSE, 35000, 'aspirin.jpg'),

(8, 5, 'Vitamin C 1000mg Abbott', 'Ascorbic Acid', 'Viên sủi', '1000mg', 'Viên',
    'Ascorbic Acid 1000mg',
    'Bổ sung vitamin C, tăng sức đề kháng',
    'Sỏi thận oxalat',
    'Hiếm gặp: Tiêu chảy khi dùng liều cao',
    '1 viên/ngày, hòa tan vào 200ml nước',
    'Nơi khô mát, tránh ẩm',
    FALSE, TRUE, 120000, 'vitamin-c.jpg'),

(8, 5, 'Vitamin D3 1000IU', 'Cholecalciferol', 'Viên nang mềm', '1000IU', 'Viên',
    'Cholecalciferol 1000IU',
    'Phòng ngừa thiếu vitamin D, loãng xương',
    'Tăng canxi máu',
    'Hiếm gặp: Táo bón, buồn nôn',
    '1 viên/ngày, uống cùng bữa ăn có dầu mỡ',
    'Bảo quản nơi khô, tránh ánh sáng',
    FALSE, TRUE, 180000, 'vitamin-d3.jpg'),

(2, 2, 'Oresol 245', 'Oresol', 'Gói bột pha', '245mg', 'Gói',
    'Glucose, Natri clorid, Kali clorid, Natri citrat',
    'Bù nước điện giải khi tiêu chảy, mất nước',
    'Suy thận nặng',
    'Không',
    'Pha 1 gói vào 200ml nước sôi để nguội. Uống ngay sau pha.',
    'Nơi khô mát',
    FALSE, TRUE, 5000, 'oresol.jpg'),

(1, 4, 'Metformin 500mg', 'Metformin', 'Viên nén bao phim', '500mg', 'Viên',
    'Metformin HCl 500mg',
    'Điều trị đái tháo đường type 2',
    'Suy thận, suy gan nặng, nhiễm toan ceton',
    'Buồn nôn, tiêu chảy, đầy hơi',
    '500mg x 2-3 lần/ngày, uống cùng bữa ăn',
    'Nơi khô mát, tránh ánh sáng',
    TRUE, FALSE, 55000, 'metformin.jpg'),

(1, 6, 'Lipitor 20mg', 'Atorvastatin', 'Viên nén bao phim', '20mg', 'Viên',
    'Atorvastatin 20mg',
    'Giảm cholesterol máu, phòng ngừa tim mạch',
    'Bệnh gan đang tiến triển, thai kỳ',
    'Đau cơ, tăng men gan',
    '1 viên/ngày, có thể uống bất kỳ lúc nào',
    'Nhiệt độ phòng (15-30°C)',
    TRUE, FALSE, 280000, 'lipitor.jpg'),

(3, 5, 'Ensure Gold 850g', 'Ensure', 'Bột pha sữa', '850g', 'Hộp',
    'Protein, Vitamin, Khoáng chất, HMB',
    'Bổ sung dinh dưỡng cho người lớn tuổi',
    'Galactosemia',
    'Hiếm gặp: Đầy hơi, khó tiêu',
    'Pha 6 muỗng (51g) vào 190ml nước ấm. 2 lần/ngày.',
    'Nơi khô mát, sau khi mở nắp dùng trong 3 tuần',
    FALSE, TRUE, 680000, 'ensure-gold.jpg'),

(5, 5, 'Nhiệt kế điện tử Omron', 'Thermometer', 'Thiết bị', 'N/A', 'Cái',
    'N/A',
    'Đo nhiệt độ cơ thể',
    'Không',
    'Không',
    'Đặt đầu đo dưới lưỡi/nách, đợi tín hiệu beep',
    'Nơi khô, tránh va đập mạnh',
    FALSE, TRUE, 150000, 'thermometer.jpg');

-- 10.7 Nhà cung cấp
INSERT INTO suppliers (supplier_name, contact_person, phone, email, address, tax_code) VALUES
('Công ty TNHH Dược phẩm Phúc Vinh', 'Nguyễn Văn Phúc', '0283.123.4567', 'phucvinh@pharma.vn', '12 Đinh Tiên Hoàng, Q.1, TP.HCM', '0123456789'),
('Công ty CP Dược liệu Trung ương 3', 'Trần Thị Mai', '0284.234.5678', 'trunguong3@dlt.vn', '456 Võ Thị Sáu, Q.3, TP.HCM', '0234567890');

-- 10.8 Phiếu nhập kho
INSERT INTO stock_receipts (supplier_id, user_id, receipt_date, invoice_number, total_amount, status) VALUES
(1, 3, '2026-03-15', 'NK-2026-001', 50000000, 'completed'),
(2, 3, '2026-03-20', 'NK-2026-002', 35000000, 'completed');

-- 10.9 Lô hàng (với FEFO)
INSERT INTO batches (product_id, receipt_id, batch_number, manufacture_date, expiry_date, 
    quantity_received, quantity_remaining, purchase_price, selling_price, storage_location) VALUES

-- Amoxicillin - 3 lô khác nhau
(1, 1, 'AMX-2025-12-001', '2025-12-01', '2027-12-01', 500, 450, 35000, 45000, 'Kệ A1-01'),
(1, 1, 'AMX-2026-01-015', '2026-01-15', '2028-01-15', 500, 500, 36000, 45000, 'Kệ A1-02'),
(1, 2, 'AMX-2026-02-010', '2026-02-10', '2028-02-10', 300, 300, 36500, 45000, 'Kệ A1-03'),

-- Paracetamol - sắp hết hạn
(2, 1, 'PARA-2025-06-020', '2025-06-20', '2026-06-20', 1000, 850, 12000, 15000, 'Kệ B2-05'),
(2, 2, 'PARA-2026-03-01', '2026-03-01', '2028-03-01', 2000, 2000, 12500, 15000, 'Kệ B2-06'),

-- Aspirin
(3, 1, 'ASP-2025-11-10', '2025-11-10', '2027-11-10', 300, 280, 28000, 35000, 'Kệ A2-03'),

-- Vitamin C
(4, 2, 'VITC-2026-02-15', '2026-02-15', '2028-02-15', 200, 180, 95000, 120000, 'Kệ C1-01'),

-- Vitamin D3
(5, 2, 'VITD3-2026-01-20', '2026-01-20', '2028-01-20', 150, 140, 145000, 180000, 'Kệ C1-02'),

-- Oresol
(6, 1, 'ORS-2026-03-10', '2026-03-10', '2028-03-10', 5000, 4800, 3500, 5000, 'Kệ D3-01'),

-- Metformin
(7, 1, 'MET-2025-12-25', '2025-12-25', '2027-12-25', 400, 350, 42000, 55000, 'Kệ A3-04'),

-- Lipitor
(8, 2, 'LIP-2026-02-05', '2026-02-05', '2028-02-05', 100, 95, 225000, 280000, 'Kệ A4-01'),

-- Ensure
(9, 2, 'ENS-2026-03-01', '2026-03-01', '2027-09-01', 50, 45, 550000, 680000, 'Kệ E1-01'),

-- Nhiệt kế
(10, 1, 'THER-2026-01-10', '2026-01-10', '2031-01-10', 30, 25, 120000, 150000, 'Kệ F2-03');

-- 10.10 Chi tiết phiếu nhập
INSERT INTO stock_receipt_details (receipt_id, batch_id, quantity, unit_price) VALUES
(1, 1, 500, 35000),
(1, 4, 1000, 12000),
(1, 6, 300, 28000),
(1, 9, 5000, 3500),
(1, 10, 400, 42000),
(1, 13, 30, 120000),
(2, 2, 500, 36000),
(2, 3, 300, 36500),
(2, 5, 2000, 12500),
(2, 7, 200, 95000),
(2, 8, 150, 145000),
(2, 11, 100, 225000),
(2, 12, 50, 550000);

-- 10.11 Tương tác thuốc
INSERT INTO drug_interactions (drug_a_id, drug_b_id, severity, description, recommendation) VALUES
(1, 3, 'moderate', 
    'Aspirin có thể làm giảm hiệu quả kháng sinh Amoxicillin trong một số trường hợp', 
    'Theo dõi đáp ứng điều trị. Tránh dùng đồng thời nếu không cần thiết'),
(3, 7, 'severe', 
    'Aspirin kết hợp Metformin tăng nguy cơ hạ đường huyết và xuất huyết tiêu hóa', 
    'Tránh dùng đồng thời. Nếu cần thiết phải theo dõi sát đường huyết và dấu hiệu xuất huyết'),
(7, 8, 'moderate', 
    'Metformin có thể tăng tác dụng của Atorvastatin, tăng nguy cơ tổn thương cơ', 
    'Theo dõi triệu chứng đau cơ, yếu cơ. Xét nghiệm CK nếu có triệu chứng');

-- 10.12 Voucher
INSERT INTO vouchers (voucher_code, description, discount_type, discount_value, min_order_amount, max_discount_amount, 
    usage_limit, valid_from, valid_to, is_active) VALUES
('WELCOME10', 'Giảm 10% cho khách hàng mới', 'percent', 10, 200000, 50000, 100, '2026-01-01', '2026-12-31', TRUE),
('HEALTH50K', 'Giảm 50.000đ cho đơn từ 500.000đ', 'fixed', 50000, 500000, NULL, 200, '2026-03-01', '2026-06-30', TRUE),
('FREESHIP', 'Miễn phí vận chuyển', 'fixed', 25000, 300000, NULL, 500, '2026-01-01', '2026-12-31', TRUE);

-- 10.13 Giỏ hàng
INSERT INTO carts (user_id) VALUES (4), (5);

INSERT INTO cart_items (cart_id, product_id, quantity) VALUES
(1, 2, 2),  -- Khách 1: 2 hộp Paracetamol
(1, 4, 1),  -- Khách 1: 1 hộp Vitamin C
(2, 1, 1),  -- Khách 2: 1 hộp Amoxicillin
(2, 6, 3);  -- Khách 2: 3 gói Oresol

-- 10.14 Đơn hàng
INSERT INTO orders (user_id, voucher_id, shipping_name, shipping_phone, shipping_address, shipping_note,
    subtotal, discount_amount, shipping_fee, total_amount, has_prescription, prescription_image,
    prescription_verified, verified_by, verified_at, status, payment_status, payment_method, order_date) VALUES

-- Đơn hàng 1: Không cần đơn thuốc
(4, 3, 'Phạm Thị Lan', '0912345678', '321 Võ Văn Tần, Q.3, TP.HCM', 'Giao giờ hành chính',
    150000, 25000, 0, 125000, FALSE, NULL, FALSE, NULL, NULL, 'completed', 'paid', 'bank_transfer', '2026-03-25 10:30:00'),

-- Đơn hàng 2: Có đơn thuốc - Đã duyệt
(5, NULL, 'Hoàng Văn Nam', '0923456789', '654 Cách Mạng Tháng 8, Q.10, TP.HCM', NULL,
    280000, 0, 25000, 305000, TRUE, 'prescription_20260328_001.jpg', TRUE, 2, '2026-03-28 14:20:00', 
    'shipping', 'unpaid', 'cod', '2026-03-28 09:15:00'),

-- Đơn hàng 3: Có đơn thuốc - Chưa duyệt
(4, 1, 'Phạm Thị Lan', '0912345678', '321 Võ Văn Tần, Q.3, TP.HCM', 'Nhớ gọi trước khi giao',
    335000, 33500, 25000, 326500, TRUE, 'prescription_20260401_002.jpg', FALSE, NULL, NULL,
    'pending', 'unpaid', 'cod', '2026-04-01 16:45:00');

-- 10.15 Chi tiết đơn hàng (áp dụng FEFO)
INSERT INTO order_details (order_id, product_id, batch_id, product_name, quantity, unit_price, discount_percent) VALUES
-- Đơn 1
(1, 2, 4, 'Paracetamol 500mg', 2, 15000, 0),
(1, 4, 7, 'Vitamin C 1000mg Abbott', 1, 120000, 0),

-- Đơn 2 (Xuất lô hết hạn sớm nhất - FEFO)
(2, 1, 1, 'Amoxicillin 500mg DHG', 2, 45000, 0),
(2, 3, 6, 'Aspirin 100mg', 3, 35000, 0),
(2, 8, 11, 'Lipitor 20mg', 1, 280000, 0),

-- Đơn 3
(3, 7, 10, 'Metformin 500mg', 2, 55000, 0),
(3, 5, 8, 'Vitamin D3 1000IU', 1, 180000, 0),
(3, 2, 4, 'Paracetamol 500mg', 1, 15000, 0);

-- 10.16 Đánh giá sản phẩm
INSERT INTO reviews (product_id, user_id, order_id, rating, comment, is_verified_purchase, is_approved) VALUES
(2, 4, 1, 5, 'Thuốc rất tốt, hạ sốt nhanh. Giá cả hợp lý.', TRUE, TRUE),
(4, 4, 1, 4, 'Vitamin C sủi bọt, dễ uống. Hơi ngọt.', TRUE, TRUE),
(1, 5, 2, 5, 'Kháng sinh hiệu quả, viêm họng hết sau 3 ngày.', TRUE, TRUE);

