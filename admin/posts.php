<?php include 'includes/header.php'; 

// XÓA BÀI VIẾT
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM BaiViet WHERE id = ?")->execute([$_GET['delete']]);
    echo "<script>window.location='posts.php';</script>";
}

// LẤY DANH SÁCH
$sql = "SELECT b.*, u.TenDayDu FROM BaiViet b JOIN NguoiDung u ON b.idNguoiDung = u.id ORDER BY b.NgayDang DESC";
$posts = $db->query($sql)->fetchAll();
?>

<div class="card">
    <h2>Quản lý Bài viết Cộng đồng</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Người đăng</th>
                <th>Nội dung (Tóm tắt)</th>
                <th>Hình ảnh</th>
                <th>Ngày đăng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($posts as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo $p['TenDayDu']; ?></td>
                <td><?php echo substr(htmlspecialchars($p['NoiDung']), 0, 50); ?>...</td>
                <td>
                    <?php if($p['HinhAnh']): ?>
                        <img src="/DoAn_TourDuLich/<?php echo $p['HinhAnh']; ?>" width="50" height="50" style="object-fit:cover;">
                    <?php else: ?>
                        Không
                    <?php endif; ?>
                </td>
                <td><?php echo date('d/m/Y', strtotime($p['NgayDang'])); ?></td>
                <td>
                    <a href="posts.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Xóa bài này?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'includes/footer.php'; ?>