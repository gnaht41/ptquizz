<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';

class MailHelper {
    /**
     * Gửi email sử dụng SMTP Gmail (hoặc SMTP khác)
     * 
     * @param string $to Email người nhận
     * @param string $subject Tiêu đề
     * @param string $body Nội dung (HTML)
     * @return bool|string True nếu thành công, Error message nếu thất bại
     */
    public static function send($to, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình Server
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;              // Bật để xem log nếu lỗi
            $mail->isSMTP();                                      // Gửi qua SMTP
            $mail->Host       = 'smtp.gmail.com';                 // Server SMTP Gmail
            $mail->SMTPAuth   = true;                             // Bật xác thực SMTP
            
            // QUAN TRỌNG: Người dùng cần điền thông tin này
            $mail->Username   = 'joydaide2004@gmail.com';           // Tên đăng nhập Gmail
            $mail->Password   = 'xmkkflslnezoepqz';              // Mật khẩu ứng dụng (App Password)
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Bảo mật TLS
            $mail->Port       = 587;                              // Cổng TLS: 587, SSL: 465
            $mail->CharSet    = 'UTF-8';

            // Người gửi & Người nhận
            $mail->setFrom('joydaide2004@gmail.com', 'PT QUIZ System');
            $mail->addAddress($to);

            // Nội dung
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
