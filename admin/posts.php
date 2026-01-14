<?php include 'includes/header.php'; 

// 1. XỬ LÝ XÓA BÀI VIẾT
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM BaiViet WHERE id = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Đã xóa bài viết!'); window.location='posts.php';</script>";
}

// 2. LẤY DANH SÁCH BÀI VIẾT (Kèm thông tin người dùng)
$sql = "SELECT b.*, u.TenDayDu, u.Avatar as UserAvatar 
        FROM BaiViet b 
        JOIN NguoiDung u ON b.idNguoiDung = u.id 
        ORDER BY b.NgayDang DESC";
$posts = $db->query($sql)->fetchAll();
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2><i class="fas fa-comments"></i> Quản lý Bài viết Cộng đồng</h2>
        <span class="badge" style="background:#0866FF; color:white; padding:5px 10px; border-radius:4px;">
            Tổng: <?php echo count($posts); ?> bài
        </span>
    </div>

    <table class="table table-bordered"> <thead>
            <tr style="background:#f8f9fa;">
                <th width="50">ID</th>
                <th width="200">Người đăng</th>
                <th>Nội dung & Hình ảnh</th>
                <th width="150">Ngày đăng</th>
                <th width="100" text-align="center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($posts as $p): ?>
            <tr>
                <td style="text-align:center; font-weight:bold;">#<?php echo $p['id']; ?></td>
                
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <?php 
                            $avatar = $p['UserAvatar'] ? $p['UserAvatar'] : 'assets/images/default-avatar.png';
                            if (!empty($p['UserAvatar']) && strpos($p['UserAvatar'], 'http') !== 0) {
                                $avatar = '/DoAn_TourDuLich/uploads/avatars/' . $p['UserAvatar'];
                            }
                        ?>
                        <img src="<?php echo $avatar; ?>" style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                        <span style="font-weight:600;"><?php echo htmlspecialchars($p['TenDayDu']); ?></span>
                    </div>
                </td>

                <td>
                    <p style="margin:0 0 5px 0; color:#333;">
                        <?php echo nl2br(htmlspecialchars(substr($p['NoiDung'], 0, 100))); ?>
                        <?php echo strlen($p['NoiDung']) > 100 ? '...' : ''; ?>
                    </p>
                    
                    <?php if(!empty($p['HinhAnh'])): ?>
                        <?php 
                            $postImg = $p['HinhAnh'];
                            // Nếu không phải link http (tức là ảnh local), thì nối thêm đường dẫn project
                            if (strpos($postImg, 'http') !== 0) {
                                $postImg = '/DoAn_TourDuLich/' . $postImg;
                            }
                        ?>
                        <div style="margin-top:5px;">
                            <a href="<?php echo $postImg; ?>" target="_blank">
                                <img src="<?php echo $postImg; ?>" style="height:60px; border-radius:4px; border:1px solid #ddd;">
                            </a>
                        </div>
                    <?php endif; ?>
                </td>

                <td>
                    <span style="font-size:13px; color:#666;">
                        <?php echo date('H:i d/m/Y', strtotime($p['NgayDang'])); ?>
                    </span>
                </td>

                <td style="text-align:center;">
                    <a href="posts.php?delete=<?php echo $p['id']; ?>" 
                       onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không? Hành động này không thể hoàn tác!')" 
                       class="btn btn-danger" 
                       style="background:#dc3545; color:white; padding:5px 10px; border-radius:4px; text-decoration:none;">
                        <i class="fas fa-trash"></i> Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>