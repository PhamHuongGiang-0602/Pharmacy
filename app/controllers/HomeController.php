<?php
require_once __DIR__ . '/../models/ProductModel.php';

class HomeController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new ProductModel();
    }
    
    /**
     * Hiển thị trang chủ
     */
    public function index() {
        // Lấy dữ liệu từ Model
        $bestSellers = $this->productModel->getBestSellers(8);
        $saleProducts = $this->productModel->getSaleProducts(8);
        $newProducts = $this->productModel->getNewProducts(8);
        $categories = $this->productModel->getCategories();
        
        // Truyền dữ liệu vào View
        $data = [
            'bestSellers' => $bestSellers,
            'saleProducts' => $saleProducts,
            'newProducts' => $newProducts,
            'categories' => $categories,
            'pageTitle' => 'Trang chủ - Nhà thuốc trực tuyến'
        ];
        
        // Load view
        $this->loadView('home', $data);
    }
    
    /**
     * Helper: Load view với dữ liệu
     */
    private function loadView($viewName, $data = []) {
        // Extract data để dùng như biến trong view
        extract($data);
        
        // Load view file
        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View không tồn tại: " . $viewPath);
        }
    }
}