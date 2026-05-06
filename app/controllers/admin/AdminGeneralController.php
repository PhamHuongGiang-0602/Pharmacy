<?php

require_once __DIR__ . '/../BaseController.php';

class AdminGeneralController extends BaseController {
    
    public function __construct() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
            $this->redirect(BASE_URL . 'auth/login');
        }
    }
    
    /**
     * Quản lý nhà cung cấp
     */
    public function suppliers() {
        $db = (new BaseModel())->db;
        $suppliers = $db->query("SELECT * FROM suppliers ORDER BY supplier_id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('admin/general/suppliers', ['suppliers' => $suppliers, 'pageTitle' => 'Nhà cung cấp']);
    }
    
    /**
     * Quản lý tương tác thuốc
     */
    public function interactions() {
        $db = (new BaseModel())->db;
        $sql = "SELECT di.*, p1.product_name as drug_a, p2.product_name as drug_b 
                FROM drug_interactions di
                JOIN products p1 ON di.drug_a_id = p1.product_id
                JOIN products p2 ON di.drug_b_id = p2.product_id";
        $interactions = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('admin/general/interactions', ['interactions' => $interactions, 'pageTitle' => 'Tương tác thuốc']);
    }

    /**
     * Quản lý nhà sản xuất
     */
    public function manufacturers() {
        $db = (new BaseModel())->db;
        $manufacturers = $db->query("SELECT * FROM manufacturers ORDER BY manufacturer_id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('admin/general/manufacturers', ['manufacturers' => $manufacturers, 'pageTitle' => 'Nhà sản xuất']);
    }
    
    /**
     * Quản lý danh mục
     */
    public function categories() {
        $db = (new BaseModel())->db;
        $categories = $db->query("SELECT * FROM categories ORDER BY category_id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('admin/general/categories', ['categories' => $categories, 'pageTitle' => 'Danh mục sản phẩm']);
    }
}
