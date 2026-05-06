<?php

namespace App\Services\Emails;

class PasswordResetEmail extends BaseEmail {
    private $otp;
    private $userName;

    public function __construct($otp, $userName) {
        $this->otp = $otp;
        $this->userName = $userName;
    }

    protected function getSubject(): string {
        return "🔒 Mã xác nhận khôi phục mật khẩu - Nhà thuốc 1985";
    }

    protected function getBody(): string {
        return "
        <div style='background-color: #f4f7f6; padding: 40px 0; font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                <!-- Header -->
                <div style='background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 30px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 28px; font-weight: bold; letter-spacing: 1px;'>Nhà thuốc 1985</h1>
                    <p style='color: #dbeafe; margin: 10px 0 0 0; font-size: 16px;'>Bảo mật tài khoản</p>
                </div>
                
                <!-- Body -->
                <div style='padding: 40px 30px;'>
                    <h2 style='color: #1f2937; font-size: 22px; margin-top: 0;'>Chào {$this->userName},</h2>
                    <p style='color: #4b5563; font-size: 16px; line-height: 1.6;'>Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn tại hệ thống Nhà thuốc 1985. Dưới đây là mã xác thực (OTP) của bạn:</p>
                    
                    <div style='text-align: center; margin: 35px 0;'>
                        <div style='display: inline-block; background: #f3f4f6; border: 2px dashed #9ca3af; padding: 15px 40px; border-radius: 8px;'>
                            <span style='font-size: 32px; font-weight: 800; color: #1e40af; letter-spacing: 8px;'>{$this->otp}</span>
                        </div>
                    </div>
                    
                    <p style='color: #ef4444; font-size: 14px; text-align: center; font-weight: bold;'>⚠️ Mã này sẽ hết hạn sau 15 phút.</p>
                    <p style='color: #6b7280; font-size: 15px; line-height: 1.6; text-align: center;'>Vui lòng không chia sẻ mã này với bất kỳ ai để đảm bảo an toàn cho tài khoản của bạn. Nếu bạn không yêu cầu đổi mật khẩu, vui lòng bỏ qua email này.</p>
                </div>
                
                <!-- Footer -->
                <div style='background: #f3f4f6; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;'>
                    <p style='color: #9ca3af; font-size: 12px; margin: 0;'>&copy; 2026 Nhà thuốc 1985. 25 Tựu Liệt, Thanh Trì, Hà Nội.</p>
                </div>
            </div>
        </div>
        ";
    }
}
