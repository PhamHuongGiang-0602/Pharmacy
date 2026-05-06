<?php

namespace App\Services\Emails;

class OrderCompletedEmail extends BaseEmail {
    private $order;
    private $userName;

    public function __construct($order, $userName) {
        $this->order = $order;
        $this->userName = $userName;
    }

    protected function getSubject(): string {
        return "✅ Đơn hàng #" . $this->order['order_id'] . " đã được giao thành công - Nhà thuốc 1985";
    }

    protected function getBody(): string {
        $totalAmount = number_format($this->order['total_amount'], 0, ',', '.') . 'đ';
        
        return "
        <div style='background-color: #f4f7f6; padding: 40px 0; font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                <!-- Header -->
                <div style='background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 28px; font-weight: bold; letter-spacing: 1px;'>Nhà thuốc 1985</h1>
                    <p style='color: #d1fae5; margin: 10px 0 0 0; font-size: 16px;'>Cảm ơn bạn đã đồng hành</p>
                </div>
                
                <!-- Body -->
                <div style='padding: 40px 30px;'>
                    <h2 style='color: #1f2937; font-size: 22px; margin-top: 0;'>Chào {$this->userName},</h2>
                    <p style='color: #4b5563; font-size: 16px; line-height: 1.6;'>Tuyệt vời! Đơn hàng của bạn đã được giao và thanh toán thành công. Hy vọng bạn hài lòng với sản phẩm và dịch vụ của Nhà thuốc 1985.</p>
                    
                    <div style='background: #f9fafb; border-left: 4px solid #10b981; padding: 20px; border-radius: 4px; margin: 30px 0;'>
                        <h3 style='margin: 0 0 15px 0; color: #374151; font-size: 18px;'>Đơn hàng #{$this->order['order_id']}</h3>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 15px;'>Trạng thái:</td>
                                <td style='padding: 8px 0; color: #10b981; font-size: 15px; text-align: right; font-weight: bold;'>Đã hoàn thành</td>
                            </tr>
                            <tr>
                                <td style='padding: 15px 0 8px 0; color: #111827; font-size: 16px; font-weight: bold; border-top: 1px solid #e5e7eb;'>Tổng thanh toán:</td>
                                <td style='padding: 15px 0 8px 0; color: #ef4444; font-size: 18px; text-align: right; font-weight: bold; border-top: 1px solid #e5e7eb;'>{$totalAmount}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <p style='color: #4b5563; font-size: 16px; line-height: 1.6;'>Nếu bạn có bất kỳ phản hồi hoặc thắc mắc nào về sản phẩm, đừng ngần ngại liên hệ với chúng tôi.</p>
                    
                    <div style='text-align: center; margin-top: 40px;'>
                        <a href='#' style='display: inline-block; background: #10b981; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; font-size: 16px;'>Đánh giá đơn hàng</a>
                    </div>
                </div>
                
                <!-- Footer -->
                <div style='background: #f3f4f6; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;'>
                    <p style='color: #6b7280; font-size: 14px; margin: 0 0 10px 0;'>Hotline hỗ trợ: <strong style='color: #10b981;'>1800 599 921</strong>.</p>
                    <p style='color: #9ca3af; font-size: 12px; margin: 0;'>&copy; 2026 Nhà thuốc 1985. 25 Tựu Liệt, Thanh Trì, Hà Nội.</p>
                </div>
            </div>
        </div>
        ";
    }
}
