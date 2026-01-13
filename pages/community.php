<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

// --- HÀM XỬ LÝ ẢNH ĐẠI DIỆN ---
function getAvatarUrl($path) {
    if (empty($path)) return '/DoAn_TourDuLich/assets/images/default-avatar.png';
    if (strpos($path, 'http') === 0) return $path;
    return '/DoAn_TourDuLich/uploads/avatars/' . $path;
}

// LẤY DỮ LIỆU BÀI VIẾT
$sql = "SELECT b.*, u.TenDayDu, u.Avatar, 
        (SELECT COUNT(*) FROM LuotThich WHERE idBaiViet = b.id) as TotalLikes,
        (SELECT COUNT(*) FROM BinhLuanBaiViet WHERE idBaiViet = b.id) as TotalComments
        FROM BaiViet b 
        JOIN NguoiDung u ON b.idNguoiDung = u.id 
        WHERE b.TrangThai = 1
        ORDER BY b.NgayDang DESC";
$posts = $db->query($sql)->fetchAll();

// Lấy Avatar người dùng hiện tại
$myAvatar = '/DoAn_TourDuLich/assets/images/default-avatar.png';
if(isset($_SESSION['user_id'])){
     $stmtU = $db->prepare("SELECT Avatar FROM NguoiDung WHERE id = ?");
     $stmtU->execute([$_SESSION['user_id']]);
     $path = $stmtU->fetchColumn();
     $myAvatar = getAvatarUrl($path);
}
?>

