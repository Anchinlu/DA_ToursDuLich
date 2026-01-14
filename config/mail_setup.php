<?php
// Load thư viện PHPMailer từ Composer
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOrderConfirmation($userEmail, $userName, $orderData) {
    $mail = new PHPMailer(true);

    try {
        // 1. Cấu hình Server (Dùng Gmail SMTP)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // --- THAY THÔNG TIN CỦA BẠN VÀO ĐÂY ---
        $mail->Username   = 'dh2594182@gmail.com'; // Email của bạn
        $mail->Password   = 'ijohonjkniitlftp';
        // ---------------------------------------

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // 2. Người gửi & Người nhận
        $mail->setFrom('noreply@chinliu.com', 'Chinliu Tour Booking');
        $mail->addAddress($userEmail, $userName);

        // 3. Nội dung Email
        $mail->isHTML(true);
        $mail->Subject = 'Xác nhận đơn hàng #' . $orderData['id'] . ' - Chinliu Tour';
        
        // Tạo nội dung HTML đẹp mắt
        $bodyContent = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                <h2 style='color: #0866FF; text-align: center;'>ĐẶT TOUR THÀNH CÔNG!</h2>
                <p>Xin chào <strong>$userName</strong>,</p>
                <p>Đơn hàng của bạn đã được Admin xác nhận thành công. Dưới đây là thông tin vé điện tử của bạn:</p>
                
                <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                    <tr style='background-color: #f2f2f2;'>
                        <td style='padding: 10px; border: 1px solid #ddd;'>Mã đơn:</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'><strong>#{$orderData['id']}</strong></td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'>Tên Tour:</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$orderData['TenTour']}</td>
                    </tr>
                    <tr style='background-color: #f2f2f2;'>
                        <td style='padding: 10px; border: 1px solid #ddd;'>Ngày khởi hành:</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>" . date('d/m/Y', strtotime($orderData['NgayKhoiHanh'])) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'>Số lượng:</td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$orderData['SoNguoi']} khách</td>
                    </tr>
                    <tr style='background-color: #f2f2f2;'>
                        <td style='padding: 10px; border: 1px solid #ddd;'>Tổng tiền:</td>
                        <td style='padding: 10px; border: 1px solid #ddd; color: red; font-weight: bold;'>" . number_format($orderData['TongGia']) . " VNĐ</td>
                    </tr>
                </table>

                <p style='margin-top: 20px;'>Vui lòng đến điểm hẹn trước 30 phút. Chúc bạn có một chuyến đi vui vẻ!</p>
                <p style='color: #888; font-size: 12px; text-align: center;'>Đây là email tự động, vui lòng không trả lời.</p>
            </div>
        ";

        $mail->Body = $bodyContent;
        $mail->AltBody = "Xác nhận đơn hàng #{$orderData['id']}. Tổng tiền: " . number_format($orderData['TongGia']) . " VNĐ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Ghi log lỗi nếu cần
        return false;
    }
}
?>