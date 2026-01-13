<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

// Check đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='../auth/login.php';</script>";
    exit;
}

$uid = $_SESSION['user_id'];

// Lấy thông tin User
$stmt = $db->prepare("SELECT * FROM NguoiDung WHERE id = ?");
$stmt->execute([$uid]);
$user = $stmt->fetch();

// Lấy danh sách bài viết của user
$postStmt = $db->prepare("SELECT * FROM BaiViet WHERE idNguoiDung = ? ORDER BY NgayDang DESC");
$postStmt->execute([$uid]);
$myPosts = $postStmt->fetchAll();

// --- HÀM XỬ LÝ ẢNH (GIỐNG Y HỆT BÊN COMMUNITY) ---
function getAvatarUrl($path) {
    if (empty($path)) return 'assets/images/default-avatar.png'; // Trả về link tương đối
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return $path; // Giữ nguyên, không thêm /DoAn...
    return 'uploads/avatars/' . $path;
}
?>

<style>
    body { background-color: #f0f2f5; }
    /* THÊM ĐOẠN NÀY ĐỂ FIX FOOTER */
    /* 1. Đẩy nội dung chính dài ra để ép footer xuống đáy */
    .profile-container {
        min-height: 70vh; /* Chiếm tối thiểu 70% chiều cao màn hình */
        /* Giữ nguyên các thuộc tính cũ: */
        max-width: 960px; 
        margin: 30px auto; 
        padding: 0 15px; 
        display: flex; 
        gap: 20px;
    }
    
    /* CỘT TRÁI: MENU */
    .profile-sidebar { flex: 1; max-width: 300px; }
    .user-card { background: white; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); padding: 20px; text-align: center; margin-bottom: 20px; }
    .profile-avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .profile-name { margin-top: 10px; font-size: 18px; font-weight: 700; color: #050505; }
    .profile-email { font-size: 14px; color: #65676b; margin-bottom: 15px; }
    
    .sidebar-menu { background: white; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); overflow: hidden; }
    .menu-item { display: block; padding: 12px 20px; color: #050505; font-weight: 600; cursor: pointer; border-left: 3px solid transparent; transition: all 0.2s; }
    .menu-item:hover { background: #f2f2f2; }
    .menu-item.active { background: #e7f3ff; color: #1877f2; border-left-color: #1877f2; }
    .menu-item i { width: 25px; text-align: center; margin-right: 10px; }

    /* CỘT PHẢI: NỘI DUNG */
    .profile-content { flex: 2; }
    .tab-content { display: none; background: white; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); padding: 25px; animation: fadeIn 0.3s; }
    .tab-content.active { display: block; }
    .tab-header { font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }

    /* FORM STYLE */
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-weight: 600; margin-bottom: 5px; color: #333; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    .btn-save { background: #1877f2; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .btn-save:hover { background: #166fe5; }

    /* DANH SÁCH BÀI VIẾT NHỎ */
    .mini-post { display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #eee; }
    .mini-post:last-child { border-bottom: none; }
    .mini-post img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
    .mini-post-content { flex: 1; }
    .mini-post-date { font-size: 12px; color: #999; }
    .status-label { font-size: 11px; padding: 2px 6px; border-radius: 4px; background: #e4e6eb; color: #65676b; }
    .status-active { background: #e7f3ff; color: #1877f2; }

    footer, .footer {
        padding: 20px 0 !important;
        height: auto !important;
        min-height: auto !important;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="profile-container">
    
    <div class="profile-sidebar">
        <div class="user-card">
            <img src="<?php echo getAvatarUrl($user['Avatar']); ?>" class="profile-avatar" onerror="this.src='../assets/images/default-avatar.png'">
            <div class="profile-name"><?php echo htmlspecialchars($user['TenDayDu']); ?></div>
            <div class="profile-email"><?php echo htmlspecialchars($user['TenDangNhap']); ?></div>
            <a href="../auth/logout.php" style="color: #d32f2f; font-size: 14px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>

        <div class="sidebar-menu">
            <div class="menu-item active" onclick="switchTab('info')">
                <i class="fas fa-user"></i> Thông tin cá nhân
            </div>
            <div class="menu-item" onclick="switchTab('posts')">
                <i class="fas fa-pen-nib"></i> Bài viết của tôi
            </div>
            <div class="menu-item" onclick="switchTab('password')">
                <i class="fas fa-key"></i> Đổi mật khẩu
            </div>
            <div class="menu-item" onclick="switchTab('support')" style="color: #d93025;">
                <i class="fas fa-exclamation-circle"></i> Báo cáo sự cố
            </div>
        </div>
    </div>

    <div class="profile-content">
        
        <div id="tab-info" class="tab-content active">
            <div class="tab-header">Thông tin tài khoản</div>
            <form action="#" method="POST">
                <div class="form-group">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['TenDayDu']); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Email (Không thể thay đổi)</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['TenDangNhap']); ?>" disabled style="background:#eee;">
                </div>
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['SoDienThoai'] ?? ''); ?>" placeholder="Thêm số điện thoại...">
                </div>
                <button type="button" class="btn-save" onclick="alert('Tính năng cập nhật thông tin đang bảo trì!')">Lưu thay đổi</button>
            </form>
        </div>

        <div id="tab-posts" class="tab-content">
            <div class="tab-header">Bài viết đã đăng (<?php echo count($myPosts); ?>)</div>
            <?php if(count($myPosts) > 0): ?>
                <?php foreach($myPosts as $p): ?>
                <div class="mini-post">
                    <?php if(!empty($p['HinhAnh'])): ?>
                        <img src="<?php echo $p['HinhAnh']; ?>" loading="lazy">
                    <?php endif; ?>
                    <div class="mini-post-content">
                        <div style="font-weight:600; margin-bottom:5px;">
                            <?php echo substr(htmlspecialchars($p['NoiDung']), 0, 80) . '...'; ?>
                        </div>
                        <div class="mini-post-date">
                            <?php echo date('d/m/Y H:i', strtotime($p['NgayDang'])); ?> • 
                            <span class="status-label <?php echo ($p['TrangThai'] == 1) ? 'status-active' : ''; ?>">
                                <?php echo ($p['TrangThai'] == 1) ? 'Công khai' : 'Ẩn'; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <button style="border:none; background:none; color:#d32f2f; cursor:pointer;"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; color:#666;">Bạn chưa có bài viết nào.</p>
                <div style="text-align:center;"><a href="community.php" style="color:#1877f2;">Đến trang Cộng đồng</a></div>
            <?php endif; ?>
        </div>

        <div id="tab-password" class="tab-content">
            <div class="tab-header">Đổi mật khẩu</div>
            <form>
                <div class="form-group">
                    <label class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control">
                </div>
                <button type="button" class="btn-save" onclick="alert('Tính năng này đang phát triển!')">Cập nhật mật khẩu</button>
            </form>
        </div>

        <div id="tab-support" class="tab-content">
            <div class="tab-header" style="color:#d93025;"><i class="fas fa-bug"></i> Báo cáo sự cố / Góp ý</div>
            <p style="color:#666; font-size:14px; margin-bottom:20px;">
                Nếu bạn gặp lỗi khi đặt tour, lỗi hiển thị hoặc muốn đóng góp ý kiến, vui lòng điền vào mẫu bên dưới.
            </p>
            <form id="supportForm">
                <div class="form-group">
                    <label class="form-label">Tiêu đề sự cố <span style="color:red">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Ví dụ: Lỗi không đặt được vé..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Chi tiết nội dung <span style="color:red">*</span></label>
                    <textarea name="content" class="form-control" rows="5" placeholder="Mô tả chi tiết vấn đề bạn gặp phải..." required></textarea>
                </div>
                <button type="submit" class="btn-save" style="background:#d93025;">Gửi báo cáo</button>
            </form>
        </div>

    </div>
</div>

<script>
    // 1. CHUYỂN TAB
    function switchTab(tabName) {
        document.querySelectorAll('.menu-item').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));

        const menus = document.querySelectorAll('.menu-item');
        if(tabName == 'info') menus[0].classList.add('active');
        if(tabName == 'posts') menus[1].classList.add('active');
        if(tabName == 'password') menus[2].classList.add('active');
        if(tabName == 'support') menus[3].classList.add('active');

        document.getElementById('tab-' + tabName).classList.add('active');
    }

    // 2. XỬ LÝ GỬI BÁO CÁO (AJAX)
    document.getElementById('supportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        const originalText = btn.innerText;
        btn.innerText = 'Đang gửi...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('/DoAn_TourDuLich/process/support_process.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') {
                this.reset();
            }
        })
        .catch(err => alert('Lỗi hệ thống!'))
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    });
</script>

<?php include '../includes/footer.php'; ?>