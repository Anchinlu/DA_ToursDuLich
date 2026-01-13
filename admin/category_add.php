<?php include 'includes/header.php'; 

$id = isset($_GET['id']) ? $_GET['id'] : '';
$cat = ['TenDanhMuc' => '']; // Giá trị mặc định
$isEdit = false;

// NẾU LÀ SỬA -> LẤY DỮ LIỆU CŨ
if ($id) {
    $stmt = $db->prepare("SELECT * FROM DanhMuc WHERE id = ?");
    $stmt->execute([$id]);
    $cat = $stmt->fetch();
    $isEdit = true;
}

// XỬ LÝ KHI BẤM LƯU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    try {
        if ($isEdit) {
            // Cập nhật
            $sql = "UPDATE DanhMuc SET TenDanhMuc = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $id]);
            echo "<script>alert('Cập nhật thành công!'); window.location='categories.php';</script>";
        } else {
            // Thêm mới
            // Kiểm tra trùng tên
            $check = $db->prepare("SELECT COUNT(*) FROM DanhMuc WHERE TenDanhMuc = ?");
            $check->execute([$name]);
            if ($check->fetchColumn() > 0) {
                echo "<div class='card' style='color:red; margin-bottom:20px;'>Lỗi: Tên danh mục đã tồn tại!</div>";
            } else {
                $sql = "INSERT INTO DanhMuc (TenDanhMuc) VALUES (?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$name]);
                echo "<script>alert('Thêm mới thành công!'); window.location='categories.php';</script>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='card' style='color:red'>Lỗi hệ thống: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h2><?php echo $isEdit ? 'Chỉnh sửa Danh mục' : 'Thêm Danh mục Mới'; ?></h2>
    
    <form method="POST" action="" style="margin-top: 20px;">
        <div style="margin-bottom: 20px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Tên Danh Mục:</label>
            <input type="text" name="name" required class="form-control" 
                   style="width:100%; padding:12px; border:1px solid #ddd; border-radius:6px;" 
                   value="<?php echo htmlspecialchars($cat['TenDanhMuc']); ?>"
                   placeholder="Ví dụ: Tour Miền Bắc, Tour Biển...">
        </div>

        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Cập nhật' : 'Thêm mới'; ?></button>
            <a href="categories.php" class="btn btn-warning">Hủy bỏ</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>