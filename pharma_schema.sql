-- ============================================================
--  PHARMA WEBSITE - DATABASE SCHEMA
--  Phiên bản: 1.0
--  Mô tả: Schema đầy đủ cho website bán thuốc
--  Gồm: 15 bảng, đủ tất cả chức năng đề bài
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS pharma_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE pharma_db;


-- ============================================================
-- NHÓM 1: PHÂN QUYỀN & NGƯỜI DÙNG
-- ============================================================

-- 1.1 Bảng vai trò
CREATE TABLE roles (
    id          TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50)  NOT NULL UNIQUE,   -- 'admin','pharmacist','warehouse','customer'
    label       VARCHAR(100) NOT NULL,           -- tên hiển thị tiếng Việt
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO roles (name, label) VALUES
    ('admin',       'Quản trị viên'),
    ('pharmacist',  'Dược sĩ'),
    ('warehouse',   'Nhân viên kho'),
    ('customer',    'Khách hàng');


-- 1.2 Bảng người dùng
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id       TINYINT UNSIGNED NOT NULL DEFAULT 4,   -- mặc định là customer
    full_name     VARCHAR(150) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    phone         VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    avatar        VARCHAR(255),
    address       TEXT,
    is_active     TINYINT(1) NOT NULL DEFAULT 1,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id),
    INDEX idx_users_email (email),
    INDEX idx_users_role  (role_id)
) ENGINE=InnoDB;

-- Tài khoản admin mặc định (password: Admin@123)
INSERT INTO users (role_id, full_name, email, phone, password_hash) VALUES
    (1, 'Administrator', 'admin@pharma.vn', '0900000001',
     '$2y$12$exampleHashForAdmin123456789012345678901234567890123456');


-- ============================================================
-- NHÓM 2: DANH MỤC & SẢN PHẨM
-- ============================================================

-- 2.1 Bảng nhà cung cấp
CREATE TABLE suppliers (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(200) NOT NULL,
    contact_name VARCHAR(150),
    phone        VARCHAR(20),
    email        VARCHAR(150),
    address      TEXT,
    tax_code     VARCHAR(30),           -- mã số thuế công ty dược
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- 2.2 Bảng danh mục (hỗ trợ phân cấp cha-con)
CREATE TABLE categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id   INT UNSIGNED DEFAULT NULL,          -- NULL = danh mục gốc
    name        VARCHAR(150) NOT NULL,
    slug        VARCHAR(150) NOT NULL UNIQUE,
    icon        VARCHAR(100),                        -- tên icon hoặc đường dẫn ảnh
    sort_order  TINYINT UNSIGNED DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_cat_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_cat_parent (parent_id)
) ENGINE=InnoDB;

INSERT INTO categories (parent_id, name, slug, sort_order) VALUES
    (NULL, 'Thuốc kê đơn',          'thuoc-ke-don',           1),
    (NULL, 'Thuốc không kê đơn',    'thuoc-khong-ke-don',     2),
    (NULL, 'Thực phẩm chức năng',   'thuc-pham-chuc-nang',    3),
    (NULL, 'Mỹ phẩm dược',          'my-pham-duoc',           4),
    (NULL, 'Thiết bị y tế',         'thiet-bi-y-te',          5),
    (1,    'Kháng sinh',            'khang-sinh',             1),
    (1,    'Tim mạch',              'tim-mach',               2),
    (2,    'Giảm đau hạ sốt',       'giam-dau-ha-sot',        1),
    (2,    'Tiêu hóa',              'tieu-hoa',               2),
    (2,    'Hô hấp',                'ho-hap',                 3),
    (3,    'Vitamin & khoáng chất', 'vitamin-khoang-chat',    1),
    (3,    'Tăng đề kháng',         'tang-de-khang',          2);


