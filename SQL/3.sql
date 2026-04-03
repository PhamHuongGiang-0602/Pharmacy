-- File: add_more_products.sql
USE pharmacy_db;

-- Thêm các thuốc phổ biến tại Việt Nam
INSERT INTO products (category_id, manufacturer_id, product_name, generic_name, dosage_form, strength, unit, 
    active_ingredients, indications, price, discount_percent, image_url, is_prescription_required, is_otc) VALUES

(7, 1, 'Siro Ho Prospan 100ml', 'Hedera Helix', 'Siro', '100ml', 'Chai',
    'Cao khô lá thường xuân 700mg/100ml',
    'Điều trị ho có đờm, viêm phế quản cấp và mãn tính',
    125000, 10, 'prospan.jpg', FALSE, TRUE),

(2, 2, 'Salonpas Gel 30g', 'Salonpas', 'Gel bôi', '30g', 'Tuýp',
    'Methyl Salicylate, L-Menthol',
    'Giảm đau cơ, đau khớp, đau lưng',
    45000, 15, 'salonpas.jpg', FALSE, TRUE),

(8, 5, 'Blackmores Omega Daily 60 viên', 'Omega-3', 'Viên nang mềm', '1000mg', 'Viên',
    'Dầu cá 1000mg (EPA 180mg, DHA 120mg)',
    'Bổ sung Omega-3 cho tim mạch, não bộ',
    420000, 20, 'blackmores-omega.jpg', FALSE, TRUE),

(8, 5, 'Nature Made Vitamin E 400IU', 'Vitamin E', 'Viên nang mềm', '400IU', 'Viên',
    'dl-alpha-Tocopheryl Acetate 400IU',
    'Chống oxy hóa, làm đẹp da, tốt cho tim mạch',
    350000, 0, 'vitamin-e.jpg', FALSE, TRUE),

(3, 2, 'Sữa Glucerna 850g', 'Glucerna', 'Bột pha sữa', '850g', 'Hộp',
    'Protein, Chất xơ, Vitamin, Khoáng chất',
    'Dinh dưỡng cho người đái tháo đường',
    580000, 5, 'glucerna.jpg', FALSE, TRUE),

(4, 1, 'Kem chống nắng Sunplay SPF50+ 30g', 'Sunplay', 'Kem bôi', '30g', 'Tuýp',
    'Zinc Oxide, Titanium Dioxide',
    'Chống nắng phổ rộng UVA/UVB',
    180000, 12, 'sunplay.jpg', FALSE, TRUE),

(2, 1, 'Dầu gió Trúc Lâm 5ml', 'Dầu gió', 'Dầu', '5ml', 'Lọ',
    'Bạc hà, khuynh diệp, long não',
    'Xoa bóp giảm đau đầu, say xe, ngứa do muỗi đốt',
    12000, 0, 'dau-gio.jpg', FALSE, TRUE),

(7, 3, 'Hapacol 325mg (100 viên)', 'Paracetamol', 'Viên nén', '325mg', 'Viên',
    'Paracetamol 325mg',
    'Giảm đau, hạ sốt',
    28000, 0, 'hapacol.jpg', FALSE, TRUE),

(8, 5, 'Centrum Silver 100 viên', 'Multivitamin', 'Viên nén bao phim', 'N/A', 'Viên',
    'Vitamin A, C, D, E, B-complex, Khoáng chất',
    'Vitamin tổng hợp cho người trên 50 tuổi',
    680000, 15, 'centrum.jpg', FALSE, TRUE),

(2, 1, 'Xịt họng Tantum Verde 30ml', 'Benzydamine', 'Dung dịch xịt', '30ml', 'Chai',
    'Benzydamine HCl 1.5mg/ml',
    'Giảm đau, kháng viêm họng, miệng',
    95000, 0, 'tantum.jpg', FALSE, TRUE);

-- Cập nhật lô hàng cho các sản phẩm mới
INSERT INTO batches (product_id, batch_number, manufacture_date, expiry_date, 
    quantity_received, quantity_remaining, purchase_price, selling_price, storage_location) VALUES
(11, 'PRO-2026-01', '2026-01-15', '2028-01-15', 200, 200, 100000, 125000, 'Kệ B1-01'),
(12, 'SAL-2026-02', '2026-02-01', '2028-02-01', 300, 300, 35000, 45000, 'Kệ C2-01'),
(13, 'BLK-2026-01', '2026-01-20', '2027-12-20', 150, 150, 320000, 420000, 'Kệ D1-01'),
(14, 'VIT-2026-01', '2026-01-10', '2027-12-10', 100, 100, 280000, 350000, 'Kệ D1-02'),
(15, 'GLU-2026-02', '2026-02-15', '2027-08-15', 80, 80, 520000, 580000, 'Kệ E1-01');