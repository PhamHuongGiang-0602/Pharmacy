<?php
require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel {
    
    /**
     * Lấy sản phẩm bán chạy nhất (dựa vào view v_bestsellers)
     * @param int $limit Số lượng sản phẩm cần lấy
     * @return array Danh sách sản phẩm
     */
    public function getBestSellers($limit = 8) {
        $sql = "SELECT 
                    p.product_id,
                    p.product_name,
                    p.image_url,
                    p.price,
                    p.discount_percent,
                    m.manufacturer_name as brand,
                    COALESCE(bs.total_sold, 0) as total_sold,
                    COALESCE(bs.avg_rating, 4.5) as rating,
                    COALESCE(bs.review_count, 0) as reviews,
                    CASE 
                        WHEN p.discount_percent > 15 THEN CONCAT('Sale ', ROUND(p.discount_percent), '%')
                        WHEN COALESCE(bs.total_sold, 0) > 100 THEN 'Bán chạy'
                        ELSE ''
                    END as badge,
                    CASE 
                        WHEN p.discount_percent > 0 THEN ROUND(p.price * (100 - p.discount_percent) / 100)
                        ELSE p.price
                    END as current_price,
                    p.price as old_price
                FROM products p
                LEFT JOIN v_bestsellers bs ON p.product_id = bs.product_id
                LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id
                WHERE p.is_active = TRUE
                ORDER BY COALESCE(bs.total_sold, 0) DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy sản phẩm đang khuyến mãi
     */
    public function getSaleProducts($limit = 8) {
        $sql = "SELECT 
                    p.product_id,
                    p.product_name,
                    p.image_url,
                    p.price,
                    p.discount_percent,
                    m.manufacturer_name as brand,
                    ROUND(p.price * (100 - p.discount_percent) / 100) as current_price,
                    p.price as old_price,
                    CONCAT('Sale ', ROUND(p.discount_percent), '%') as badge
                FROM products p
                LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id
                WHERE p.is_active = TRUE AND p.discount_percent > 0
                ORDER BY p.discount_percent DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy sản phẩm mới nhất
     */
    public function getNewProducts($limit = 8) {
        $sql = "SELECT 
                    p.product_id,
                    p.product_name,
                    p.image_url,
                    p.price,
                    p.discount_percent,
                    m.manufacturer_name as brand,
                    CASE 
                        WHEN p.discount_percent > 0 THEN ROUND(p.price * (100 - p.discount_percent) / 100)
                        ELSE p.price
                    END as current_price,
                    p.price as old_price,
                    'Mới' as badge,
                    4.5 as rating,
                    0 as reviews
                FROM products p
                LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id
                WHERE p.is_active = TRUE
                ORDER BY p.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy danh mục sản phẩm
     */
    public function getCategories() {
        $sql = "SELECT 
                    category_id,
                    category_name,
                    description,
                    image_url
                FROM categories
                WHERE parent_category_id IS NULL
                ORDER BY category_id";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Đếm tổng số sản phẩm theo danh mục
     */
    public function countProductsByCategory($categoryId) {
        $sql = "SELECT COUNT(*) FROM products WHERE category_id = :cat_id AND is_active = TRUE";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}