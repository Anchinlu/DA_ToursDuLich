<?php include 'includes/header.php'; 

// --- XỬ LÝ LOGIC (DELETE / LOCK) ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if ($_GET['action'] == 'delete') {
        $stmt = $db->prepare("DELETE FROM NguoiDung WHERE id = ?");
        $stmt->execute([$id]);
        echo "<script>alert('Đã xóa người dùng!'); window.location.href='users.php';</script>";
    }
    
    if ($_GET['action'] == 'lock') {
        // Đảo ngược trạng thái (Nếu đang 1 thì thành 0, ngược lại)
        $stmt = $db->prepare("UPDATE NguoiDung SET TrangThai = NOT TrangThai WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: users.php");
    }
}

// --- XỬ LÝ TÌM KIẾM ---
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT * FROM NguoiDung WHERE VaiTro != 'admin' "; // Không hiện admin để tránh xóa nhầm

if ($keyword) {
    $sql .= " AND (TenDangNhap LIKE :kw OR TenDayDu LIKE :kw OR id = :kw)";
}
$sql .= " ORDER BY id DESC"; // Mới nhất lên đầu

$stmt = $db->prepare($sql);
if ($keyword) {
    $stmt->bindValue(':kw', "%$keyword%");
}
$stmt->execute();
$users = $stmt->fetchAll();
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2>Quản lý Người dùng</h2>
        <a href="user_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>

    <form action="" method="GET" class="search-box">
        <input type="text" name="q" placeholder="Tìm theo tên, email, ID..." value="<?php echo htmlspecialchars($keyword); ?>">
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        <?php if($keyword): ?>
            <a href="users.php" class="btn btn-warning">Hủy lọc</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Avatar</th>
                <th>Thông tin</th>
                <th>Google ID</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td>#<?php echo $u['id']; ?></td>
                <td>
                    <?php 
                        $avatar = $u['Avatar'] ?: 'default.jpg';
                        if(strpos($avatar, 'http') === false) $avatar = '../uploads/avatars/'.$avatar;
                    ?>
                    <img src="<?php echo $avatar; ?>" width="40" height="40" style="border-radius:50%; object-fit:cover;">
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($u['TenDayDu']); ?></strong><br>
                    <small><?php echo htmlspecialchars($u['TenDangNhap']); ?></small>
                </td>
                <td>
                    <?php echo $u['GoogleID'] ? '<span style="color:green"><i class="fab fa-google"></i> Có</span>' : '<span style="color:#ccc">Không</span>'; ?>
                </td>
                <td>
                    <?php if ($u['TrangThai'] == 1): ?>
                        <span class="status-badge status-active">Hoạt động</span>
                    <?php else: ?>
                        <span class="status-badge status-locked">Đã khóa</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="user_add.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                    
                    <a href="users.php?id=<?php echo $u['id']; ?>&action=lock" class="btn btn-sm <?php echo $u['TrangThai']==1 ? 'btn-danger' : 'btn-primary'; ?>">
                        <i class="fas <?php echo $u['TrangThai']==1 ? 'fa-lock' : 'fa-lock-open'; ?>"></i>
                    </a>

                    <a href="users.php?id=<?php echo $u['id']; ?>&action=delete" onclick="return confirm('Bạn chắc chắn muốn xóa vĩnh viễn?');" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>