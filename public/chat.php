<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/constants.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_errors'] = ["Vui lòng đăng nhập để sử dụng tính năng Tư vấn trực tuyến."];
    header("Location: " . BASE_URL . "auth/login");
    exit;
}

require_once __DIR__ . '/../config/pusher_config.php';
$conversation_id = $_GET['id'] ?? null; 

// Auto-Reconnect Logic: Nếu User vào /consult mà không có ID, tự động tìm kiếm xem họ có phòng chat hiện hữu không
if (!$conversation_id) {
    require_once __DIR__ . '/../config/db_connect.php';
    $userId = $_SESSION['user_id'];
    $isDoctorQuery = ($_SESSION['role_id'] == 3);
    
    $stmtFind = $pdo->prepare("SELECT id FROM conversations WHERE " . ($isDoctorQuery ? "doctor_id" : "customer_id") . " = ? ORDER BY created_at DESC LIMIT 1");
    $stmtFind->execute([$userId]);
    $activeConvo = $stmtFind->fetch();
    
    if ($activeConvo) {
        // Có phòng chưa xoá -> Ép văng vào phòng đó
        header("Location: /consult?id=" . $activeConvo['id']);
        exit;
    }
}

$partner_name = "Tư vấn viên Chuyên nghiệp";
$partner_id = null;
$partner_initial = "T";