<style>
    /* 1. CẤU TRÚC CHUNG */
    body { background-color: #F0F2F5; font-family: system-ui, sans-serif; color: #050505; }
    .community-container { max-width: 680px; margin: 20px auto; padding: 0 15px; }
    
    /* 2. HEADER ĐĂNG BÀI */
    .create-post-card { background: white; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
    .my-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd; }
    .fake-input { background: #F0F2F5; border-radius: 20px; padding: 10px 15px; flex: 1; color: #65676B; font-size: 15px; cursor: pointer; transition: background 0.2s; }
    .fake-input:hover { background: #E4E6E9; }

    /* 3. MODAL (POPUP) */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(244, 244, 244, 0.85); z-index: 1000; display: none; align-items: center; justify-content: center;
    }
    .modal-box {
        background: white; width: 500px; max-width: 95%; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; flex-direction: column; animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    .modal-header { padding: 15px; border-bottom: 1px solid #e5e5e5; position: relative; text-align: center; font-weight: 700; font-size: 20px; }
    .btn-close-modal { position: absolute; right: 15px; top: 12px; width: 36px; height: 36px; border-radius: 50%; background: #E4E6E9; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 20px; color: #606770; }
    .btn-close-modal:hover { background: #d8dadf; }

    .modal-body { padding: 15px; max-height: 400px; overflow-y: auto; }
    .user-profile-small { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
    .user-name-small { font-weight: 600; font-size: 15px; }
    
    .status-input { width: 100%; border: none; outline: none; font-size: 24px; font-family: inherit; min-height: 100px; resize: none; margin-bottom: 15px; }
    .image-preview-area { border: 1px solid #ddd; border-radius: 8px; padding: 8px; position: relative; display: none; margin-bottom: 10px; }
    .preview-img { width: 100%; max-height: 250px; object-fit: cover; border-radius: 4px; }
    .btn-remove-img { position: absolute; top: 10px; right: 10px; background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }

    .add-to-post { border: 1px solid #ddd; border-radius: 8px; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .tool-icon { font-size: 24px; cursor: pointer; color: #45BD62; }
    .btn-submit-post { width: 100%; background: #0866FF; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 600; font-size: 15px; cursor: pointer; transition: background 0.2s; }
    .btn-submit-post:disabled { background: #E4E6E9; color: #BCC0C4; cursor: not-allowed; }

    /* 4. POST CARD HIỂN THỊ */
    .post-card { background: white; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); margin-bottom: 15px; overflow: hidden; }
    .post-header { padding: 12px 16px; display: flex; align-items: center; gap: 10px; }
    .post-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd; }
    .user-info h4 { margin: 0; font-size: 15px; font-weight: 600; }
    .post-time { font-size: 13px; color: #65676B; display: flex; align-items: center; gap: 4px; }
    
    .post-body { padding: 4px 16px 16px; }
    .post-content { font-size: 15px; line-height: 1.5; margin: 0; }
    .post-img { width: 100%; display: block; object-fit: cover; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; max-height: 700px; }

    .post-stats { padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dfe0e4; color: #65676B; font-size: 14px; }
    .stat-icon { background: #0866FF; color: white; border-radius: 50%; padding: 4px; font-size: 10px; margin-right: 4px; }

    .post-actions { padding: 4px 16px; display: flex; gap: 4px; justify-content: space-between; }
    .action-btn { flex: 1; background: none; border: none; color: #65676B; font-weight: 600; font-size: 14px; padding: 8px 0; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.2s; }
    .action-btn:hover { background-color: #F0F2F5; }
    .action-btn i { font-size: 18px; }
    .action-btn.active { color: #0866FF; } 
    .action-btn.active i { color: #0866FF; }

    /* 5. COMMENT SECTION */
    .comment-section { padding: 8px 16px 16px; background-color: #fff; display: none; }
    .comment-input-area { display: flex; gap: 8px; margin-bottom: 12px; position: relative; }
    .comment-avatar-small { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
    .input-wrapper { flex: 1; position: relative; }
    .comment-input { width: 100%; background: #F0F2F5; border: none; padding: 10px 40px 10px 12px; border-radius: 18px; font-size: 14px; outline: none; }
    .btn-send-comment { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; color: #0866FF; cursor: pointer; }
    
    .comment-item { display: flex; gap: 8px; margin-top: 8px; }
    .comment-bubble { background: #F0F2F5; padding: 8px 12px; border-radius: 18px; display: inline-block; }
    .comment-author { font-weight: 600; font-size: 13px; color: #050505; }
    .comment-text { font-size: 14px; color: #050505; margin-top: 2px; }
    .comment-meta { font-size: 12px; color: #65676B; margin-left: 12px; margin-top: 2px; }
</style>

<div class="community-container">
    
    <div class="create-post-card">
        <img src="<?php echo $myAvatar; ?>" class="my-avatar">
        <div class="fake-input" onclick="openModal()">
            <?php echo isset($_SESSION['user_name']) ? "Chào " . $_SESSION['user_name'] . ", bạn đang nghĩ gì?" : "Bạn đang nghĩ gì thế?"; ?>
        </div>
    </div>

    <?php foreach($posts as $post): ?>
        <?php
            // --- LOGIC PHP QUAN TRỌNG ĐỂ LIKE/COMMENT HOẠT ĐỘNG ---
            $isLiked = false;
            if(isset($_SESSION['user_id'])) {
                $checkLike = $db->prepare("SELECT id FROM LuotThich WHERE idBaiViet = ? AND idNguoiDung = ?");
                $checkLike->execute([$post['id'], $_SESSION['user_id']]);
                if($checkLike->rowCount() > 0) $isLiked = true;
            }
            $totalLikes = $post['TotalLikes'];

            // Lấy 5 comment mới nhất
            $cmtStmt = $db->prepare("SELECT c.*, u.TenDayDu, u.Avatar FROM BinhLuanBaiViet c JOIN NguoiDung u ON c.idNguoiDung = u.id WHERE c.idBaiViet = ? ORDER BY c.NgayBinhLuan DESC LIMIT 5");
            $cmtStmt->execute([$post['id']]);
            $comments = $cmtStmt->fetchAll();
        ?>

        <div class="post-card">
            <div class="post-header">
                <img src="<?php echo getAvatarUrl($post['Avatar']); ?>" class="post-avatar">
                <div class="user-info">
                    <h4><?php echo htmlspecialchars($post['TenDayDu']); ?></h4>
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
                    <?php echo ($post['TotalComments'] > 0) ? $post['TotalComments'] . ' bình luận' : ''; ?>
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
                    <p style="text-align:center; font-size:13px; padding:10px;">
                        <a href="../auth/login.php" style="color:#0866FF; font-weight:bold;">Đăng nhập</a> để bình luận.
                    </p>
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
                            <div class="comment-meta"><?php echo date('H:i d/m', strtotime($cmt['NgayBinhLuan'])); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<div class="modal-overlay" id="createPostModal">
    <div class="modal-box">
        <div class="modal-header">
            Tạo bài viết
            <div class="btn-close-modal" onclick="closeModal()"><i class="fas fa-times"></i></div>
        </div>
        
        <form action="../process/post_create_process.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="user-profile-small">
                    <img src="<?php echo $myAvatar; ?>" class="my-avatar">
                    <div class="user-name-small"><?php echo $_SESSION['user_name'] ?? 'Khách'; ?></div>
                </div>

                <textarea name="content" class="status-input" id="statusInput" placeholder="Bạn đang nghĩ gì thế?" oninput="checkInput()"></textarea>

                <div class="image-preview-area" id="imagePreviewArea">
                    <img id="previewImg" class="preview-img">
                    <div class="btn-remove-img" onclick="removeImage()"><i class="fas fa-times"></i></div>
                </div>

                <div class="add-to-post">
                    <span style="font-weight:600; font-size:14px;">Thêm vào bài viết</span>
                    <label for="fileInput" class="tool-icon" title="Ảnh/Video"><i class="fas fa-images"></i></label>
                    <input type="file" name="image" id="fileInput" accept="image/*" style="display:none" onchange="previewImage(this)">
                </div>

                <button type="submit" class="btn-submit-post" id="btnSubmit" disabled>Đăng</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- JS CHO MODAL ---
    const modal = document.getElementById('createPostModal');
    const btnSubmit = document.getElementById('btnSubmit');
    const statusInput = document.getElementById('statusInput');
    
    function openModal() {
        <?php if(!isset($_SESSION['user_id'])): ?>
            alert('Vui lòng đăng nhập để đăng bài!');
            window.location.href = '../auth/login.php';
            return;
        <?php endif; ?>
        modal.style.display = 'flex';
        statusInput.focus();
    }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(event) { if (event.target == modal) closeModal(); }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreviewArea').style.display = 'block';
                checkInput();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    function removeImage() {
        document.getElementById('fileInput').value = "";
        document.getElementById('imagePreviewArea').style.display = 'none';
        checkInput();
    }
    function checkInput() {
        const hasText = statusInput.value.trim().length > 0;
        const hasImage = document.getElementById('fileInput').files.length > 0;
        btnSubmit.disabled = !(hasText || hasImage);
    }
    function sharePost(id) {
        const url = window.location.href.split('?')[0] + '?post_id=' + id;
        navigator.clipboard.writeText(url);
        alert('Đã sao chép liên kết!');
    }
</script>

<script src="/DoAn_TourDuLich/assets/js/like_ajax.js"></script>
<script src="/DoAn_TourDuLich/assets/js/comment_ajax.js"></script>

<?php include '../includes/footer.php'; ?>