<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Services\Emails\BaseEmail;

class MailerService {
    /**
     * Sử dụng Constructor Property Promotion theo yêu cầu
     */
    public function __construct(private PHPMailer $mailer) {}

    /**
     * Dispatch an email
     */
    public function dispatch(BaseEmail $email, string $toEmail, string $subject = '', string $body = '', array $attachments = []): bool {
        try {
            $this->mailer->addAddress($toEmail);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            
            // Add attachments if any
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
                }
            }

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $this->mailer->ErrorInfo);
            return false;
        } finally {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
        }
    }
}
