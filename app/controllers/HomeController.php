<?php
// Sử dụng đường dẫn tuyệt đối dựa trên vị trí file hiện tại
require_once __DIR__ . '/BaseController.php';

// KIỂM TRA KỸ: Nếu thư mục của bạn trên GitHub là 'models' (viết thường) 
// thì bạn PHẢI sửa chữ 'Models' dưới đây thành 'models'.
require_once __DIR__ . '/../models/ProductModel.php'; 

class HomeController extends BaseController {
    // ... giữ nguyên phần code bên dưới
    private $productModel;
    
    public function __construct() {
        $this->productModel = new ProductModel();
    }
    
    /**
     * Hiển thị trang chủ
     */
    public function index() {
        // Lấy dữ liệu từ Model
        $bestSellers = $this->productModel->getBestSellers(24);
        $saleProducts = $this->productModel->getSaleProducts(24);
        $newProducts = $this->productModel->getNewProducts(24);
        $categories = $this->productModel->getCategories();

        // Nếu chưa có nhiều dữ liệu bán chạy/khuyến mãi thì dùng sản phẩm mới để tránh trang chủ bị trống.
        if (count($bestSellers) < 8) {
            $bestSellers = $this->productModel->getNewProducts(24);
        }
        if (count($saleProducts) < 8) {
            $saleProducts = $this->productModel->getNewProducts(24);
        }
        
        // Truyền dữ liệu vào View
        $data = [
            'bestSellers' => $bestSellers,
            'saleProducts' => $saleProducts,
            'newProducts' => $newProducts,
            'categories' => $categories,
            'pageTitle' => 'Trang chủ - Nhà thuốc 1985'
        ];
        
        // Load view
        $this->loadView('home', $data);
    }
}