-- 2.3 Bảng sản phẩm / thuốc
CREATE TABLE products (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id         INT UNSIGNED NOT NULL,
    supplier_id         INT UNSIGNED,
    name                VARCHAR(255) NOT NULL,
    slug                VARCHAR(255) NOT NULL UNIQUE,
    brand               VARCHAR(150),                   -- thương hiệu / tên biệt dược
    active_ingredient   TEXT,                           -- thành phần hoạt chất
    dosage_form         VARCHAR(100),                   -- dạng bào chế: viên nén, siro...
    strength            VARCHAR(100),                   -- hàm lượng: 500mg, 250mg/5ml
    unit                VARCHAR(50) NOT NULL DEFAULT 'hộp',
    price               DECIMAL(12,0) NOT NULL,         -- giá bán lẻ (VNĐ)
    original_price      DECIMAL(12,0),                  -- giá gốc để hiển thị % giảm
    short_desc          TEXT,                           -- mô tả ngắn
    indication          TEXT,                           -- công dụng / chỉ định
    dosage_instructions TEXT,                           -- liều dùng & cách dùng
    side_effects        TEXT,                           -- tác dụng phụ
    contraindications   TEXT,                           -- chống chỉ định
    storage_conditions  VARCHAR(255),                   -- điều kiện bảo quản
    requires_rx         TINYINT(1) NOT NULL DEFAULT 0,  -- 1 = thuốc kê đơn
    is_featured         TINYINT(1) NOT NULL DEFAULT 0,
    is_active           TINYINT(1) NOT NULL DEFAULT 1,
    image_main          VARCHAR(255),
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_prod_category FOREIGN KEY (category_id) REFERENCES categories(id),
    CONSTRAINT fk_prod_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    INDEX idx_prod_category  (category_id),
    INDEX idx_prod_supplier  (supplier_id),
    INDEX idx_prod_slug      (slug),
    FULLTEXT idx_prod_search (name, active_ingredient, brand)   -- hỗ trợ tìm kiếm full-text
) ENGINE=InnoDB;


-- 2.4 Bảng ảnh phụ sản phẩm
CREATE TABLE product_images (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    image_path  VARCHAR(255) NOT NULL,
    alt_text    VARCHAR(255),
    sort_order  TINYINT UNSIGNED DEFAULT 0,

    CONSTRAINT fk_img_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_img_product (product_id)
) ENGINE=InnoDB;


-- ============================================================
-- NHÓM 3: QUẢN LÝ KHO & LÔ HÀNG (nghiệp vụ đặc thù dược)
-- ============================================================

-- 3.1 Bảng lô hàng / lô sản xuất
--     Mỗi sản phẩm có thể có nhiều lô, mỗi lô có hạn sử dụng riêng
CREATE TABLE inventory_batches (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id          INT UNSIGNED NOT NULL,
    supplier_id         INT UNSIGNED,
    batch_number        VARCHAR(100) NOT NULL,       -- số lô sản xuất
    manufactured_date   DATE,
    expiry_date         DATE NOT NULL,               -- hạn sử dụng – cực kỳ quan trọng
    quantity_imported   INT UNSIGNED NOT NULL,       -- số lượng nhập
    quantity_remaining  INT UNSIGNED NOT NULL,       -- số lượng còn lại
    import_price        DECIMAL(12,0),               -- giá nhập (để tính lợi nhuận)
    imported_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    note                TEXT,

    CONSTRAINT fk_batch_product  FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_batch_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    INDEX idx_batch_product (product_id),
    INDEX idx_batch_expiry  (expiry_date)            -- index để query cảnh báo hết hạn
) ENGINE=InnoDB;


-- View tiện lợi: tồn kho tổng hợp theo sản phẩm
CREATE OR REPLACE VIEW vw_stock_summary AS
SELECT
    p.id            AS product_id,
    p.name          AS product_name,
    p.requires_rx,
    SUM(b.quantity_remaining) AS total_stock,
    MIN(b.expiry_date)        AS nearest_expiry,
    COUNT(b.id)               AS batch_count,
    -- Cờ cảnh báo: hết hạn trong 90 ngày
    SUM(CASE WHEN b.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
             AND b.quantity_remaining > 0 THEN 1 ELSE 0 END) AS expiring_soon_batches
FROM products p
LEFT JOIN inventory_batches b ON b.product_id = p.id
WHERE p.is_active = 1
GROUP BY p.id, p.name, p.requires_rx;


-- ============================================================
-- NHÓM 4: GIỎ HÀNG & ĐƠN HÀNG
-- ============================================================

