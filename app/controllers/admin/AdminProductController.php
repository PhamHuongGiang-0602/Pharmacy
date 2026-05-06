<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../Models/ProductModel.php';

class AdminProductController extends BaseController {
    private $productModel;
    
    public function __construct() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) { // 1 is Admin
            $this->redirect(BASE_URL . 'auth/login');
        }
        $this->productModel = new ProductModel();
    }
    
    /**
     * Danh sách sản phẩm
     */
    public function index() {
        $db = (new BaseModel())->db;
        
        $q = $_GET['q'] ?? '';
        $category_id = $_GET['category_id'] ?? '';
        $is_rx = $_GET['is_rx'] ?? '';
        
        $where = ["1=1"];
        $params = [];
        
        if ($q !== '') {
            $where[] = "(p.product_name LIKE :q OR p.product_id = :qid)";
            $params['q'] = "%$q%";
            $params['qid'] = $q;
        }
        
        if ($category_id !== '') {
            $where[] = "(p.category_id = :cat_id OR p.category_id IN (SELECT category_id FROM categories WHERE parent_category_id = :cat_id))";
            $params['cat_id'] = $category_id;
        }

        if ($is_rx !== '') {
            $where[] = "p.is_prescription_required = :is_rx";
            $params['is_rx'] = (int)$is_rx;
        }
        
        $whereSql = implode(' AND ', $where);
        
        $sql = "SELECT p.*, c.category_name, m.manufacturer_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                LEFT JOIN manufacturers m ON p.manufacturer_id = m.manufacturer_id 
                WHERE $whereSql
                ORDER BY p.product_id DESC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        
        $this->loadView('admin/product/index', [
            'products' => $products,
            'categories' => $categories,
            'pageTitle' => 'Quản lý sản phẩm',
            'q' => $q,
            'category_id' => $category_id,
            'is_rx' => $is_rx
        ]);
    }
    
    /**
     * Thêm sản phẩm mới
     */
    public function create() {
        $db = (new BaseModel())->db;
        $categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        $manufacturers = $db->query("SELECT * FROM manufacturers")->fetchAll(PDO::FETCH_ASSOC);
        
        $this->loadView('admin/product/create', [
            'categories' => $categories,
            'manufacturers' => $manufacturers,
            'pageTitle' => 'Thêm sản phẩm mới'
        ]);
    }
    
    /**
     * Lưu sản phẩm
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new BaseModel())->db;
            
            // Xử lý upload ảnh
            $imageUrl = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../public/img/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                    $imageUrl = 'prod_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageUrl);
                }
            }

            $sql = "INSERT INTO products (product_name, category_id, manufacturer_id, price, discount_percent, 
                                        generic_name, dosage_form, unit, active_ingredients, indications, is_prescription_required, image_url) 
                    VALUES (:name, :cat_id, :m_id, :price, :discount, :generic, :dosage, :unit, :ingredients, :indications, :is_rx, :img)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'name' => $_POST['product_name'],
                'cat_id' => $_POST['category_id'],
                'm_id' => $_POST['manufacturer_id'],
                'price' => $_POST['price'],
                'discount' => $_POST['discount_percent'] ?? 0,
                'generic' => $_POST['generic_name'] ?? '',
                'dosage' => $_POST['dosage_form'] ?? '',
                'unit' => $_POST['unit'] ?? 'Viên',
                'ingredients' => $_POST['active_ingredients'] ?? '',
                'indications' => $_POST['indications'] ?? '',
                'is_rx' => isset($_POST['is_prescription_required']) ? 1 : 0,
                'img' => $imageUrl
            ]);
            
            $_SESSION['success_message'] = "Thêm sản phẩm thành công.";
            $this->redirect(BASE_URL . 'admin/product');
        }
    }

    /**
     * Sửa sản phẩm
     */
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $db = (new BaseModel())->db;
        
        $product = $db->query("SELECT * FROM products WHERE product_id = " . intval($id))->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            $this->redirect(BASE_URL . 'admin/product');
        }
        
        $categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        $manufacturers = $db->query("SELECT * FROM manufacturers")->fetchAll(PDO::FETCH_ASSOC);
        
        $this->loadView('admin/product/edit', [
            'product' => $product,
            'categories' => $categories,
            'manufacturers' => $manufacturers,
            'pageTitle' => 'Sửa sản phẩm'
        ]);
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['product_id'] ?? 0;
            $db = (new BaseModel())->db;
            
            // Xử lý upload ảnh
            $imageUrl = $_POST['old_image'] ?? null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../public/img/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                    $imageUrl = 'prod_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageUrl);
                }
            }

            $sql = "UPDATE products SET 
                    product_name = :name, category_id = :cat_id, manufacturer_id = :m_id, 
                    price = :price, discount_percent = :discount, generic_name = :generic, 
                    dosage_form = :dosage, unit = :unit, active_ingredients = :ingredients, 
                    indications = :indications, is_prescription_required = :is_rx, image_url = :img 
                    WHERE product_id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'name' => $_POST['product_name'],
                'cat_id' => $_POST['category_id'],
                'm_id' => $_POST['manufacturer_id'],
                'price' => $_POST['price'],
                'discount' => $_POST['discount_percent'] ?? 0,
                'generic' => $_POST['generic_name'] ?? '',
                'dosage' => $_POST['dosage_form'] ?? '',
                'unit' => $_POST['unit'] ?? 'Viên',
                'ingredients' => $_POST['active_ingredients'] ?? '',
                'indications' => $_POST['indications'] ?? '',
                'is_rx' => isset($_POST['is_prescription_required']) ? 1 : 0,
                'img' => $imageUrl,
                'id' => $id
            ]);
            
            $_SESSION['success_message'] = "Cập nhật sản phẩm thành công.";
            $this->redirect(BASE_URL . 'admin/product');
        }
    }

    /**
     * Xóa sản phẩm (Soft delete)
     */
    public function delete() {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $db = (new BaseModel())->db;
            
            // Check pending orders
            $stmt = $db->prepare("SELECT COUNT(*) FROM order_details od JOIN orders o ON od.order_id = o.order_id WHERE od.product_id = ? AND o.status IN ('pending', 'processing', 'shipping')");
            $stmt->execute([$id]);
            $pendingCount = $stmt->fetchColumn();
            
            if ($pendingCount > 0) {
                $_SESSION['error_message'] = "Không thể xóa sản phẩm đang có trong đơn hàng chưa hoàn tất.";
            } else {
                $db->prepare("UPDATE products SET is_active = 0 WHERE product_id = ?")->execute([$id]);
                $_SESSION['success_message'] = "Đã ẩn sản phẩm.";
            }
        }
        $this->redirect(BASE_URL . 'admin/product');
    }

}
