<?php
session_start();
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập!']);
    exit;
}

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if (empty($title) || empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin!']);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO HoTro (idNguoiDung, TieuDe, NoiDung) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $content]);

    echo json_encode(['status' => 'success', 'message' => 'Gửi yêu cầu hỗ trợ thành công! Chúng tôi sẽ liên hệ sớm nhất.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>