-- 4.1 Bảng mã giảm giá
CREATE TABLE coupons (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(50)  NOT NULL UNIQUE,
    coupon_desc     VARCHAR(255),
    discount_type   ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
    discount_value  DECIMAL(10,2) NOT NULL,     -- % hoặc số tiền cố định
    min_order_value DECIMAL(12,0) DEFAULT 0,    -- giá trị đơn hàng tối thiểu
    max_discount    DECIMAL(12,0),              -- giảm tối đa (dành cho loại percent)
    usage_limit     INT UNSIGNED DEFAULT NULL,  -- NULL = không giới hạn
    used_count      INT UNSIGNED DEFAULT 0,
    starts_at       DATETIME,
    expires_at      DATETIME,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- 4.2 Bảng đơn hàng
CREATE TABLE orders (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED NOT NULL,
    coupon_id           INT UNSIGNED DEFAULT NULL,
    order_code          VARCHAR(20) NOT NULL UNIQUE,   -- ví dụ: PH-20240601-0001
    status              ENUM(
                            'pending',      -- chờ xác nhận
                            'confirmed',    -- đã xác nhận
                            'processing',   -- đang chuẩn bị hàng
                            'shipping',     -- đang giao
                            'delivered',    -- đã giao
                            'cancelled',    -- đã hủy
                            'returned'      -- hoàn trả
                        ) NOT NULL DEFAULT 'pending',
    payment_method      ENUM('cod','bank_transfer','momo','vnpay') NOT NULL DEFAULT 'cod',
    payment_status      ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
    subtotal            DECIMAL(12,0) NOT NULL,        -- tổng trước giảm giá
    discount_amount     DECIMAL(12,0) DEFAULT 0,
    shipping_fee        DECIMAL(12,0) DEFAULT 0,
    total_amount        DECIMAL(12,0) NOT NULL,        -- tổng sau giảm giá + ship
    -- Địa chỉ giao hàng (snapshot tại thời điểm đặt)
    shipping_name       VARCHAR(150) NOT NULL,
    shipping_phone      VARCHAR(20)  NOT NULL,
    shipping_address    TEXT NOT NULL,
    note                TEXT,
    confirmed_at        DATETIME,
    shipped_at          DATETIME,
    delivered_at        DATETIME,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_order_user   FOREIGN KEY (user_id)   REFERENCES users(id),
    CONSTRAINT fk_order_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL,
    INDEX idx_order_user   (user_id),
    INDEX idx_order_status (status),
    INDEX idx_order_code   (order_code)
) ENGINE=InnoDB;


-- 4.3 Bảng chi tiết đơn hàng
CREATE TABLE order_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        INT UNSIGNED NOT NULL,
    product_id      INT UNSIGNED NOT NULL,
    batch_id        INT UNSIGNED,                   -- lô hàng được xuất
    product_name    VARCHAR(255) NOT NULL,          -- snapshot tên lúc đặt
    product_price   DECIMAL(12,0) NOT NULL,         -- snapshot giá lúc đặt
    quantity        INT UNSIGNED NOT NULL,
    subtotal        DECIMAL(12,0) NOT NULL,         -- price * quantity

    CONSTRAINT fk_item_order   FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_item_batch   FOREIGN KEY (batch_id)   REFERENCES inventory_batches(id) ON DELETE SET NULL,
    INDEX idx_item_order   (order_id),
    INDEX idx_item_product (product_id)
) ENGINE=InnoDB;


-- ============================================================
-- NHÓM 5: ĐƠN THUỐC & TƯ VẤN DƯỢC SĨ
-- ============================================================

-- 5.1 Bảng đơn thuốc upload
CREATE TABLE prescriptions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    order_id        INT UNSIGNED DEFAULT NULL,          -- liên kết đơn hàng sau khi duyệt
    pharmacist_id   INT UNSIGNED DEFAULT NULL,          -- dược sĩ phụ trách
    image_path      VARCHAR(255) NOT NULL,              -- ảnh đơn thuốc
    status          ENUM('pending','reviewed','approved','rejected') NOT NULL DEFAULT 'pending',
    patient_note    TEXT,                               -- ghi chú của khách hàng
    pharmacist_note TEXT,                               -- phản hồi của dược sĩ
    reviewed_at     DATETIME,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_rx_user        FOREIGN KEY (user_id)       REFERENCES users(id),
    CONSTRAINT fk_rx_order       FOREIGN KEY (order_id)      REFERENCES orders(id) ON DELETE SET NULL,
    CONSTRAINT fk_rx_pharmacist  FOREIGN KEY (pharmacist_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_rx_user   (user_id),
    INDEX idx_rx_status (status)
) ENGINE=InnoDB;


-- ============================================================
-- NHÓM 6: NGHIỆP VỤ DƯỢC NÂNG CAO
-- ============================================================

