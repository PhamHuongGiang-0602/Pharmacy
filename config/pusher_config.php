<?php
// config/pusher_config.php
require_once __DIR__ . '/../vendor/autoload.php';

// Thay thế các cấu hình dưới đây bằng thông tin từ Pusher Dashboard (pusher.com)
define('PUSHER_APP_ID', '2143188');
define('PUSHER_APP_KEY', 'd6ab45203ca2f60c642c');
define('PUSHER_APP_SECRET', 'f032cf367fc0c050a104');
define('PUSHER_APP_CLUSTER', 'ap1');

/**
 * Trả về instance của Pusher để sử dụng ở các file khác
 */
function getPusher() {
    $options = array(
        'cluster' => PUSHER_APP_CLUSTER,
        'useTLS' => true
    );
    
    // Bỏ qua SSL (Chỉ dùng cho Localhost/XAMPP)
    $client = new \GuzzleHttp\Client([
        'verify' => false,
    ]);

    return new Pusher\Pusher(
        PUSHER_APP_KEY,
        PUSHER_APP_SECRET,
        PUSHER_APP_ID,
        $options,
        $client
    );
}
?>
