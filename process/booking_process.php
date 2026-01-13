<?php
session_start();
require_once '../config/db_connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $tour_id = $_POST['tour_id'];
    $start_date = $_POST['start_date'];
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);

    try {
        // 3. Lấy giá tour hiện tại từ CSDL (Để bảo mật, không lấy giá từ form HTML)
        $stmt = $db->prepare("SELECT Gia FROM Tour WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch();

        if ($tour) {
            $price = $tour['Gia'];
            
            // 4. Tính tổng tiền (Server-side calculation)
            // Giả sử trẻ em tính 50% giá
            $total_price = ($price * $adults) + ($price * 0.5 * $children);

            // 5. Lưu vào bảng DonDatTour
            $sql = "INSERT INTO DonDatTour (idNguoiDung, idTour, NgayKhoiHanh, SoNguoiLon, SoTreEm, TongGia, TrangThai) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Chờ xử lý')";
            
            $stmtInsert = $db->prepare($sql);
            $stmtInsert->execute([$user_id, $tour_id, $start_date, $adults, $children, $total_price]);

            // 6. Chuyển hướng đến trang thành công (trỏ tới file trong thư mục pages)
            $booking_id = $db->lastInsertId();
            header("Location: /DoAn_TourDuLich/pages/booking_success.php?id=" . $booking_id);
            exit;
        } else {
            echo "Lỗi: Tour không tồn tại.";
        }

    } catch (Exception $e) {
        echo "Lỗi hệ thống: " . $e->getMessage();
    }
} else {
    // Nếu cố tình truy cập trực tiếp link này
    header("Location: index.php");
}
?>