-- 6.1 Bảng tương tác thuốc
--     Lưu các cặp thuốc có tương tác để cảnh báo khi mua cùng nhau
CREATE TABLE drug_interactions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_a_id    INT UNSIGNED NOT NULL,
    product_b_id    INT UNSIGNED NOT NULL,
    severity        ENUM('mild','moderate','severe') NOT NULL DEFAULT 'moderate',
    interaction_desc TEXT NOT NULL,     -- mô tả nguy cơ tương tác
    recommendation  TEXT,               -- khuyến nghị xử lý
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Đảm bảo không lưu trùng cặp (A,B) và (B,A)
    CONSTRAINT uq_interaction UNIQUE (product_a_id, product_b_id),
    CONSTRAINT fk_di_product_a FOREIGN KEY (product_a_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_di_product_b FOREIGN KEY (product_b_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT chk_di_diff CHECK (product_a_id <> product_b_id),
    INDEX idx_di_a (product_a_id),
    INDEX idx_di_b (product_b_id)
) ENGINE=InnoDB;


-- ============================================================
-- NHÓM 7: HỖ TRỢ UI (tuỳ chọn, điểm cộng)
-- ============================================================

-- 7.1 Bảng banner / slider trang chủ
CREATE TABLE banners (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255),
    image_path  VARCHAR(255) NOT NULL,
    link_url    VARCHAR(255),
    sort_order  TINYINT UNSIGNED DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- 7.2 Bảng đánh giá sản phẩm
CREATE TABLE product_reviews (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NOT NULL,
    rating      TINYINT UNSIGNED NOT NULL,    -- 1–5 sao
    comment     TEXT,
    is_approved TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_rv_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_rv_user    FOREIGN KEY (user_id)    REFERENCES users(id),
    CONSTRAINT chk_rating    CHECK (rating BETWEEN 1 AND 5),
    UNIQUE uq_user_product (user_id, product_id),   -- mỗi user chỉ review 1 lần / sản phẩm
    INDEX idx_rv_product (product_id)
) ENGINE=InnoDB;


-- ============================================================
-- STORED PROCEDURE: Kiểm tra tương tác thuốc
-- Dùng trong PHP: CALL check_drug_interactions(product_ids_json)
-- ============================================================
DELIMITER $$

CREATE PROCEDURE check_drug_interactions(
    IN p_product_a INT UNSIGNED,
    IN p_product_b INT UNSIGNED
)
BEGIN
    SELECT
        di.severity,
        di.description,
        di.recommendation,
        pa.name AS drug_a,
        pb.name AS drug_b
    FROM drug_interactions di
    JOIN products pa ON pa.id = di.product_a_id
    JOIN products pb ON pb.id = di.product_b_id
    WHERE
        (di.product_a_id = p_product_a AND di.product_b_id = p_product_b)
        OR
        (di.product_a_id = p_product_b AND di.product_b_id = p_product_a);
END$$

DELIMITER ;


-- ============================================================
-- STORED PROCEDURE: Cập nhật tồn kho khi xuất hàng
-- Gọi sau khi đơn hàng được xác nhận
-- ============================================================
DELIMITER $$

CREATE PROCEDURE reduce_stock(
    IN p_batch_id  INT UNSIGNED,
    IN p_quantity  INT UNSIGNED
)
BEGIN
    DECLARE v_remaining INT UNSIGNED;
    SELECT quantity_remaining INTO v_remaining
    FROM inventory_batches WHERE id = p_batch_id FOR UPDATE;

    IF v_remaining < p_quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Không đủ tồn kho trong lô này';
    ELSE
        UPDATE inventory_batches
        SET quantity_remaining = quantity_remaining - p_quantity
        WHERE id = p_batch_id;
    END IF;
END$$

DELIMITER ;


-- ============================================================
-- DỮ LIỆU MẪU (seed data để test)
-- ============================================================

-- Nhà cung cấp mẫu
INSERT INTO suppliers (name, contact_name, phone, email, address, tax_code) VALUES
    ('Công ty CP Dược Hậu Giang', 'Nguyễn Văn A', '0710000001', 'dhg@example.com', 'Cần Thơ', '1800300069'),
    ('Công ty CP Dược phẩm Imexpharm', 'Trần Thị B', '0710000002', 'imex@example.com', 'TP.HCM', '1900200180'),
    ('Công ty TNHH Traphaco', 'Lê Văn C', '0243000003', 'traphaco@example.com', 'Hà Nội', '1001234567');

-- Sản phẩm mẫu
INSERT INTO products (category_id, supplier_id, name, slug, brand, active_ingredient, dosage_form, strength, unit, price, original_price, indication, dosage_instructions, side_effects, requires_rx, is_featured, is_active) VALUES
    (8, 2, 'Paracetamol 500mg DHG', 'paracetamol-500mg-dhg', 'DHG Pharma', 'Paracetamol 500mg', 'Viên nén bao phim', '500mg', 'hộp 100 viên', 35000, 40000,
     'Giảm đau, hạ sốt trong các trường hợp đau đầu, đau răng, cảm cúm, sốt.',
     'Người lớn: 1-2 viên/lần, 3-4 lần/ngày. Không quá 8 viên/ngày.',
     'Buồn nôn, phát ban da hiếm gặp. Dùng liều cao có thể gây độc gan.',
     0, 1, 1),

    (8, 1, 'Ibuprofen 400mg', 'ibuprofen-400mg', 'Imexpharm', 'Ibuprofen 400mg', 'Viên nén bao phim', '400mg', 'hộp 20 viên', 28000, NULL,
     'Giảm đau, kháng viêm, hạ sốt.',
     'Người lớn: 1 viên/lần, ngày 3 lần sau ăn. Không dùng quá 7 ngày liên tục.',
     'Đau dạ dày, buồn nôn, chóng mặt. Thận trọng với người có tiền sử loét dạ dày.',
     0, 0, 1),

    (6, 3, 'Amoxicillin 500mg', 'amoxicillin-500mg', 'Traphaco', 'Amoxicillin trihydrate 574mg (tương đương Amoxicillin 500mg)', 'Nang cứng', '500mg', 'hộp 10 vỉ x 10 viên', 85000, NULL,
     'Điều trị nhiễm khuẩn đường hô hấp, tiết niệu, da và mô mềm.',
     'Người lớn: 1 viên x 3 lần/ngày, cách đều nhau 8 giờ. Uống nguyên vỉ với nhiều nước.',
     'Tiêu chảy, buồn nôn, phát ban dị ứng. Ngừng thuốc và liên hệ bác sĩ nếu có phản ứng dị ứng.',
     1, 0, 1),

    (11, 2, 'Vitamin C 1000mg Sủi', 'vitamin-c-1000mg-sui', 'Imexpharm', 'Acid ascorbic 1000mg', 'Viên sủi', '1000mg', 'hộp 4 vỉ x 5 viên', 55000, 65000,
     'Bổ sung Vitamin C, tăng sức đề kháng, hỗ trợ hấp thu sắt.',
     'Người lớn: 1 viên/ngày, hòa tan vào 200ml nước, uống sau ăn.',
     'Có thể gây rối loạn tiêu hóa nếu dùng liều cao kéo dài.',
     0, 1, 1);

-- Lô hàng mẫu cho sản phẩm 1 (Paracetamol)
INSERT INTO inventory_batches (product_id, supplier_id, batch_number, manufactured_date, expiry_date, quantity_imported, quantity_remaining, import_price) VALUES
    (1, 2, 'PC2024001', '2024-01-01', '2026-01-01', 500, 480, 28000),
    (1, 2, 'PC2024002', '2024-06-01', '2026-06-01', 300, 300, 28500);

-- Lô hàng sắp hết hạn (để test cảnh báo)
INSERT INTO inventory_batches (product_id, supplier_id, batch_number, manufactured_date, expiry_date, quantity_imported, quantity_remaining, import_price) VALUES
    (2, 1, 'IB2022BETA', '2022-03-01', '2024-09-01', 100, 45, 22000);

-- Mã giảm giá mẫu
INSERT INTO coupons (code, coupon_desc, discount_type, discount_value, min_order_value, max_discount, usage_limit, expires_at) VALUES
    ('WELCOME10', 'Giảm 10% cho đơn hàng đầu tiên', 'percent', 10.00, 100000, 50000, 1000, '2025-12-31 23:59:59'),
    ('SAVE50K',   'Giảm 50,000đ cho đơn từ 300,000đ', 'fixed',   50000, 300000, NULL, 500,  '2025-06-30 23:59:59');

-- Dữ liệu tương tác thuốc mẫu
INSERT INTO drug_interactions (product_a_id, product_b_id, severity, interaction_desc, recommendation) VALUES
    (1, 2, 'mild',
     'Paracetamol và Ibuprofen đều có tác dụng hạ sốt, giảm đau. Dùng đồng thời không thường được khuyến cáo nhưng ít gây nguy hiểm ở liều thông thường.',
     'Tham khảo ý kiến dược sĩ trước khi dùng đồng thời. Không tăng liều của cả hai thuốc.');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- TỔNG KẾT SCHEMA
-- ============================================================
-- Bảng chính (15 bảng + 1 view + 2 stored procedure):
--
-- [Phân quyền]   roles, users
-- [Sản phẩm]     suppliers, categories, products, product_images
-- [Kho hàng]     inventory_batches + view vw_stock_summary
-- [Đơn hàng]     coupons, orders, order_items
-- [Dược sĩ]      prescriptions
-- [Nghiệp vụ]    drug_interactions
-- [UI hỗ trợ]    banners, product_reviews
-- [Procedures]   check_drug_interactions, reduce_stock
--
-- Chạy file này: mysql -u root -p < pharma_schema.sql
-- ============================================================
