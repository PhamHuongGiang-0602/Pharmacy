<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../Models/InventoryModel.php';

class AdminInventoryController extends BaseController {
    private $inventoryModel;
    
    public function __construct() {
        // Kiểm tra quyền Admin/Kho (Giả sử logic check role đơn giản)
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 3])) {
            $this->redirect(BASE_URL . 'auth/login');
        }
        $this->inventoryModel = new InventoryModel();
    }
    
    /**
     * Dashboard Quản lý kho
     */
    public function index() {
        $summary = $this->inventoryModel->getInventorySummary();
        $alerts = $this->inventoryModel->getExpiryAlerts();
        
        $this->loadView('admin/inventory/index', [
            'summary' => $summary,
            'alerts' => $alerts,
            'pageTitle' => 'Quản lý kho & Lô hàng (FEFO)'
        ]);
    }
    
    /**
     * Xem chi tiết lô hàng của một sản phẩm
     */
    public function productBatches() {
        $productId = $_GET['id'] ?? 0;
        if (!$productId) {
            $this->redirect(BASE_URL . 'admin/inventory');
        }
        
        $batches = $this->inventoryModel->getBatchesByProductId($productId);
        $db = (new BaseModel())->db;
        $product = $db->query("SELECT product_name FROM products WHERE product_id = " . intval($productId))->fetchColumn();
        
        $this->loadView('admin/inventory/batches', [
            'batches' => $batches,
            'product_name' => $product,
            'pageTitle' => 'Chi tiết lô hàng: ' . $product
        ]);
    }
    /**
     * Giao diện nhập hàng mới
     */
    public function import() {
        $db = (new BaseModel())->db;
        $suppliers = $db->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
        $products = $db->query("SELECT product_id, product_name FROM products WHERE is_active = TRUE")->fetchAll(PDO::FETCH_ASSOC);
        
        $this->loadView('admin/inventory/import', [
            'suppliers' => $suppliers,
            'products' => $products,
            'pageTitle' => 'Nhập hàng vào kho'
        ]);
    }
    
    /**
     * Xử lý lưu phiếu nhập hàng
     */
    public function handleImport() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'supplier_id' => $_POST['supplier_id'],
                'user_id' => $_SESSION['user_id'],
                'receipt_date' => $_POST['receipt_date'],
                'invoice_number' => $_POST['invoice_number'],
                'total_amount' => $_POST['total_amount'],
                'notes' => $_POST['notes']
            ];
            
            // Lấy dữ liệu các lô hàng từ form (Giả sử JSON hoặc mảng)
            $items = [];
            if (isset($_POST['products']) && is_array($_POST['products'])) {
                foreach ($_POST['products'] as $i => $pid) {
                    $items[] = [
                        'product_id' => $pid,
                        'batch_number' => $_POST['batch_numbers'][$i],
                        'manufacture_date' => $_POST['manufacture_dates'][$i],
                        'expiry_date' => $_POST['expiry_dates'][$i],
                        'quantity' => $_POST['quantities'][$i],
                        'purchase_price' => $_POST['purchase_prices'][$i],
                        'selling_price' => $_POST['selling_prices'][$i],
                        'storage_location' => $_POST['storage_locations'][$i]
                    ];
                }
            }
            
            if ($this->inventoryModel->addStockReceipt($data, $items)) {
                $_SESSION['success_message'] = "Nhập kho thành công!";
            } else {
                $_SESSION['error_message'] = "Có lỗi xảy ra khi nhập kho.";
            }
            
            $this->redirect(BASE_URL . 'admin/inventory');
        }
    }
}
