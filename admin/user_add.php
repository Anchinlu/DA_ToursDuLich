<?php include 'includes/header.php'; 

$id = isset($_GET['id']) ? $_GET['id'] : '';
$user = ['TenDangNhap' => '', 'TenDayDu' => '', 'VaiTro' => 'user', 'TrangThai' => 1]; // Mặc định
$isEdit = false;

// NẾU LÀ SỬA -> LẤY DỮ LIỆU CŨ
if ($id) {
    $stmt = $db->prepare("SELECT * FROM NguoiDung WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    $isEdit = true;
}

// XỬ LÝ KHI BẤM LƯU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $status = $_POST['status'];

    try {
        if ($isEdit) {
            // --- CẬP NHẬT ---
            $sql = "UPDATE NguoiDung SET TenDayDu = ?, TrangThai = ? WHERE id = ?";
            $params = [$fullname, $status, $id];
            
            // Nếu có nhập pass mới thì mới đổi pass
            if (!empty($password)) {
                $sql = "UPDATE NguoiDung SET TenDayDu = ?, TrangThai = ?, MatKhau = ? WHERE id = ?";
                $params = [$fullname, $status, password_hash($password, PASSWORD_DEFAULT), $id];
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo "<script>alert('Cập nhật thành công!'); window.location='users.php';</script>";

        } else {
            // --- THÊM MỚI ---
            $sql = "INSERT INTO NguoiDung (TenDangNhap, MatKhau, TenDayDu, VaiTro, TrangThai) VALUES (?, ?, ?, 'user', ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $fullname, $status]);
            echo "<script>alert('Thêm mới thành công!'); window.location='users.php';</script>";
        }
    } catch (Exception $e) {
        echo "<div class='card' style='color:red'>Lỗi: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2><?php echo $isEdit ? 'Chỉnh sửa thành viên' : 'Thêm thành viên mới'; ?></h2>
    <form method="POST" action="" style="margin-top: 20px;">
        
        <div style="margin-bottom: 15px;">
            <label>Tên đăng nhập / Email:</label>
            <input type="text" name="username" class="form-control" style="width:100%; padding:10px; margin-top:5px;" 
                   value="<?php echo $user['TenDangNhap']; ?>" <?php echo $isEdit ? 'readonly style="background:#eee"' : 'required'; ?>>
        </div>

        <div style="margin-bottom: 15px;">
            <label>Họ và tên:</label>
            <input type="text" name="fullname" required style="width:100%; padding:10px; margin-top:5px;" 
                   value="<?php echo $user['TenDayDu']; ?>">
        </div>

        <div style="margin-bottom: 15px;">
            <label>Mật khẩu <?php echo $isEdit ? '(Để trống nếu không đổi)' : ''; ?>:</label>
            <input type="password" name="password" style="width:100%; padding:10px; margin-top:5px;" 
                   <?php echo $isEdit ? '' : 'required'; ?>>
        </div>

        <div style="margin-bottom: 15px;">
            <label>Trạng thái:</label>
            <select name="status" style="width:100%; padding:10px; margin-top:5px;">
                <option value="1" <?php echo $user['TrangThai'] == 1 ? 'selected' : ''; ?>>Hoạt động</option>
                <option value="0" <?php echo $user['TrangThai'] == 0 ? 'selected' : ''; ?>>Khóa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Lưu lại</button>
        <a href="users.php" class="btn btn-warning">Quay lại</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>