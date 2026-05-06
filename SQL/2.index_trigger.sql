-- =====================================================
-- DATABASE QUẢN LÝ NHÀ THUỐC - PHẦN 2: LOGIC NÂNG CAO
-- =====================================================

USE pharmacy_db;

-- =====================================================
-- 11. TẠO INDEX BỔ SUNG (Tối ưu hiệu suất truy vấn)
-- =====================================================

-- Tăng tốc tìm kiếm sản phẩm đang kinh doanh và theo danh mục
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_products_category_active ON products(category_id, is_active);

-- Tối ưu cho quy trình FEFO và cảnh báo hạn dùng
CREATE INDEX idx_batches_product_expiry ON batches(product_id, expiry_date);
CREATE INDEX idx_batches_status_expiry ON batches(status, expiry_date);

-- Tối ưu cho quản lý đơn hàng và duyệt đơn thuốc
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_orders_prescription ON orders(has_prescription, prescription_verified, status);

-- Tối ưu cho việc tính toán thống kê chi tiết
CREATE INDEX idx_order_details_product ON order_details(product_id);

-- =====================================================
-- 12. TẠO TRIGGERS (Tự động hóa nghiệp vụ kho)
-- =====================================================
DELIMITER $$

-- A. TRỪ KHO: Khi có chi tiết đơn hàng mới được tạo
DROP TRIGGER IF EXISTS after_order_detail_insert$$
CREATE TRIGGER after_order_detail_insert
AFTER INSERT ON order_details
FOR EACH ROW
BEGIN
    IF NEW.batch_id IS NOT NULL THEN
        UPDATE batches 
        SET quantity_remaining = quantity_remaining - NEW.quantity
        WHERE batch_id = NEW.batch_id;
    END IF;
END$$

-- B. HOÀN KHO: Khi đơn hàng chuyển sang trạng thái 'cancelled'
DROP TRIGGER IF EXISTS after_order_status_cancel$$
CREATE TRIGGER after_order_status_cancel
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    -- Nếu trạng thái đơn hàng chuyển sang 'cancelled'
    IF OLD.status != 'cancelled' AND NEW.status = 'cancelled' THEN
        -- Cộng lại số lượng vào từng lô hàng tương ứng trong đơn hàng đó
        UPDATE batches b
        JOIN order_details od ON b.batch_id = od.batch_id
        SET b.quantity_remaining = b.quantity_remaining + od.quantity
        WHERE od.order_id = NEW.order_id;
    END IF;
END$$

-- C. CẬP NHẬT TRẠNG THÁI LÔ: Khi nhập lô hàng mới
DROP TRIGGER IF EXISTS before_batch_insert$$
CREATE TRIGGER before_batch_insert
BEFORE INSERT ON batches
FOR EACH ROW
BEGIN
    SET NEW.is_expired = (NEW.expiry_date < CURDATE());
    SET NEW.days_to_expiry = DATEDIFF(NEW.expiry_date, CURDATE());
    
    IF NEW.expiry_date < CURDATE() THEN
        SET NEW.status = 'expired';
    ELSEIF DATEDIFF(NEW.expiry_date, CURDATE()) <= NEW.expiry_alert_days THEN
        SET NEW.status = 'near_expiry';
    ELSE
        SET NEW.status = 'active';
    END IF;
END$$

-- D. NHẬT KÝ GIÁ: Lưu lịch sử khi giá sản phẩm thay đổi
DROP TRIGGER IF EXISTS after_product_price_update$$
CREATE TRIGGER after_product_price_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF OLD.price != NEW.price THEN
        INSERT INTO price_history (product_id, old_price, new_price)
        VALUES (NEW.product_id, OLD.price, NEW.price);
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- 13. TỰ ĐỘNG HÓA LỊCH TRÌNH (EVENTS)
-- =====================================================

-- Đảm bảo trình lập lịch của MySQL luôn bật
SET GLOBAL event_scheduler = ON;

DROP EVENT IF EXISTS update_batch_status_daily;
DELIMITER $$
CREATE EVENT update_batch_status_daily
ON SCHEDULE EVERY 1 DAY
STARTS (CURRENT_DATE + INTERVAL 1 DAY)
DO
BEGIN
    -- Cập nhật trạng thái 'Hết hạn'
    UPDATE batches 
    SET status = 'expired', 
        is_expired = TRUE, 
        days_to_expiry = DATEDIFF(expiry_date, CURDATE())
    WHERE expiry_date < CURDATE() AND status != 'expired';
    
    -- Cập nhật trạng thái 'Sắp hết hạn'
    UPDATE batches 
    SET status = 'near_expiry', 
        days_to_expiry = DATEDIFF(expiry_date, CURDATE())
    WHERE expiry_date >= CURDATE() 
        AND DATEDIFF(expiry_date, CURDATE()) <= expiry_alert_days
        AND status = 'active';
END$$
DELIMITER ;

-- =====================================================
-- 14. KIỂM TRA TRẠNG THÁI HỆ THỐNG
-- =====================================================

SELECT 'Hệ thống Index, Triggers và Events đã được thiết lập thành công!' as status;

-- Hiển thị các Trigger đã cài đặt thành công
SHOW TRIGGERS;

-- Kiểm tra trạng thái hoạt động của Event Scheduler
SHOW VARIABLES LIKE 'event_scheduler';

-- Bật lại kiểm tra khóa ngoại để bảo vệ dữ liệu
SET FOREIGN_KEY_CHECKS = 1;