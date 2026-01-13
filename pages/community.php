<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

// --- HÀM XỬ LÝ (GIỮ NGUYÊN) ---
function getAvatarUrl($path) {
    if (empty($path)) return '/DoAn_TourDuLich/assets/images/default-avatar.png';
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return '/DoAn_TourDuLich/' . $path;
    return '/DoAn_TourDuLich/uploads/avatars/' . $path;
}

// LẤY DỮ LIỆU (GIỮ NGUYÊN)
$sql = "SELECT b.*, u.TenDayDu, u.Avatar, 
        (SELECT COUNT(*) FROM LuotThich WHERE idBaiViet = b.id) as TotalLikes,
        (SELECT COUNT(*) FROM BinhLuanBaiViet WHERE idBaiViet = b.id) as TotalComments
        FROM BaiViet b 
        JOIN NguoiDung u ON b.idNguoiDung = u.id 
        WHERE b.TrangThai = 1
        ORDER BY b.NgayDang DESC";
$posts = $db->query($sql)->fetchAll();
?>

<style>
    /* 1. FONT CHỮ CHUẨN FACEBOOK */
    body {
        background-color: #F0F2F5; /* Màu nền xám chuẩn FB */
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        color: #050505; /* Màu chữ đen chuẩn FB */
        -webkit-font-smoothing: antialiased;
    }
    
    .community-container {
        max-width: 680px;
        margin: 20px auto;
        padding: 0 15px;
    }

    /* 2. HEADER: KIỂU "BẠN ĐANG NGHĨ GÌ?" */
    .create-post-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        padding: 12px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .my-avatar {
        width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;
    }
    .fake-input {
        background: #F0F2F5;
        border-radius: 20px;
        padding: 10px 15px;
        flex: 1;
        color: #65676B;
        font-size: 15px;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        display: block;
    }
    .fake-input:hover { background: #E4E6E9; }

    /* 3. POST CARD */
    .post-card { 
        background: white; 
        border-radius: 8px; 
        box-shadow: 0 1px 2px rgba(0,0,0,0.1); 
        margin-bottom: 15px; 
        overflow: hidden;
    }
    
    /* Header Bài viết */
    .post-header { padding: 12px 16px; display: flex; align-items: center; gap: 10px; }
    .post-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd; }
    
    .user-info h4 {
        margin: 0; font-size: 15px; font-weight: 600; line-height: 1.2;
    }
    .user-info h4 a { color: #050505; text-decoration: none; }
    .user-info h4 a:hover { text-decoration: underline; }
    
    .post-time { font-size: 13px; color: #65676B; margin-top: 2px; display: flex; align-items: center; gap: 4px; }

    /* Nội dung Text */
    .post-body { padding: 4px 16px 16px 16px; }
    .post-content { color: #050505; font-size: 15px; line-height: 1.5; margin: 0; font-weight: 400; }

    /* Ảnh bài viết */
    .post-img { 
        width: 100%; display: block; object-fit: cover;
        border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;
        max-height: 700px; 
    }

    /* Thống kê (Like/Cmt) */
    .post-stats {
        padding: 10px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dfe0e4;
        color: #65676B;
        font-size: 14px;
    }
    .stat-icon { background: #0866FF; color: white; border-radius: 50%; padding: 4px; font-size: 10px; margin-right: 4px; }

    /* Nút hành động (Like/Comment/Share) */
    .post-actions { 
        padding: 4px 16px; 
        display: flex; 
        gap: 4px;
        justify-content: space-between; 
    }
    .action-btn { 
        flex: 1; 
        background: none; 
        border: none; 
        color: #65676B; 
        font-weight: 600; 
        font-size: 14px;
        padding: 8px 0;
        border-radius: 4px;
        cursor: pointer; 
        display: flex; align-items: center; justify-content: center; gap: 8px; 
        transition: background 0.2s;
    }
    .action-btn:hover { background-color: #F0F2F5; }
    .action-btn i { font-size: 18px; } /* Icon to hơn chút */

    /* Active State (Like) */
    .action-btn.active { color: #0866FF; } /* Màu xanh FB hiện đại */
    .action-btn.active i { color: #0866FF; }
    /* Hoặc dùng màu đỏ truyền thống nếu thích: color: #e41e3f */

    /* 4. KHUNG BÌNH LUẬN (Chat Bubble Style) */
    .comment-section {
        padding: 8px 16px 16px;
        background-color: #fff;
        display: none; /* Ẩn mặc định */
    }
    .comment-input-area { display: flex; gap: 8px; margin-bottom: 12px; position: relative; }
    .comment-avatar-small { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
    
    .input-wrapper { flex: 1; position: relative; }
    .comment-input {
        width: 100%;
        background: #F0F2F5;
        border: none;
        padding: 10px 40px 10px 12px;
        border-radius: 18px; /* Bo tròn kiểu Chat */
        font-size: 14px;
        outline: none;
        color: #050505;
    }
    .comment-input::placeholder { color: #65676B; }
    
    /* Nút gửi trong input */
    .btn-send-comment {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        border: none; background: none; color: #0866FF; cursor: pointer;
        transition: transform 0.2s;
    }
    .btn-send-comment:hover { transform: translateY(-50%) scale(1.1); }

    /* Danh sách comment */
    .comment-item { display: flex; gap: 8px; margin-top: 8px; animation: fadeIn 0.3s; }
    .comment-bubble { 
        background: #F0F2F5; 
        padding: 8px 12px; 
        border-radius: 18px; 
        display: inline-block;
        position: relative;
    }
    .comment-author { font-weight: 600; font-size: 13px; color: #050505; }
    .comment-text { font-size: 14px; color: #050505; margin-top: 2px; }
    .comment-meta { font-size: 12px; color: #65676B; margin-left: 12px; margin-top: 2px; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
<div class="community-container">
    
    <?php 
        $myAvatar = isset($_SESSION['user_id']) ? getAvatarUrl($_SESSION['user_avatar'] ?? '') : '/DoAn_TourDuLich/assets/images/default-avatar.png';
        if(isset($_SESSION['user_id'])){
             $stmtU = $db->prepare("SELECT Avatar FROM NguoiDung WHERE id = ?");
             $stmtU->execute([$_SESSION['user_id']]);
             $myAvatar = getAvatarUrl($stmtU->fetchColumn());
        }
    ?>
    <div class="create-post-card">
        <img src="<?php echo $myAvatar; ?>" class="my-avatar">
        <div class="fake-input" style="cursor: pointer;">
            <?php echo isset($_SESSION['user_name']) ? "Chào " . $_SESSION['user_name'] . ", bạn đang nghĩ gì?" : "Bạn đang nghĩ gì thế?"; ?>
        </div>
    </div>

    <?php foreach($posts as $post): ?>
        <?php
            // Logic Like & Comment Count
            $isLiked = false;
            if(isset($_SESSION['user_id'])) {
                $checkLike = $db->prepare("SELECT id FROM LuotThich WHERE idBaiViet = ? AND idNguoiDung = ?");
                $checkLike->execute([$post['id'], $_SESSION['user_id']]);
                if($checkLike->rowCount() > 0) $isLiked = true;
            }
            $countLike = $db->prepare("SELECT COUNT(*) FROM LuotThich WHERE idBaiViet = ?");
            $countLike->execute([$post['id']]);
            $totalLikes = $countLike->fetchColumn();

            // Logic lấy Comment cũ
            $cmtStmt = $db->prepare("SELECT c.*, u.TenDayDu, u.Avatar FROM BinhLuanBaiViet c JOIN NguoiDung u ON c.idNguoiDung = u.id WHERE c.idBaiViet = ? ORDER BY c.NgayBinhLuan DESC LIMIT 5");
            $cmtStmt->execute([$post['id']]);
            $comments = $cmtStmt->fetchAll();
        ?>

        <div class="post-card">
            <div class="post-header">
                <img src="<?php echo getAvatarUrl($post['Avatar']); ?>" class="post-avatar" onerror="this.src='/DoAn_TourDuLich/assets/images/default-avatar.png'">
                <div class="user-info">
                    <h4><a href="#"><?php echo htmlspecialchars($post['TenDayDu']); ?></a></h4>
                    <div class="post-time">
                        <?php echo date('d/m \l\ú\c H:i', strtotime($post['NgayDang'])); ?> 
                        <i class="fas fa-globe-asia" style="font-size:12px;"></i>
                    </div>
                </div>
            </div>

            <div class="post-body">
                <p class="post-content"><?php echo nl2br(htmlspecialchars($post['NoiDung'])); ?></p>
            </div>
            <?php if(!empty($post['HinhAnh'])): ?>
                <img src="<?php echo $post['HinhAnh']; ?>" class="post-img" loading="lazy">
            <?php endif; ?>

            <div class="post-stats">
                <div class="like-info">
                    <?php if($totalLikes > 0): ?>
                        <i class="fas fa-thumbs-up stat-icon"></i> <?php echo $totalLikes; ?>
                    <?php endif; ?>
                </div>
                <div class="comment-info">
                    <span class="comment-count-display">
                        <?php echo ($post['TotalComments'] > 0) ? $post['TotalComments'] . ' bình luận' : ''; ?>
                    </span>
                </div>
            </div>

            <div class="post-actions">
                <button class="action-btn btn-like-action <?php echo $isLiked ? 'active' : ''; ?>" data-id="<?php echo $post['id']; ?>">
                    <i class="<?php echo $isLiked ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                    <span>Thích</span>
                    <span class="like-count" style="display:none;"><?php echo $totalLikes; ?></span> 
                </button>
                
                <button class="action-btn btn-toggle-comment">
                    <i class="far fa-comment-alt"></i>
                    <span>Bình luận</span>
                </button>
                
                <button class="action-btn" onclick="sharePost(<?php echo $post['id']; ?>)">
                    <i class="fas fa-share"></i>
                    <span>Chia sẻ</span>
                </button>
            </div>

            <div class="comment-section">
                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="comment-input-area">
                    <img src="<?php echo $myAvatar; ?>" class="comment-avatar-small">
                    <div class="input-wrapper">
                        <input type="text" class="comment-input" placeholder="Viết bình luận công khai...">
                        <button class="btn-send-comment" data-id="<?php echo $post['id']; ?>">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                <?php else: ?>
                    <p style="text-align:center; font-size:13px;"><a href="/DoAn_TourDuLich/auth/login.php" style="font-weight:bold; color:#0866FF;">Đăng nhập</a> để bình luận.</p>
                <?php endif; ?>

                <div class="comment-list">
                    <?php foreach($comments as $cmt): ?>
                    <div class="comment-item">
                        <img src="<?php echo getAvatarUrl($cmt['Avatar']); ?>" class="comment-avatar-small">
                        <div>
                            <div class="comment-bubble">
                                <div class="comment-author"><?php echo htmlspecialchars($cmt['TenDayDu']); ?></div>
                                <div class="comment-text"><?php echo htmlspecialchars($cmt['NoiDung']); ?></div>
                            </div>
                            <div class="comment-meta"><?php echo date('H:i', strtotime($cmt['NgayBinhLuan'])); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<script>
function sharePost(id) {
    const url = window.location.href.split('?')[0] + '?post_id=' + id;
    navigator.clipboard.writeText(url);
    alert('Đã sao chép liên kết!');
}
</script>

<script src="/DoAn_TourDuLich/assets/js/like_ajax.js"></script>
<script src="/DoAn_TourDuLich/assets/js/comment_ajax.js"></script>

<?php include '../includes/footer.php'; ?>