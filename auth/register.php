<?php
session_start();
require_once '../config/db_connect.php';

// Tích hợp Cloudinary
if (file_exists('../config/cloudinary_setup.php')) {
    require_once '../config/cloudinary_setup.php';
}
use Cloudinary\Api\Upload\UploadApi;

$message = '';

// XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG KÝ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']); // Lấy Email (sẽ lưu vào cột TenDangNhap)
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        $message = "<div class='alert alert-error'><i class='fas fa-exclamation-triangle'></i> Mật khẩu xác nhận không khớp!</div>";
    } else {
        $avatar_path = '';
        
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            try {
                $upload = (new UploadApi())->upload($_FILES['avatar']['tmp_name'], [
                    'folder' => 'chinliu_avatars', 
                    'resource_type' => 'image'
                ]);
                $avatar_path = $upload['secure_url']; 
            } catch (Exception $e) {
            }
        }
        // -------------------------------------------

        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Lưu Email vào cột TenDangNhap
            $sql = "INSERT INTO NguoiDung (TenDangNhap, MatKhau, TenDayDu, Avatar, VaiTro, TrangThai) 
                    VALUES (:email, :pass, :name, :avatar, 'user', 1)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':email'  => $email, 
                ':pass'   => $hashed_password, 
                ':name'   => $fullname, 
                ':avatar' => $avatar_path
            ]);
            
            $message = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Đăng ký thành công! <a href='login.php' class='link-primary transition-link'>Đăng nhập ngay</a></div>";

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $message = "<div class='alert alert-error'><i class='fas fa-times-circle'></i> Email này đã được sử dụng!</div>";
            } else {
                $message = "<div class='alert alert-error'>Lỗi hệ thống: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Chinliu Tour</title>
    <link rel="stylesheet" href="/DoAn_TourDuLich/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="auth-container">
    <div class="hero-section">
        <div class="hero-overlay">
            <h1 style="font-size: 42px; line-height: 1.2; margin-bottom: 15px;">Khám phá<br>Việt Nam</h1>
            <p style="font-size: 16px; opacity: 0.9; margin-bottom: 10px;">
                Trải nghiệm những hành trình tuyệt vời với tour du lịch chất lượng cao
            </p>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                    <div class="stat-text">
                        <strong>200+ Tour</strong>
                        <span>Điểm đến</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                    <div class="stat-text">
                        <strong>4.8/5.0</strong>
                        <span>Đánh giá</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-wrapper">
            <div style="margin-bottom: 15px; text-align: center;">
                <h2 style="font-size: 24px;">Tạo tài khoản</h2>
                <p class="subtitle" style="margin-bottom: 10px; font-size: 14px;">Điền thông tin để bắt đầu hành trình</p>
            </div>

            <?php echo $message; ?>

            <form action="register.php" method="POST" enctype="multipart/form-data">
                
                <div class="row-grid">
                    <div class="input-group">
                        <i class="fas fa-user field-icon"></i>
                        <input type="text" id="fullname" name="fullname" placeholder=" " required>
                        <label for="fullname">Họ và tên</label>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-envelope field-icon"></i>
                        <input type="email" id="email" name="email" placeholder=" " required>
                        <label for="email">Địa chỉ Email</label>
                    </div>
                </div>

                <div class="row-grid">
                    <div class="input-group">
                        <i class="fas fa-lock field-icon"></i>
                        <input type="password" id="reg_password" name="password" placeholder=" " required>
                        <label for="reg_password">Mật khẩu</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-check-circle field-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder=" " required>
                        <label for="confirm_password">Nhập lại MK</label>
                    </div>
                </div>

                <div class="avatar-compact">
                    <i class="fas fa-camera" style="color: var(--text-light);"></i>
                    <span style="color: var(--text-gray); white-space: nowrap;">Ảnh đại diện (Tùy chọn):</span>
                    <input type="file" name="avatar" accept="image/*" style="font-size: 12px;">
                </div>

                <div style="margin-bottom: 15px; font-size: 13px;">
                    <input type="checkbox" id="terms" required>
                    <label for="terms" style="color: var(--text-gray);">
                        Đồng ý với <a href="#" class="link-primary">Điều khoản</a> & <a href="#" class="link-primary">Chính sách</a>.
                    </label>
                </div>

                <button type="submit" class="btn-primary" style="padding: 12px;">Đăng Ký Ngay</button>
            </form>

            <div class="divider" style="margin: 15px 0;"><span>Hoặc</span></div>
            
            <a href="login_google.php" class="social-btn" style="padding: 10px;">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                Đăng ký bằng Google
            </a>

            <p style="text-align: center; margin-top: 15px; font-size: 14px;">
                Đã có tài khoản? <a href="login.php" class="link-primary transition-link">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>

<div id="wiper-container">
    <div class="center-logo">CHINLIU</div>
    <div class="wipe-layer wipe-light"></div>
    <div class="wipe-layer wipe-dark"></div>
</div>

<script src="/DoAn_TourDuLich/assets/js/script.js"></script>
</body>
</html>