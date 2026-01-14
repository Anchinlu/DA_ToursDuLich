<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$userName = 'Khách';
// Thêm biến kiểm tra Admin
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

function getHeaderAvatar($path) {
    // 1. Ảnh Online
    if (strpos($path, 'http') === 0) {
        return $path;
    }
    // 2. Không có ảnh -> Ảnh mặc định
    if (empty($path)) {
        return 'https://ui-avatars.com/api/?name=User&background=random&size=150';
    }
    // 3. Xử lý đường dẫn
    $cleanPath = ltrim($path, '/');
    if (strpos($cleanPath, 'uploads/') === 0) {
        return '/DoAn_TourDuLich/' . $cleanPath;
    }
    return '/DoAn_TourDuLich/uploads/avatars/' . $cleanPath;
}

$avatarUrl = 'https://ui-avatars.com/api/?name=Khach&background=random'; 
if ($isLoggedIn) {
    $userName = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Thành viên';
    $rawAvatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : '';
    
    if (empty($rawAvatar)) {
        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=random';
    } else {
        $avatarUrl = getHeaderAvatar($rawAvatar);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chinliu Tour - Trải Nghiệm Khác Biệt</title>
    <meta name="referrer" content="no-referrer"> 
    
    <link rel="stylesheet" href="/DoAn_TourDuLich/assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .user-dropdown { 
            position: relative; display: inline-flex; align-items: center; cursor: pointer; padding-left: 15px; height: 100%; user-select: none; 
        }
        .user-profile { display: flex; align-items: center; gap: 8px; }
        .avatar-circle { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; transition: 0.3s; }
        .user-name-text { font-weight: 600; font-size: 14px; color: #333; max-width: 100px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .user-dropdown.active .avatar-circle { border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.2); }
        .user-dropdown.active .user-name-text { color: var(--primary-color); }
        .user-dropdown.active .fa-caret-down { transform: rotate(180deg); transition: 0.3s; }

        .dropdown-content { display: none; position: absolute; right: 0; top: 130%; background-color: white; min-width: 220px; box-shadow: 0px 5px 20px rgba(0,0,0,0.15); border-radius: 8px; z-index: 9999; padding: 8px 0; border: 1px solid #eee; }
        .dropdown-content.show { display: block; animation: pullDown 0.2s ease-out forwards; }

        .dropdown-content a { color: #444; padding: 10px 20px; text-decoration: none; display: flex; align-items: center; font-size: 14px; transition: all 0.2s; }
        .dropdown-content a:hover { background-color: #f0fdf4; color: var(--primary-color); padding-left: 25px; }
        .dropdown-content i { width: 25px; text-align: center; margin-right: 5px; color: #888; }
        
        @keyframes pullDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container flex-between">
        <div class="contact-info">
            <span><i class="fas fa-map-marker-alt"></i> Q.Tân Bình, TP.HCM</span>
            <span><i class="fas fa-envelope"></i> contact@chinliu.com</span>
        </div>
        <div class="social-icons" style="display:flex; gap:15px;">
            <span style="margin-right:10px; font-weight:600; font-size:12px;">HOTLINE: 1900 1234</span>
            <a href="#" style="color:white;"><i class="fab fa-facebook-f"></i></a>
            <a href="#" style="color:white;"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</div>

<nav class="navbar">
    <div class="container flex-between">
        <a href="/DoAn_TourDuLich/index.php" class="logo">Chinliu <span>Tour</span></a>

        <ul class="nav-menu">
            <li><a href="/DoAn_TourDuLich/index.php">Trang chủ</a></li>
            <li><a href="/DoAn_TourDuLich/pages/about.php">Giới thiệu</a></li>
            <li><a href="/DoAn_TourDuLich/pages/search.php">Tours</a></li>
            <li><a href="/DoAn_TourDuLich/pages/community.php">Cộng Đồng</a></li>
        </ul>

        <div class="nav-icons">
            <a href="/DoAn_TourDuLich/pages/search.php" style="color: inherit;">
            </a>
            
            <?php if ($isLoggedIn): ?>
                <div class="user-dropdown" id="userTrigger" onclick="toggleMenu(event)">
                    <div class="user-profile">
                            <img src="<?php echo $avatarUrl; ?>" class="avatar-circle" 
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=random';">
                        <span class="user-name-text"><?php echo htmlspecialchars($userName); ?></span>
                        <i class="fas fa-caret-down" style="font-size:12px; color:#666; margin-left:3px; transition:0.3s;"></i>
                    </div>
                    
                    <div class="dropdown-content" id="userDropdownMenu">
                        <?php if($isAdmin): ?>
                            <a href="/DoAn_TourDuLich/admin/index.php" style="color: #d32f2f; font-weight: bold; background-color: #fff5f5;">
                                <i class="fas fa-user-shield"></i> Trang Quản Trị
                            </a>
                            <hr style="margin: 5px 0; border: 0; border-top: 1px solid #eee;">
                        <?php endif; ?>

                        <a href="/DoAn_TourDuLich/pages/profile.php"><i class="fas fa-user-circle"></i> Hồ sơ cá nhân</a>
                        <a href="/DoAn_TourDuLich/pages/history.php"><i class="fas fa-history"></i> Lịch sử đặt tour</a>
                        <a href="/DoAn_TourDuLich/pages/profile.php#posts"><i class="fas fa-pen-nib"></i> Bài viết của tôi</a>
                        <a href="/DoAn_TourDuLich/auth/logout.php" style="color:#d32f2f;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/DoAn_TourDuLich/auth/login.php" class="btn-primary" style="padding: 8px 20px; font-size: 13px; border-radius:20px;">ĐĂNG NHẬP</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
function toggleMenu(event) {
    event.stopPropagation();
    
    var menu = document.getElementById("userDropdownMenu");
    var trigger = document.getElementById("userTrigger");

    menu.classList.toggle("show");
    trigger.classList.toggle("active");
}
window.onclick = function(event) {
    var menu = document.getElementById("userDropdownMenu");
    var trigger = document.getElementById("userTrigger");
    
    if (menu.classList.contains('show')) {
        if (!event.target.closest('#userDropdownMenu') && !event.target.closest('#userTrigger')) {
            menu.classList.remove('show');
            trigger.classList.remove('active');
        }
    }
}
</script>