<?php

namespace App\Services\Emails;

abstract class BaseEmail {
    protected ?string $trackingId = null;
    protected ?string $to = null;

    /**
     * Abstract methods to be implemented by subclasses
     */
    abstract protected function getSubject(): string;
    abstract protected function getBody(): string;

    /**
     * Get attachments if any
     */
    protected function getAttachments(): array {
        return [];
    }

    /**
     * Template Method pattern - defines the skeleton of the email sending process
     * Chuẩn bị + Gửi
     */
    public function send(string $to): bool {
        $this->to = $to;
        $this->trackingId = uniqid('mail_');

        // 1. Chuẩn bị dữ liệu (Subject, Body + Tracking Pixel)
        $subject = $this->getSubject();
        $body = $this->prepareBody($this->getBody());
        $attachments = $this->getAttachments();

        // 2. Khởi tạo MailerService và gửi
        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $this->configureMailer($mailer);
        
        $mailerService = new \App\Services\MailerService($mailer);
        $isSent = $mailerService->dispatch($this, $to, $subject, $body, $attachments);

        // 3. Theo dõi trạng thái (Lưu DB)
        $this->logStatus($isSent ? 'sent' : 'failed');

        return $isSent;
    }

    /**
     * Thêm tracking pixel vào body HTML
     */
    protected function prepareBody(string $rawBody): string {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $trackingUrl = "http://" . $host . "/Pharmacy/api/track_email.php?id=" . $this->trackingId;
        
        $pixel = "<img src='{$trackingUrl}' width='1' height='1' style='display:none; border:0;' alt='' />";
        
        // Chèn pixel vào cuối thẻ body nếu có, hoặc nối vào cuối chuỗi
        if (stripos($rawBody, '</body>') !== false) {
            return str_ireplace('</body>', $pixel . '</body>', $rawBody);
        }
        return $rawBody . $pixel;
    }

    /**
     * Cấu hình SMTP cho PHPMailer
     */
    protected function configureMailer(\PHPMailer\PHPMailer\PHPMailer $mailer): void {
        $mailer->isSMTP();
        $mailer->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
        $mailer->SMTPAuth   = true;
        $mailer->Username   = defined('SMTP_USER') ? SMTP_USER : '';
        $mailer->Password   = defined('SMTP_PASS') ? SMTP_PASS : '';
        $mailer->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'tls';
        $mailer->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mailer->CharSet    = 'UTF-8';
        
        $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@pharmacy.com';
        $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Nhà thuốc 1985';
        $mailer->setFrom($fromEmail, $fromName);
    }

    /**
     * Ghi log trạng thái email vào Database
     */
    protected function logStatus(string $status): void {
        global $pdo;
        if (!isset($pdo)) {
            $dbPath = __DIR__ . '/../../../config/db_connect.php';
            if (file_exists($dbPath)) {
                require $dbPath;
            } else {
                return; // Không có DB để log
            }
        }
        
        if (isset($pdo)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO email_logs (tracking_id, email_type, recipient, subject, status, sent_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $this->trackingId,
                    get_class($this),
                    $this->to,
                    $this->getSubject(),
                    $status
                ]);
            } catch (\PDOException $e) {
                error_log("Mail log error: " . $e->getMessage());
            }
        }
    }
}
