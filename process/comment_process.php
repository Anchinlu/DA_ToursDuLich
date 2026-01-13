<?php
session_start();
require_once '../config/db_connect.php';

// Hàm helper để xử lý đường dẫn Avatar (Copy từ file khác sang cho tiện dùng)
function getAvatarPath($path) {
    if (empty($path)) return '/DoAn_TourDuLich/assets/images/default-avatar.png';
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return '/DoAn_TourDuLich/' . $path;
    return '/DoAn_TourDuLich/uploads/avatars/' . $path;
}

header('Content-Type: application/json'); // Trả về JSON

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để bình luận!']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

if ($post_id > 0 && !empty($content)) {
    try {
        // 1. Thêm bình luận vào DB
        $stmt = $db->prepare("INSERT INTO BinhLuanBaiViet (idBaiViet, idNguoiDung, NoiDung, NgayBinhLuan) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$post_id, $user_id, $content]);
        $comment_id = $db->lastInsertId();

        // 2. Lấy thông tin người vừa bình luận để trả về cho JS hiển thị
        $userStmt = $db->prepare("SELECT TenDayDu, Avatar FROM NguoiDung WHERE id = ?");
        $userStmt->execute([$user_id]);
        $user = $userStmt->fetch();

        // 3. Đếm lại tổng số bình luận
        $countStmt = $db->prepare("SELECT COUNT(*) FROM BinhLuanBaiViet WHERE idBaiViet = ?");
        $countStmt->execute([$post_id]);
        $total_comments = $countStmt->fetchColumn();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => $comment_id,
                'avatar' => getAvatarPath($user['Avatar']),
                'name' => $user['TenDayDu'],
                'content' => htmlspecialchars($content),
                'time' => 'Vừa xong'
            ],
            'total_comments' => $total_comments
        ]);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nội dung không được để trống!']);
}
?>