<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/CartModel.php';
require_once __DIR__ . '/../Models/ProductModel.php';

class CartController extends BaseController {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }
    
    /**
     * Xem giỏ hàng
     */
    public function index() {
        $cartItems = [];
        $total = 0;
        $interactions = [];
        
        if (isset($_SESSION['user_id'])) {
            $cartId = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            if ($cartId) {
                $cartItems = $this->cartModel->getCartItems($cartId);
                // Kiểm tra tương tác thuốc
                $interactions = $this->cartModel->checkCartInteractions($cartId);
            } else {
                // Session user không còn hợp lệ trong DB -> logout mềm
                unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['full_name'], $_SESSION['role_id'], $_SESSION['user']);
            }
        } else {
            // Xử lý giỏ hàng tạm bằng Session cho khách vãng lai
            $sessionCart = $_SESSION['cart'] ?? [];
            foreach ($sessionCart as $productId => $qty) {
                $product = $this->productModel->getProductById($productId);
                if ($product) {
                    $cartItems[] = array_merge($product, ['quantity' => $qty]);
                }
            }
        }
        
        // Tính tổng tiền
        foreach ($cartItems as $item) {
            $total += ($item['current_price'] ?? $item['price']) * $item['quantity'];
        }
        
        $this->loadView('cart/index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'interactions' => $interactions,
            'pageTitle' => 'Giỏ hàng - Nhà thuốc 1985'
        ]);
    }
    
    /**
     * Thêm vào giỏ (Normal POST)
     */
    public function add() {
        $productId = $_POST['product_id'] ?? null;
        $qty = (int)($_POST['quantity'] ?? 1);
        
        if (!$productId) {
            $this->redirect(BASE_URL);
        }

        // Kiểm tra sản phẩm tồn tại
        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            $_SESSION['error_message'] = 'Sản phẩm không tồn tại.';
            $this->redirect(BASE_URL);
        }
        
        if (isset($_SESSION['user_id'])) {
            $cartId = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            if ($cartId) {
                $this->cartModel->addItem($cartId, $productId, $qty);
            }
        } else {
            // Khách vãng lai
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $qty;
            } else {
                $_SESSION['cart'][$productId] = $qty;
            }
        }
        
        $_SESSION['success_message'] = 'Đã thêm vào giỏ hàng thành công!';
        $this->redirect(BASE_URL . 'cart');
    }
    
    /**
     * API: Thêm vào giỏ (AJAX)
     */
    public function addAjax() {
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? $_POST['product_id'] ?? null;
        $qty = (int)($input['quantity'] ?? $_POST['quantity'] ?? 1);
        
        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
        }

        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        }
        
        $cartCount = 0;
        $total = 0;

        if (isset($_SESSION['user_id'])) {
            $cartId = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            if (!$cartId) {
                $this->json(['success' => false, 'message' => 'Phiên đăng nhập không hợp lệ']);
            }
            $success = $this->cartModel->addItem($cartId, $productId, $qty);
            $cartItems = $this->cartModel->getCartItems($cartId);
            $cartCount = count($cartItems);
            foreach ($cartItems as $item) {
                $total += $item['current_price'] * $item['quantity'];
            }
        } else {
            // Guest session cart
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $qty;
            } else {
                $_SESSION['cart'][$productId] = $qty;
            }
            $success = true;
            $cartCount = count($_SESSION['cart']);
            foreach ($_SESSION['cart'] as $pid => $pqty) {
                $p = $this->productModel->getProductById($pid);
                if ($p) $total += $p['current_price'] * $pqty;
            }
        }
        
        $this->json([
            'success' => $success, 
            'message' => $success ? 'Đã thêm sản phẩm vào giỏ hàng!' : 'Lỗi khi thêm vào giỏ hàng', 
            'cart_count' => $cartCount,
            'cart_total' => number_format($total) . 'đ'
        ]);
    }
    
    /**
     * API: Cập nhật số lượng (AJAX)
     */
    public function updateAjax() {
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;
        $qty = (int)($input['quantity'] ?? 1);
        
        if (!$productId) $this->json(['success' => false]);

        $total = 0;
        $itemTotal = 0;
        $cartCount = 0;
        $success = false;

        if (isset($_SESSION['user_id'])) {
            $cartId = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            if ($cartId) {
                $success = $this->cartModel->updateQuantity($cartId, $productId, $qty);
                $cartItems = $this->cartModel->getCartItems($cartId);
                $cartCount = count($cartItems);
                foreach ($cartItems as $item) {
                    $total += $item['current_price'] * $item['quantity'];
                    if ($item['product_id'] == $productId) {
                        $itemTotal = $item['current_price'] * $item['quantity'];
                    }
                }
            }
        } else {
            // Guest session update
            if (isset($_SESSION['cart'][$productId])) {
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$productId]);
                } else {
                    $_SESSION['cart'][$productId] = $qty;
                }
                $success = true;
                $cartCount = count($_SESSION['cart']);
                foreach ($_SESSION['cart'] as $pid => $pqty) {
                    $p = $this->productModel->getProductById($pid);
                    if ($p) {
                        $pTotal = $p['current_price'] * $pqty;
                        $total += $pTotal;
                        if ($pid == $productId) $itemTotal = $pTotal;
                    }
                }
            }
        }
        
        $this->json([
            'success' => $success, 
            'total' => number_format($total) . 'đ', 
            'item_total' => number_format($itemTotal) . 'đ',
            'cart_count' => $cartCount
        ]);
    }
    
    /**
     * API: Xóa khỏi giỏ (AJAX)
     */
    public function removeAjax() {
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;
        
        if (!$productId) $this->json(['success' => false]);

        $success = false;
        $total = 0;
        $cartCount = 0;

        if (isset($_SESSION['user_id'])) {
            $cartId = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            if ($cartId) {
                $success = $this->cartModel->removeItem($cartId, $productId);
                $cartItems = $this->cartModel->getCartItems($cartId);
                $cartCount = count($cartItems);
                foreach ($cartItems as $item) {
                    $total += $item['current_price'] * $item['quantity'];
                }
            }
        } else {
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                $success = true;
                $cartCount = count($_SESSION['cart']);
                foreach ($_SESSION['cart'] as $pid => $pqty) {
                    $p = $this->productModel->getProductById($pid);
                    if ($p) $total += $p['current_price'] * $pqty;
                }
            }
        }
        
        $this->json([
            'success' => $success, 
            'total' => number_format($total) . 'đ', 
            'cart_count' => $cartCount
        ]);
    }
    
    /**
     * Chuyển hướng sang trang thanh toán của OrderController
     */
    public function checkout() {
        $this->redirect(BASE_URL . 'order/checkout');
    }
}
