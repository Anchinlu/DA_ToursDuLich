<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $new_date = $_POST['new_date'];
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $db->prepare("SELECT id FROM DonDatTour WHERE id = ? AND idNguoiDung = ?");
        $stmt->execute([$order_id, $user_id]);
        
        if ($stmt->rowCount() > 0) {
            $sql = "UPDATE DonDatTour 
                    SET NgayKhoiHanh = ?, 
                        TrangThai = 'Chờ xử lý', 
                        LyDoHuy = NULL 
                    WHERE id = ?";
            
            $update = $db->prepare($sql);
            $update->execute([$new_date, $order_id]);

            echo "<script>
                    alert('Đã gửi yêu cầu đổi ngày thành công!');
                    window.location.href = 'history.php';
                  </script>";
        } else {
            echo "<script>alert('Lỗi!'); window.location.href='history.php';</script>";
        }

    } catch (Exception $e) {
        echo "Lỗi: " . $e->getMessage();
    }
} else {
    header("Location: history.php");
}
?>