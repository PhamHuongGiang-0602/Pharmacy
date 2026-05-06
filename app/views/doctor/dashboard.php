<?php
// app/views/doctor/dashboard.php
require_once __DIR__ . '/../../../config/pusher_config.php';
require_once __DIR__ . '/../../../config/db_connect.php';
require_once __DIR__ . '/../../../config/constants.php';

// Cố gắng tìm phiên chat gần nhất của bác sĩ này
$stmtConvo = $pdo->prepare("
    SELECT c.id, u.full_name as customer_name 
    FROM conversations c
    JOIN users u ON c.customer_id = u.user_id
    WHERE c.doctor_id = ? 
    ORDER BY c.created_at DESC 
    LIMIT 1
");
$stmtConvo->execute([$_SESSION['user_id']]);
$lastConvo = $stmtConvo->fetch();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard Bác sĩ'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #eef2f5; margin: 0; padding: 20px;}
        .dashboard-container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .header-top { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px; }
        .status-badge { background: #10b981; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(16,185,129,0.3);}
        .status-badge.offline { background: #ef4444; box-shadow: 0 2px 4px rgba(239,68,68,0.3); }
        .pulse { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: white; animation: pulse-animation 2s infinite; }
        
        .welcome-text { font-size: 24px; font-weight: 700; color: #1e293b; margin:0;}
        .subtitle { color: #64748b; font-size: 15px; margin-top: 5px; }

        .queue-alert { background: #fefce8; border: 2px solid #facc15; padding: 25px; border-radius: 12px; margin-top: 30px; display: none; transition: all 0.3s ease;}
        .queue-alert h3 { color: #b45309; margin-top: 0; display: flex; align-items: center; gap: 10px;}
        .queue-alert p { color: #854d0e; font-size: 16px;}
        
        .action-buttons { display: flex; gap: 15px; margin-top: 20px; }
        .btn { padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; transition: transform 0.2s, opacity 0.2s; }
        .btn:hover { transform: translateY(-2px); opacity: 0.9; }
        .btn:active { transform: translateY(0); }
        .btn-accept { background: #3b82f6; color: white; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.5); }
        .btn-reject { background: #f87171; color: white; }

        @keyframes pulse-animation { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); } }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header-top">
            <div>
                <h1 class="welcome-text">Xin chào, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
                <p class="subtitle">Bảng điều khiển kết nối Bệnh nhân WebRTC</p>
            </div>
            <div class="status-badge" id="statusBadge">
                <span class="pulse"></span> Đang trực tuyến
            </div>
        </div>
        
        <p style="color: #475569; line-height: 1.6;">
            Hệ thống Load Balancer đang tự động phân luồng khách hàng vào hàng đợi. Vui lòng giữ tab này mở. Khi có bệnh nhân cần tư vấn, hệ thống sẽ đẩy thẳng đến phiên của bạn.
        </p>

        <?php if ($lastConvo): ?>
        <div style="background: #f0fdf4; border: 1px solid #86efac; padding: 16px 24px; border-radius: 10px; margin-top: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div>
                <h4 style="margin: 0 0 5px 0; color: #166534; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z"/></svg>
                    Phiên trò chuyện hiện tại
                </h4>
                <p style="margin: 0; color: #15803d; font-size: 15px;">Bệnh nhân: <b><?php echo htmlspecialchars($lastConvo['customer_name']); ?></b></p>
            </div>
            <a href="<?= BASE_URL ?>consult?id=<?php echo $lastConvo['id']; ?>" class="btn" style="background: #16a34a; color: white; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                Trở lại phòng Chat
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M5 13h11.17l-4.88 4.88c-.39.39-.39 1.03 0 1.42.39.39 1.02.39 1.41 0l6.59-6.59c.39-.39.39-1.02 0-1.41l-6.58-6.6a.996.996 0 1 0-1.41 1.41L16.17 11H5c-.55 0-1 .45-1 1s.45 1 1 1z"/></svg>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="queue-alert" id="incomingAlert">
            <h3>
                <!-- Icon bell -->
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22q-.825 0-1.412-.587Q10 20.825 10 20h4q0 .825-.587 1.413Q12.825 22 12 22zm-6-3v-2h2v-7q0-2.075 1.25-3.688Q10.5 4.7 12.5 4.2v-.7q0-.625.438-1.062Q13.375 2 14 2q.625 0 1.062.438.438.437.438 1.062v.7q2 .5 3.25 2.112Q20 7.925 20 10v7h2v2z"/></svg>
                YÊU CẦU TƯ VẤN MỚI TỪ HỆ THỐNG!
            </h3>
            <p>Phát hiện có khách hàng đang chờ kết nối. Nhấn Chấp nhận để mở kênh Video/Chat.</p>
            
            <div class="action-buttons">
                <button class="btn btn-accept" onclick="respondRequest('accept')">Bắt đầu Tư vấn</button>
                <button class="btn btn-reject" onclick="respondRequest('reject')">Đang bận / Bỏ qua</button>
            </div>
        </div>
    </div>

    <!-- Pusher SDK -->
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script>
        const THE_DOCTOR_ID = <?php echo $_SESSION['user_id']; ?>;
        const PUSHER_KEY = '<?php echo constant("PUSHER_APP_KEY") !== "" ? PUSHER_APP_KEY : ""; ?>';
        const PUSHER_CLUSTER = '<?php echo PUSHER_APP_CLUSTER; ?>';
        let currentRequestId = null;

        // 1. Gửi Heartbeat duy trì Online State (15s/lần)
        function sendPing() {
            fetch('<?= BASE_URL ?>api/heartbeat.php').catch(e => {
                document.getElementById('statusBadge').className = 'status-badge offline';
                document.getElementById('statusBadge').innerHTML = 'Mất kết nối';
            });
        }
        setInterval(sendPing, 15000); 
        sendPing(); // Gửi ngay lúc load

        // 2. Lắng nghe điều phối (Routing Pusher)
        const pusher = new Pusher(PUSHER_KEY, {
            cluster: PUSHER_CLUSTER,
            authEndpoint: '<?= BASE_URL ?>api/pusher_auth.php'
        });
        
        // Private Channel để bảo mật
        const channel = pusher.subscribe('private-doctor-' + THE_DOCTOR_ID);
        
        channel.bind('incoming-consult', function(data) {
            console.log("CÓ CUỘC GỌI", data);
            currentRequestId = data.request_id;
            // Hiển thị khung Alert lên màn hình
            document.getElementById('incomingAlert').style.display = 'block';
            
            // Note: Trình duyệt ngày nay chỉ cấp quyền chuông khi người dùng đã click
        });

        // 3. Phản hồi Yêu cầu về Server (Accept hoặc Reject)
        window.respondRequest = function(actionType) {
            if(!currentRequestId) return;
            
            const formData = new URLSearchParams();
            formData.append('request_id', currentRequestId);
            formData.append('action', actionType);

            fetch('<?= BASE_URL ?>api/doctor_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success && actionType === 'accept') {
                    // Cấp phòng chat thành công, chuyển hướng vào Public View
                    window.location.href = '<?= BASE_URL ?>consult?id=' + data.conversation_id;
                } else if(actionType === 'reject') {
                    // Tắt Alert đi, hệ thống sẽ đẩy cho Bác sĩ khác
                    document.getElementById('incomingAlert').style.display = 'none';
                    currentRequestId = null;
                } else {
                    // Khi khách hàng bị Timeout và nhảy sang ông khác
                    alert(data.error || "Yêu cầu đã bị hủy hoặc được bác sĩ khác thụ lý.");
                    document.getElementById('incomingAlert').style.display = 'none';
                }
            });
        };
    </script>
</body>
</html>