if ($conversation_id) {
    require_once __DIR__ . '/../config/db_connect.php';
    $stmt = $pdo->prepare("
        SELECT c.*, doc.full_name as doctor_name, cus.full_name as customer_name 
        FROM conversations c 
        JOIN users doc ON c.doctor_id = doc.user_id 
        JOIN users cus ON c.customer_id = cus.user_id 
        WHERE c.id = ?
    ");
    $stmt->execute([$conversation_id]);
    $convo = $stmt->fetch();
    
    if ($convo) {
        $isDoctor = ($_SESSION['role_id'] == 3);
        if ($isDoctor) {
            $partner_name = $convo['customer_name']; // Sẽ show tên Khách cho BS
            $partner_id = $convo['customer_id'];
        } else {
            $partner_name = "BS. " . $convo['doctor_name']; // Show tên BS cho Khách
            $partner_id = $convo['doctor_id'];
        }
        $partner_initial = mb_substr(str_replace('BS. ', '', $partner_name), 0, 1, 'UTF-8');
        
        $homeUrl = $isDoctor ? '/doctor/dashboard' : '/home';
    } else {
        die("Hội thoại không tồn tại hoặc bạn không có quyền truy cập.");
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Tư Vấn Nhà Thuốc</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="/public/css/chat.css">
    
    <script>
        window.CURRENT_USER_ID = <?php echo $_SESSION['user_id']; ?>;
        window.CURRENT_CONVERSATION_ID = <?php echo $conversation_id ? $conversation_id : 'null'; ?>;
        window.PUSHER_KEY = '<?php echo constant("PUSHER_APP_KEY") !== "" ? PUSHER_APP_KEY : ""; ?>';
        window.PUSHER_CLUSTER = '<?php echo PUSHER_APP_CLUSTER; ?>';
    </script>
</head>
<body style="background: #eef2f5;">

<?php if (!$conversation_id): ?>
    <!-- ============================================== -->
    <!-- WAITING ROOM (KHÁCH HÀNG BẤM TÌM BÁC SĨ)         -->
    <!-- ============================================== -->
    <div style="max-width: 600px; margin: 100px auto; text-align: center; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
        <h2 style="color: #1e293b; font-size: 28px;">Tư Vấn Sức Khỏe Trực Tuyến</h2>
        <p style="color: #64748b; font-size: 16px;">Biết rõ bệnh tình - Chữa trị kịp thời. Bác sĩ chuyên khoa luôn sẵn sàng hỗ trợ bạn.</p>
        
        <div id="queueStatus" style="margin: 30px 0; color: #0084ff; font-weight: 500;"></div>
        
        <button id="findDoctorBtn" style="padding: 14px 28px; background: #0084ff; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 132, 255, 0.4);">
            <svg width="20" height="20" fill="currentColor" style="vertical-align: middle; margin-right: 5px;" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            Liên hệ Bác sĩ ngay
        </button>
    </div>
    
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script>
        const THE_USER_ID = <?php echo $_SESSION['user_id']; ?>;
        let reqId = null;
        let pTimeout = null;

        const pusher = new Pusher('<?php echo PUSHER_APP_KEY; ?>', {
            cluster: '<?php echo PUSHER_APP_CLUSTER; ?>',
            authEndpoint: '/api/pusher_auth.php' // Required for private channels
        });
        
        // Listen for Doctor Acceptance
        const channel = pusher.subscribe('private-customer-' + THE_USER_ID);
        channel.bind('request-status', function(data) {
            console.log("Status update from Doctor: ", data);
            if (data.status === 'accepted') {
                if(pTimeout) clearTimeout(pTimeout);
                document.getElementById('queueStatus').innerHTML = "<b style='color:green'>Bác sĩ đã tiếp nhận! Đang chuyển hướng vào phòng chat...</b>";
                
                // Trễ 1 chút cho user đọc thông báo rồi nhảy vào chat
                setTimeout(() => {
                    window.location.href = '/consult?id=' + data.conversation_id;
                }, 1000);
            } else if (data.status === 'exhausted') {
                if(pTimeout) clearTimeout(pTimeout);
                document.getElementById('queueStatus').innerHTML = "<b style='color:red'>Tất cả bác sĩ tuyến trên hiện đang bận hoặc không online. Vui lòng thử lại sau vài phút.</b>";
                document.getElementById('findDoctorBtn').style.display = 'inline-block';
            }
        });

        document.getElementById('findDoctorBtn').addEventListener('click', function() {
            this.style.display = 'none';
            document.getElementById('queueStatus').innerHTML = "<i>Hệ thống đang tự động lọc và tìm bác sĩ trực tuyến... Vui lòng chờ...</i>";
            
            fetch('/api/handle_request.php', { 
                method: 'POST', 
                body: new URLSearchParams({action: 'start'}) 
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    reqId = data.request_id;
                    // Bắt đầu đếm ngược 30s timeout nếu bác sĩ ko Accept
                    pTimeout = setTimeout(() => advanceQueue(), 30000);
                } else {
                    document.getElementById('queueStatus').innerHTML = "<b style='color:red'>" + (data.error || "Gặp sự cố, vui lòng thử lại.") + "</b>";
                    this.style.display = 'inline-block';
                }
            });
        });

        function advanceQueue() {
            document.getElementById('queueStatus').innerHTML = "<i>Bác sĩ phân bổ đầu tiên đang bận, đang tự động luân chuyển sang bác sĩ tiếp theo...</i>";
            fetch('/api/handle_request.php', { method: 'POST', body: new URLSearchParams({action: 'advance', request_id: reqId}) });
            pTimeout = setTimeout(() => advanceQueue(), 30000);
        }
    </script>

<?php else: ?>
    <!-- ============================================== -->
    <!-- PHÒNG CHAT CHÍNH THỨC (Sau khi khớp thành công)  -->
    <!-- ============================================== -->
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h2>Khung chat</h2>
            </div>
            <div class="conversation-list">
                <div class="conversation-item active">
                    <div class="avatar"><?php echo strtoupper($partner_initial); ?></div>
                    <div class="conversation-info">
                        <h3><?php echo htmlspecialchars($partner_name); ?></h3>
                        <p>Đang kết nối để tư vấn...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung chat chính -->
        <div class="chat-main">
            <div class="chat-header">
                <div class="header-user-info">
                    <div class="avatar" style="width: 40px; height: 40px; font-size: 16px;"><?php echo strtoupper($partner_initial); ?></div>
                    <h3><?php echo htmlspecialchars($partner_name); ?></h3>
                </div>
                <div class="header-actions">
                    <!-- Nút Quay về -->
                    <a href="<?php echo $homeUrl; ?>" title="Trở về Trang Chính" style="color: #64748b; margin-right: 15px; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; text-decoration: none; transition: background 0.2s;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    </a>
                    
                    <!-- Nút Kết Thúc Tư Vấn (Cancel/Delete Chat) -->
                    <button id="endConsultBtn" title="Kết Thúc Giao Dịch Tư Vấn" onclick="endConsultation()" style="background: #ef4444; color: white; border: none; border-radius: 20px; padding: 8px 16px; margin-right: 10px; font-weight: bold; cursor: pointer; transition: transform 0.2s;">
                        Kết Thúc
                    </button>
                    
                    <!-- Nút bắt đầu gọi Video -->
                    <button id="startCallBtn" title="Gọi Video" aria-label="Audio/Video Call">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                    </button>
                    <button title="Thông tin">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </button>
                </div>
            </div>

            <!-- Box Tin nhắn -->
            <div class="chat-messages" id="chatMessages">
                <!-- Data đổ Ajax vào đây -->
            </div>

            <!-- Box nhập text -->
            <div class="chat-input-area">
                <input type="text" id="chatInput" class="chat-input" placeholder="Nhắn tin..." autocomplete="off">
                <button class="send-btn" id="sendBtn">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- UI Modal Calling -->
    <div id="videoCallModal">
        <h2 style="color:white; margin-bottom: 20px;">Đang gọi Video WebRTC...</h2>
        <div class="video-container">
            <video id="remoteVideo" autoplay playsinline></video>
            <video id="localVideo" autoplay playsinline muted></video>
        </div>
        <div class="call-controls">
            <button class="end-call-btn" id="endCallBtn">Kết thúc cuộc gọi</button>
        </div>
    </div>

    <!-- WebRTC/Pusher Scripts -->
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script src="https://unpkg.com/peerjs@1.5.2/dist/peerjs.min.js"></script>
    
    <script>
        // Lấy Partner ID từ conversation trong thực tế để truyền cho PeerJS
        window.PARTNER_USER_ID = <?php echo $partner_id ? $partner_id : 'null'; ?>;
        window.THE_HOME_URL = "<?php echo $homeUrl; ?>";
    </script>
    <script src="/public/js/chat.js?v=<?php echo time(); ?>"></script>
    <script src="/public/js/rtc_call.js?v=<?php echo time(); ?>"></script>

<?php endif; ?>
</body>
</html>
