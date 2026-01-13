<?php
session_start();
require_once '../config/db_connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.history.back();</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = $_POST['tour_id'];
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    // Validate dữ liệu
    if ($rating < 1 || $rating > 5) $rating = 5;
    if (empty($comment)) {
        echo "<script>alert('Vui lòng nhập nội dung!'); window.history.back();</script>";
        exit;
    }

    try {
        // 2. [QUAN TRỌNG] Kiểm tra xem khách có đơn hàng Đã xác nhận không
        $check = $db->prepare("SELECT id FROM DonDatTour WHERE idNguoiDung = ? AND idTour = ? AND TrangThai = 'Đã xác nhận'");
        $check->execute([$user_id, $tour_id]);

        if ($check->rowCount() == 0) {
            echo "<script>alert('Lỗi: Bạn phải đặt và tham gia tour này mới được quyền đánh giá!'); window.history.back();</script>";
            exit;
        }

        // 3. Nếu hợp lệ thì Lưu vào CSDL
        $stmt = $db->prepare("INSERT INTO DanhGia (idTour, idNguoiDung, SoSao, BinhLuan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tour_id, $user_id, $rating, $comment]);

        echo "<script>alert('Cảm ơn bạn đã đánh giá!'); window.location.href='../tour_detail.php?id=$tour_id';</script>";

    } catch (Exception $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>