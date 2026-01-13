<?php
session_start();
require_once '../config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $content = trim($_POST['content']);
    $image_url = null;

    // 1. XỬ LÝ UPLOAD ẢNH (NẾU CÓ)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Kiểm tra Cloudinary có được cấu hình không
        if (file_exists('../config/cloudinary_setup.php')) {
            require_once '../config/cloudinary_setup.php';
            
            try {
                // ĐÃ SỬA: Dùng tên đầy đủ (Fully Qualified Name) có dấu \ ở trước
                $upload = (new \Cloudinary\Api\Upload\UploadApi())->upload($_FILES['image']['tmp_name'], [
                    'folder' => 'chinliu_posts',
                    'resource_type' => 'image'
                ]);
                $image_url = $upload['secure_url'];
            } catch (Exception $e) {
                // Nếu lỗi Cloudinary, fallback về upload thường
                uploadLocal($image_url);
            }
        } else {
            // Upload thường nếu không có Cloudinary
            uploadLocal($image_url);
        }
    }

    // 2. LƯU VÀO DATABASE
    if (!empty($content) || !empty($image_url)) {
        try {
            $sql = "INSERT INTO BaiViet (idNguoiDung, NoiDung, HinhAnh, NgayDang, TrangThai) VALUES (?, ?, ?, NOW(), 1)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$user_id, $content, $image_url]);
            
            // Thành công -> Quay lại trang cộng đồng
            header("Location: ../pages/community.php");
            exit();
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    } else {
        header("Location: ../pages/community.php?error=empty");
    }
}

// Hàm hỗ trợ upload thường (tách ra cho gọn)
function uploadLocal(&$image_url) {
    $target_dir = "../uploads/posts/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_name = time() . '_' . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = "uploads/posts/" . $file_name;
    }
}
?>