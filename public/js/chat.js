// public/js/chat.js
document.addEventListener('DOMContentLoaded', () => {
    // Khởi tạo Pusher
    // Lưu ý: Cần thêm Pusher JS qua thẻ <script> từ CDN trong HTML và truyền các biến môi trường
    if (typeof PUSHER_KEY === 'undefined') {
        console.error("Cần cấu hình PUSHER_KEY trong file giao diện.");
        return;
    }

    const pusher = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_CLUSTER,
        authEndpoint: '/api/pusher_auth.php'
    });

    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    
    // CURRENT_CONVERSATION_ID và CURRENT_USER_ID lấy từ backend (truyền vào HTML)
    const conversationId = window.CURRENT_CONVERSATION_ID;
    const currentUserId = window.CURRENT_USER_ID;

    // Kênh subscribe
    if (conversationId) {
        const channel = pusher.subscribe('private-chat-' + conversationId);
        
        channel.bind('new-message', function(data) {
            // Kiểm tra xem tin nhắn có phải do mình bị lặp không
            if (data.sender_id != currentUserId) {
                appendMessage(data.content, 'received');
            }
        });
        
        // --- XỬ LÝ LẮNG NGHE KHÔNG GIAN BỊ HỦY BỞI ĐỐI TÁC ---
        channel.bind('conversation-ended', function(data) {
            alert("Đã kết thúc quá trình tư vấn. Hệ thống tự động chuyển hướng làm việc...");
            window.location.href = window.THE_HOME_URL || '/';
        });

        // Tải tin nhắn cũ
        loadMessages();
    }

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function loadMessages() {
        fetch(`/api/get_messages.php?conversation_id=${conversationId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    chatMessages.innerHTML = ''; // reset
                    data.messages.forEach(msg => {
                        const type = msg.sender_id == currentUserId ? 'sent' : 'received';
                        appendMessage(msg.content, type);
                    });
                }
            })
            .catch(err => console.error("Lỗi khi tải tin nhắn: ", err));
    }

    function appendMessage(content, type) {
        const row = document.createElement('div');
        row.className = `message-row ${type}`;
        
        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.textContent = content; // textContent tự escape HTML (an toàn XSS)
        
        row.appendChild(bubble);
        chatMessages.appendChild(row);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function sendMessage() {
        const text = chatInput.value.trim();
        if (!text || !conversationId) return;

        chatInput.value = '';
        appendMessage(text, 'sent'); // Cập nhật UI nhanh

        const formData = new FormData();
        formData.append('conversation_id', conversationId);
        formData.append('content', text);

        fetch('/api/send_message.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                console.error("Gửi lỗi:", data.error);
                // Có thể xử lý logic xóa tin nhắn ảo nếu gửi thất bại
            }
        })
        .catch(err => console.error("Disconnect or error:", err));
    }
});

// Hàm gọi ra từ HTML nút bấm Kết Thúc Tư Vấn
window.endConsultation = function() {
    if (!confirm("Bạn xác nhận kết thúc chứ?")) return;
    
    fetch('/api/end_consult.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ conversation_id: window.CURRENT_CONVERSATION_ID })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            window.location.href = window.THE_HOME_URL || '/';
        } else {
            alert(data.error || "Có lỗi xảy ra khi kết thúc.");
        }
    });
};
