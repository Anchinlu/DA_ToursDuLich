<?php
session_start();
require_once '../config/db_connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để thích bài viết!']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if ($post_id > 0) {
    try {
        // 2. Kiểm tra xem người dùng đã thích bài này chưa
        $check = $db->prepare("SELECT id FROM LuotThich WHERE idBaiViet = ? AND idNguoiDung = ?");
        $check->execute([$post_id, $user_id]);
        $row = $check->fetch();

        if ($row) {
            // --- TRƯỜNG HỢP ĐÃ THÍCH -> XÓA (UNLIKE) ---
            $delete = $db->prepare("DELETE FROM LuotThich WHERE idBaiViet = ? AND idNguoiDung = ?");
            $delete->execute([$post_id, $user_id]);
            $action = 'unliked';
        } else {
            // --- TRƯỜNG HỢP CHƯA THÍCH -> THÊM (LIKE) ---
            $insert = $db->prepare("INSERT INTO LuotThich (idBaiViet, idNguoiDung) VALUES (?, ?)");
            $insert->execute([$post_id, $user_id]);
            $action = 'liked';
        }

        // 3. Đếm lại tổng số lượt thích của bài viết đó
        $countStmt = $db->prepare("SELECT COUNT(*) FROM LuotThich WHERE idBaiViet = ?");
        $countStmt->execute([$post_id]);
        $total_likes = $countStmt->fetchColumn();

        // 4. Trả về kết quả cho Javascript
        echo json_encode([
            'status' => 'success',
            'action' => $action,
            'likes' => $total_likes
        ]);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống']);
    }
}
?>