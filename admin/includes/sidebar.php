<?php
// Lấy tên file hiện tại trên thanh địa chỉ (ví dụ: index.php, users.php...)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-brand">LOCY <span>Admin</span></div>
    
    <ul class="sidebar-menu">
        
        <li>
            <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="users.php" class="<?php echo ($current_page == 'users.php' || $current_page == 'user_add.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Quản lý Người dùng
            </a>
        </li>

        <li>
            <a href="categories.php" class="<?php echo ($current_page == 'categories.php' || $current_page == 'category_add.php') ? 'active' : ''; ?>">
                <i class="fas fa-list"></i> Quản lý Danh mục
            </a>
        </li>

        <li>
            <a href="tours.php" class="<?php echo ($current_page == 'tours.php' || $current_page == 'tour_add.php') ? 'active' : ''; ?>">
                <i class="fas fa-map-marked-alt"></i> Quản lý Tour
            </a>
        </li>

        <li>
            <a href="support.php" class="<?php echo ($current_page == 'support.php') ? 'active' : ''; ?>">
                <i class="fas fa-headset"></i> Hỗ trợ & Báo cáo
            </a>
        </li>

        <li>
            <a href="orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng
            </a>
        </li>

        <li><a href="/DoAn_TourDuLich/index.php" target="_blank"><i class="fas fa-globe"></i> Xem Website</a></li>
        <li><a href="/DoAn_TourDuLich/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
    </ul>
</div>