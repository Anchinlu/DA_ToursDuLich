<?php
session_start();
require_once '../config/db_connect.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $db->prepare("SELECT * FROM NguoiDung WHERE TenDangNhap = :user OR GoogleID = :user"); // Cho phép nhập cả email google
        $stmt->execute([':user' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['MatKhau'])) {
             if ($user['TrangThai'] == 0) {
                $message = "<div class='alert alert-error'><i class='fas fa-exclamation-circle'></i> Tài khoản bị khóa!</div>";
             } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['TenDangNhap'];
                $_SESSION['fullname'] = $user['TenDayDu'];
                $_SESSION['role'] = $user['VaiTro'];
                $_SESSION['avatar'] = $user['Avatar'];
                // Chuyển hướng với đường dẫn tuyệt đối cho cả admin và user
                if ($user['VaiTro'] == 'admin') {
                    header("Location: /DoAn_TourDuLich/admin/index.php");
                } else {
                    header("Location: /DoAn_TourDuLich/index.php");
                }
                exit;
             }
        } else {
            $message = "<div class='alert alert-error'><i class='fas fa-exclamation-circle'></i> Tên đăng nhập hoặc mật khẩu sai!</div>";
        }
    } catch (Exception $e) { $message = "Lỗi: ".$e->getMessage(); }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Du Lịch Việt</title>
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
            <div style="margin-bottom: 30px;">
                <h3 style="color: var(--primary-color); font-weight: bold; margin-bottom: 10px;">
                    <i class="fas fa-plane-departure"></i> Du Lịch VN
                </h3>
                <h2>Chào mừng quay lại!</h2>
                <p class="subtitle">Đăng nhập để tiếp tục khám phá tour và ưu đãi.</p>
            </div>

            <?php echo $message; ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <i class="fas fa-envelope field-icon"></i>
                    <input type="text" id="username" name="username" placeholder=" " required>
                    <label for="username">Email hoặc Tên đăng nhập</label>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock field-icon"></i>
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Mật khẩu</label>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                </div>

                <a href="#" class="forgot-pass">Quên mật khẩu?</a>

                <button type="submit" class="btn-primary">
                    Đăng nhập
                </button>
            </form>

            <div class="divider"><span>Hoặc đăng nhập bằng</span></div>

            <a href="login_google.php" class="social-btn">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                Google
            </a>

            <p style="text-align: center; margin-top: 25px;">
                Bạn chưa có tài khoản? <a href="register.php" class="link-primary transition-link">Đăng ký ngay</a>
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