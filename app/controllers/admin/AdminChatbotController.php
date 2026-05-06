<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../Models/ChatbotModel.php';

class AdminChatbotController extends BaseController {
    
    public function __construct() {
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            // Chỉ Admin và Dược sĩ mới được dùng chatbot quản trị
            $this->redirect(BASE_URL . 'auth/login');
        }
    }
    
    public function index() {
        $this->loadView('admin/chatbot', [
            'pageTitle' => 'Trợ lý AI Nhà thuốc'
        ]);
    }

    public function api() {
        header('Content-Type: application/json; charset=utf-8');

        $body = json_decode(file_get_contents('php://input'), true);
        $message = trim($body['message'] ?? '');

        if ($message === '') {
            echo json_encode(['reply' => 'Chào bạn! Tôi có thể giúp gì cho nhà thuốc hôm nay?']);
            exit;
        }

        $cb = new ChatbotModel();
        
        $totalProducts    = $cb->getTotalProducts();
        $totalInventory   = $cb->getTotalInventory();
        $todayRevenue     = $cb->getTodayRevenue();
        $monthRevenue     = $cb->getThisMonthRevenue();
        $lastMonthRevenue = $cb->getLastMonthRevenue();

        $fmt = fn($n) => number_format($n, 0, ',', '.') . '₫';

        $dataContext = "DỮ LIỆU CƠ BẢN NHÀ THUỐC 1985:\n";
        $dataContext .= "- Thời gian hiện tại: " . date('d/m/Y') . "\n";
        $dataContext .= "- Tổng sản phẩm: $totalProducts\n";
        $dataContext .= "- Tổng tồn kho: $totalInventory đơn vị\n";
        $dataContext .= "- Doanh thu hôm nay: " . $fmt((float)$todayRevenue) . "\n";
        $dataContext .= "- Doanh thu tháng này: " . $fmt((float)$monthRevenue) . "\n";
        $dataContext .= "- Doanh thu tháng trước: " . $fmt((float)$lastMonthRevenue) . "\n";

        $apiKey = 'AIzaSyCu8LircEDPNJBDux2SVQtF7aXiU5bNf-I';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;
        
        $systemInstruction = "Bạn là 'Trợ lý AI Quản trị' của Nhà thuốc 1985. Nhiệm vụ của bạn là phân tích số liệu kinh doanh. Bạn đã được trang bị các CÔNG CỤ (Tools). Hãy TỰ ĐỘNG GỌI CÔNG CỤ khi cần tìm kiếm thuốc, xem doanh thu tháng bất kỳ, hoặc kiểm tra thuốc sắp hết hạn. Trả lời ngắn gọn, chuyên nghiệp bằng tiếng Việt.";
        
        $contents = [
            [
                "role" => "user",
                "parts" => [
                    ["text" => $dataContext . "\n\nCÂU HỎI CỦA CHỦ NHÀ THUỐC: " . $message]
                ]
            ]
        ];

        $tools = [
            [
                "function_declarations" => [
                    [
                        "name" => "searchProductStock",
                        "description" => "Tìm kiếm thông tin và số lượng tồn kho của một sản phẩm thuốc theo tên.",
                        "parameters" => [
                            "type" => "OBJECT",
                            "properties" => [
                                "keyword" => ["type" => "STRING", "description" => "Tên thuốc cần tìm"]
                            ],
                            "required" => ["keyword"]
                        ]
                    ],
                    [
                        "name" => "getRevenueByMonth",
                        "description" => "Lấy tổng doanh thu của nhà thuốc trong một tháng và năm cụ thể.",
                        "parameters" => [
                            "type" => "OBJECT",
                            "properties" => [
                                "month" => ["type" => "INTEGER", "description" => "Tháng (1-12)"],
                                "year" => ["type" => "INTEGER", "description" => "Năm"]
                            ],
                            "required" => ["month", "year"]
                        ]
                    ],
                    [
                        "name" => "getExpiringProducts",
                        "description" => "Lấy danh sách các lô thuốc sắp hết hạn sử dụng.",
                        "parameters" => [
                            "type" => "OBJECT",
                            "properties" => [
                                "days" => ["type" => "INTEGER", "description" => "Số ngày cảnh báo (vd: 30, 90)"]
                            ],
                            "required" => ["days"]
                        ]
                    ]
                ]
            ]
        ];

        // Vòng lặp gọi API (Tối đa 2 lần để xử lý Function Call)
        $max_turns = 2;
        $reply = "Xin lỗi, tôi không thể phân tích dữ liệu lúc này.";

        for ($i = 0; $i < $max_turns; $i++) {
            $payload = [
                "system_instruction" => ["parts" => [["text" => $systemInstruction]]],
                "contents" => $contents,
                "tools" => $tools,
                "generationConfig" => ["temperature" => 0.7]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            if (!$response) {
                $reply = "Lỗi kết nối CURL đến API Gemini.";
                break;
            }
            $result = json_decode($response, true);
            if (!isset($result['candidates'][0]['content'])) {
                if (isset($result['error'])) {
                    $errorCode = $result['error']['code'];
                    $msgLower = mb_strtolower($message);
                    
                    // Cơ chế Offline Fallback
                    if (str_contains($msgLower, 'doanh thu')) {
                        $reply = "💰 Báo cáo (Ngoại tuyến - Lỗi API $errorCode): Doanh thu hôm nay là " . $fmt((float)$todayRevenue) . ", cả tháng đạt " . $fmt((float)$monthRevenue) . ".";
                    } elseif (str_contains($msgLower, 'tồn kho') || str_contains($msgLower, 'hàng')) {
                        $outOfStock = $cb->getOutOfStockProducts();
                        $lowStock = $cb->getLowStockProducts(20);
                        $reply = "📦 Kho hàng (Ngoại tuyến - Lỗi API $errorCode): Tổng tồn " . number_format($totalInventory) . " đơn vị. Hiện có " . count($lowStock) . " thuốc sắp hết và " . count($outOfStock) . " thuốc đã hết.";
                    } else {
                        if ($errorCode == 429) {
                            $reply = "⚠️ **Chế độ Ngoại tuyến:** Key API đã vượt hạn mức (Quota). Tạm thời tôi chỉ có thể trả lời nhanh các từ khóa như 'doanh thu', 'tồn kho', 'hàng'.";
                        } else {
                            $reply = "❌ **Lỗi API ($errorCode):** " . $result['error']['message'];
                        }
                    }
                }
                break;
            }

            $responseMsg = $result['candidates'][0]['content'];
            $parts = $responseMsg['parts'][0];

            if (isset($parts['functionCall'])) {
                // Thêm phản hồi của AI (Yêu cầu gọi hàm) vào lịch sử
                $contents[] = $responseMsg;

                $funcName = $parts['functionCall']['name'] ?? '';
                $args = $parts['functionCall']['args'] ?? [];
                $funcResult = [];

                if ($funcName == 'searchProductStock') {
                    $keyword = $args['keyword'] ?? '';
                    $funcResult = $cb->searchProductStock($keyword);
                } elseif ($funcName == 'getRevenueByMonth') {
                    $month = $args['month'] ?? (int)date('m');
                    $year = $args['year'] ?? (int)date('Y');
                    $funcResult = ['revenue' => $cb->getRevenueByMonth($month, $year)];
                } elseif ($funcName == 'getExpiringProducts') {
                    $days = $args['days'] ?? 90;
                    $funcResult = $cb->getExpiringProducts($days);
                }

                // Gửi kết quả hàm lại cho AI
                $contents[] = [
                    "role" => "function",
                    "parts" => [
                        [
                            "functionResponse" => [
                                "name" => $funcName,
                                "response" => ["name" => $funcName, "content" => $funcResult]
                            ]
                        ]
                    ]
                ];
            } elseif (isset($parts['text'])) {
                $reply = $parts['text'];
                break; // Đã có câu trả lời văn bản, thoát vòng lặp
            }
        }

        // Xử lý markdown sang HTML cơ bản
        $reply = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $reply);
        $reply = preg_replace('/\* (.*?)\n/', '<li>$1</li>', $reply);

        echo json_encode(['reply' => $reply]);
        exit;
    }
}
