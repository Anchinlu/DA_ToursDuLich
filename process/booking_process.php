<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $tour_id = $_POST['tour_id'];
    $start_date = $_POST['start_date'];
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);

    try {
        $stmt = $db->prepare("SELECT Gia FROM Tour WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch();

        if ($tour) {
            $price = $tour['Gia'];
        
            $total_price = ($price * $adults) + ($price * 0.5 * $children);

            $sql = "INSERT INTO DonDatTour (idNguoiDung, idTour, NgayKhoiHanh, SoNguoiLon, SoTreEm, TongGia, TrangThai) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Chờ xử lý')";
            
            $stmtInsert = $db->prepare($sql);
            $stmtInsert->execute([$user_id, $tour_id, $start_date, $adults, $children, $total_price]);

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
    header("Location: index.php");
}
?>