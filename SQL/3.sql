-- File: add_more_products.sql (idempotent)
-- Chạy nhiều lần không nhân bản dữ liệu:
-- - Sản phẩm: kiểm tra theo product_name + manufacturer_id + strength
-- - Lô hàng: kiểm tra theo product_id + batch_number (đã có unique key)
USE pharmacy_db;

DROP TEMPORARY TABLE IF EXISTS tmp_seed_products;
CREATE TEMPORARY TABLE tmp_seed_products (
    category_id INT NOT NULL,
    manufacturer_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    generic_name VARCHAR(255),
    dosage_form VARCHAR(100),
    strength VARCHAR(100),
    unit VARCHAR(50),
    active_ingredients TEXT,
    indications TEXT,
    price DECIMAL(12,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    image_url VARCHAR(255),
    is_prescription_required TINYINT(1) DEFAULT 0,
    is_otc TINYINT(1) DEFAULT 1
);

INSERT INTO tmp_seed_products (
    category_id, manufacturer_id, product_name, generic_name, dosage_form, strength, unit,
    active_ingredients, indications, price, discount_percent, image_url, is_prescription_required, is_otc
) VALUES
(7, 1, 'Siro Ho Prospan 100ml', 'Hedera Helix', 'Siro', '100ml', 'Chai',
    'Cao khô lá thường xuân 700mg/100ml',
    'Điều trị ho có đờm, viêm phế quản cấp và mãn tính',
    125000, 10, 'prospan.jpg', 0, 1),
(2, 2, 'Salonpas Gel 30g', 'Salonpas', 'Gel bôi', '30g', 'Tuýp',
    'Methyl Salicylate, L-Menthol',
    'Giảm đau cơ, đau khớp, đau lưng',
    45000, 15, 'salonpas.jpg', 0, 1),
(8, 5, 'Blackmores Omega Daily 60 viên', 'Omega-3', 'Viên nang mềm', '1000mg', 'Viên',
    'Dầu cá 1000mg (EPA 180mg, DHA 120mg)',
    'Bổ sung Omega-3 cho tim mạch, não bộ',
    420000, 20, 'blackmores-omega.jpg', 0, 1),
(8, 5, 'Nature Made Vitamin E 400IU', 'Vitamin E', 'Viên nang mềm', '400IU', 'Viên',
    'dl-alpha-Tocopheryl Acetate 400IU',
    'Chống oxy hóa, làm đẹp da, tốt cho tim mạch',
    350000, 0, 'vitamin-e.jpg', 0, 1),
(3, 2, 'Sữa Glucerna 850g', 'Glucerna', 'Bột pha sữa', '850g', 'Hộp',
    'Protein, Chất xơ, Vitamin, Khoáng chất',
    'Dinh dưỡng cho người đái tháo đường',
    580000, 5, 'glucerna.jpg', 0, 1),
(4, 1, 'Kem chống nắng Sunplay SPF50+ 30g', 'Sunplay', 'Kem bôi', '30g', 'Tuýp',
    'Zinc Oxide, Titanium Dioxide',
    'Chống nắng phổ rộng UVA/UVB',
    180000, 12, 'sunplay.jpg', 0, 1),
(2, 1, 'Dầu gió Trúc Lâm 5ml', 'Dầu gió', 'Dầu', '5ml', 'Lọ',
    'Bạc hà, khuynh diệp, long não',
    'Xoa bóp giảm đau đầu, say xe, ngứa do muỗi đốt',
    12000, 0, 'dau-gio.jpg', 0, 1),
(7, 3, 'Hapacol 325mg (100 viên)', 'Paracetamol', 'Viên nén', '325mg', 'Viên',
    'Paracetamol 325mg',
    'Giảm đau, hạ sốt',
    28000, 0, 'hapacol.jpg', 0, 1),
(8, 5, 'Centrum Silver 100 viên', 'Multivitamin', 'Viên nén bao phim', 'N/A', 'Viên',
    'Vitamin A, C, D, E, B-complex, Khoáng chất',
    'Vitamin tổng hợp cho người trên 50 tuổi',
    680000, 15, 'centrum.jpg', 0, 1),
(2, 1, 'Xịt họng Tantum Verde 30ml', 'Benzydamine', 'Dung dịch xịt', '30ml', 'Chai',
    'Benzydamine HCl 1.5mg/ml',
    'Giảm đau, kháng viêm họng, miệng',
    95000, 0, 'tantum.jpg', 0, 1);

INSERT INTO products (
    category_id, manufacturer_id, product_name, generic_name, dosage_form, strength, unit,
    active_ingredients, indications, price, discount_percent, image_url, is_prescription_required, is_otc
)
SELECT
    t.category_id, t.manufacturer_id, t.product_name, t.generic_name, t.dosage_form, t.strength, t.unit,
    t.active_ingredients, t.indications, t.price, t.discount_percent, t.image_url, t.is_prescription_required, t.is_otc
FROM tmp_seed_products t
LEFT JOIN products p
    ON p.product_name = t.product_name
   AND p.manufacturer_id = t.manufacturer_id
   AND COALESCE(p.strength, '') = COALESCE(t.strength, '')
WHERE p.product_id IS NULL;

DROP TEMPORARY TABLE IF EXISTS tmp_seed_batches;
CREATE TEMPORARY TABLE tmp_seed_batches (
    product_name VARCHAR(255) NOT NULL,
    manufacturer_id INT NOT NULL,
    strength VARCHAR(100),
    batch_number VARCHAR(100) NOT NULL,
    manufacture_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    quantity_received INT NOT NULL,
    quantity_remaining INT NOT NULL,
    purchase_price DECIMAL(12,2) NOT NULL,
    selling_price DECIMAL(12,2) NOT NULL,
    storage_location VARCHAR(100)
);

INSERT INTO tmp_seed_batches (
    product_name, manufacturer_id, strength, batch_number, manufacture_date, expiry_date,
    quantity_received, quantity_remaining, purchase_price, selling_price, storage_location
) VALUES
('Siro Ho Prospan 100ml', 1, '100ml', 'PRO-2026-01', '2026-01-15', '2028-01-15', 200, 200, 100000, 125000, 'Kệ B1-01'),
('Salonpas Gel 30g', 2, '30g', 'SAL-2026-02', '2026-02-01', '2028-02-01', 300, 300, 35000, 45000, 'Kệ C2-01'),
('Blackmores Omega Daily 60 viên', 5, '1000mg', 'BLK-2026-01', '2026-01-20', '2027-12-20', 150, 150, 320000, 420000, 'Kệ D1-01'),
('Nature Made Vitamin E 400IU', 5, '400IU', 'VIT-2026-01', '2026-01-10', '2027-12-10', 100, 100, 280000, 350000, 'Kệ D1-02'),
('Sữa Glucerna 850g', 2, '850g', 'GLU-2026-02', '2026-02-15', '2027-08-15', 80, 80, 520000, 580000, 'Kệ E1-01');

INSERT INTO batches (
    product_id, batch_number, manufacture_date, expiry_date,
    quantity_received, quantity_remaining, purchase_price, selling_price, storage_location
)
SELECT
    p.product_id, b.batch_number, b.manufacture_date, b.expiry_date,
    b.quantity_received, b.quantity_remaining, b.purchase_price, b.selling_price, b.storage_location
FROM tmp_seed_batches b
JOIN products p
    ON p.product_name = b.product_name
   AND p.manufacturer_id = b.manufacturer_id
   AND COALESCE(p.strength, '') = COALESCE(b.strength, '')
LEFT JOIN batches existed
    ON existed.product_id = p.product_id
   AND existed.batch_number = b.batch_number
WHERE existed.batch_id IS NULL;

DROP TEMPORARY TABLE IF EXISTS tmp_seed_products;
DROP TEMPORARY TABLE IF EXISTS tmp_seed_batches;