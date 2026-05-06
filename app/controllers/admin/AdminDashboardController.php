<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../Models/BaseModel.php';

class AdminDashboardController extends BaseController {
    
    public function __construct() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
            $this->redirect(BASE_URL . 'auth/login');
        }
    }
    
    public function index() {
        $db = (new BaseModel())->db;
        
        // 1. Thống kê tổng quan
        $totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $expiredCount = $db->query("SELECT COUNT(*) FROM batches WHERE status = 'expired' AND quantity_remaining > 0")->fetchColumn();
        
        $todayOrders = $db->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()")->fetchColumn();
        $monthlyRevenue = $db->query("SELECT SUM(total_amount) FROM orders WHERE status='completed' AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())")->fetchColumn() ?: 0;
        
        // 2. Doanh thu tháng hiện tại (Sử dụng SP get_monthly_revenue)
        $year = date('Y');
        $month = date('m');
        $stmt = $db->prepare("CALL get_monthly_revenue(:year, :month)");
        $stmt->execute(['year' => $year, 'month' => $month]);
        $revenueData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        // 3. Sản phẩm bán chạy (Từ View v_bestsellers)
        $bestSellers = $db->query("SELECT * FROM v_bestsellers LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

        // --- NEW STATS FOR CHARTS ---
        // Doanh thu 6 tháng gần nhất
        $revenueChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = date('m', strtotime("-$i months"));
            $y = date('Y', strtotime("-$i months"));
            $monthLabel = 'T' . $m;
            $q = $db->prepare("SELECT SUM(total_amount) as rev, COUNT(order_id) as cnt FROM orders WHERE status='completed' AND MONTH(order_date) = :m AND YEAR(order_date) = :y");
            $q->execute(['m' => $m, 'y' => $y]);
            $res = $q->fetch(PDO::FETCH_ASSOC);
            $revenueChartData[] = [
                'month' => $monthLabel,
                'revenue' => (float)($res['rev'] ?? 0) / 1000000,
                'completed' => (int)($res['cnt'] ?? 0)
            ];
        }

        // Trạng thái đơn hàng tháng này
        $statusStmt = $db->query("SELECT status, COUNT(*) as cnt FROM orders WHERE MONTH(order_date) = $month AND YEAR(order_date) = $year GROUP BY status");
        $statusCounts = $statusStmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $orderStatusData = [
            'completed' => $statusCounts['completed'] ?? 0,
            'processing' => ($statusCounts['confirmed'] ?? 0) + ($statusCounts['preparing'] ?? 0) + ($statusCounts['shipping'] ?? 0),
            'pending' => $statusCounts['pending'] ?? 0,
            'cancelled' => $statusCounts['cancelled'] ?? 0,
        ];

        // Người dùng mới 4 tuần gần nhất
        $weeklyNewUsers = [];
        for ($i = 3; $i >= 0; $i--) {
            // Note: Use Sunday to Saturday or just minus weeks.
            $start = date('Y-m-d', strtotime("-$i weeks Monday this week"));
            $end = date('Y-m-d', strtotime("-$i weeks Sunday this week"));
            $q = $db->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) BETWEEN :s AND :e");
            $q->execute(['s' => $start, 'e' => $end]);
            $cnt = $q->fetchColumn();
            $weeklyNewUsers[] = [
                'week' => date('d/m', strtotime($start)),
                'count' => $cnt
            ];
        }

        // Top danh mục
        $topCategories = $db->query("
            SELECT c.category_name as name, SUM(oi.quantity * oi.unit_price) as sales
            FROM order_details oi
            JOIN products p ON oi.product_id = p.product_id
            JOIN categories c ON p.category_id = c.category_id
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.status = 'completed'
            GROUP BY c.category_id
            ORDER BY sales DESC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $recentOrders = $db->query("SELECT * FROM orders ORDER BY order_id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('admin/dashboard', [
            'stats' => [
                'products' => $totalProducts,
                'orders' => $totalOrders,
                'users' => $totalUsers,
                'expired' => $expiredCount,
                'todayOrders' => $todayOrders,
                'monthlyRevenue' => $monthlyRevenue
            ],
            'revenueData' => $revenueData,
            'bestSellers' => $bestSellers,
            'revenueChartData' => $revenueChartData,
            'orderStatusData' => $orderStatusData,
            'weeklyNewUsers' => $weeklyNewUsers,
            'topCategories' => $topCategories,
            'recentOrders' => $recentOrders,
            'pageTitle' => 'Dashboard Quản trị'
        ]);
    }
}
