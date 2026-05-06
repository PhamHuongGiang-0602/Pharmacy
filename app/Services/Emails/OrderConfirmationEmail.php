<?php

namespace App\Services\Emails;

class OrderConfirmationEmail extends BaseEmail {
    private $order;
    private $userName;

    public function __construct($order, $userName) {
        $this->order = $order;
        $this->userName = $userName;
    }

    protected function getSubject(): string {
        return "🎉 Xác nhận đơn hàng #" . $this->order['order_id'] . " - Nhà thuốc 1985";
    }

    protected function getBody(): string {
        $totalAmount = number_format($this->order['total_amount'], 0, ',', '.') . 'đ';
        $orderDate = date('d/m/Y H:i');
        
        return "
        <div style='background-color: #f4f7f6; padding: 40px 0; font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                <!-- Header -->
                <div style='background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 28px; font-weight: bold; letter-spacing: 1px;'>Nhà thuốc 1985</h1>
                    <p style='color: #d1fae5; margin: 10px 0 0 0; font-size: 16px;'>Thuốc tốt từ tâm</p>
                </div>
                
                <!-- Body -->
                <div style='padding: 40px 30px;'>
                    <h2 style='color: #1f2937; font-size: 22px; margin-top: 0;'>Chào {$this->userName},</h2>
                    <p style='color: #4b5563; font-size: 16px; line-height: 1.6;'>Cảm ơn bạn đã tin tưởng và đặt hàng tại Nhà thuốc 1985. Đơn hàng của bạn đã được tiếp nhận thành công và đang trong quá trình chuẩn bị.</p>
                    
                    <div style='background: #f9fafb; border-left: 4px solid #10b981; padding: 20px; border-radius: 4px; margin: 30px 0;'>
                        <h3 style='margin: 0 0 15px 0; color: #374151; font-size: 18px;'>Thông tin đơn hàng #{$this->order['order_id']}</h3>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 15px;'>Ngày đặt:</td>
                                <td style='padding: 8px 0; color: #111827; font-size: 15px; text-align: right; font-weight: 500;'>{$orderDate}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 15px;'>Trạng thái:</td>
                                <td style='padding: 8px 0; color: #d97706; font-size: 15px; text-align: right; font-weight: bold;'>Đang chờ xử lý</td>
                            </tr>
                            <tr>
                                <td style='padding: 15px 0 8px 0; color: #111827; font-size: 16px; font-weight: bold; border-top: 1px solid #e5e7eb;'>Tổng thanh toán:</td>
                                <td style='padding: 15px 0 8px 0; color: #ef4444; font-size: 18px; text-align: right; font-weight: bold; border-top: 1px solid #e5e7eb;'>{$totalAmount}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <p style='color: #4b5563; font-size: 16px; line-height: 1.6;'>Chúng tôi sẽ gửi thêm thông báo khi đơn hàng bắt đầu được giao. Bạn có thể theo dõi tiến trình đơn hàng trên website của chúng tôi.</p>
                    
                    <div style='text-align: center; margin-top: 40px;'>
                        <a href='#' style='display: inline-block; background: #10b981; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; font-size: 16px;'>Xem chi tiết đơn hàng</a>
                    </div>
                </div>
                
                <!-- Footer -->
                <div style='background: #f3f4f6; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;'>
                    <p style='color: #6b7280; font-size: 14px; margin: 0 0 10px 0;'>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ hotline <strong style='color: #10b981;'>1800 599 921</strong>.</p>
                    <p style='color: #9ca3af; font-size: 12px; margin: 0;'>&copy; 2026 Nhà thuốc 1985. 25 Tựu Liệt, Thanh Trì, Hà Nội.</p>
                </div>
            </div>
        </div>
        ";
    }
}
