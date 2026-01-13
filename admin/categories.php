<?php include 'includes/header.php'; 

// --- XỬ LÝ XÓA ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        // Kiểm tra xem danh mục này có Tour nào không trước khi xóa
        $check = $db->prepare("SELECT COUNT(*) FROM Tour WHERE idDanhMuc = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('Không thể xóa! Danh mục này đang chứa các Tour du lịch.'); window.location='categories.php';</script>";
        } else {
            $stmt = $db->prepare("DELETE FROM DanhMuc WHERE id = ?");
            $stmt->execute([$id]);
            echo "<script>alert('Đã xóa danh mục!'); window.location='categories.php';</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}

// --- LẤY DANH SÁCH ---
$stmt = $db->query("SELECT * FROM DanhMuc ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2>Quản lý Danh mục Tour</h2>
        <a href="category_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm danh mục</a>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th>Tên Danh Mục</th>
                <th width="20%">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td>#<?php echo $cat['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($cat['TenDanhMuc']); ?></strong></td>
                <td>
                    <a href="category_add.php?id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Sửa</a>
                    <a href="categories.php?id=<?php echo $cat['id']; ?>&action=delete" onclick="return confirm('Bạn chắc chắn muốn xóa?');" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>