<?php include 'includes/header.php'; 

$id = isset($_GET['id']) ? $_GET['id'] : '';
$tour = ['TenTour' => '', 'idDanhMuc' => '', 'Gia' => '', 'MoTa' => '', 'HinhAnh' => ''];
$isEdit = false;

// 1. Lấy danh sách Danh mục để hiện vào thẻ <select>
$cats = $db->query("SELECT * FROM DanhMuc")->fetchAll();

// 2. Nếu là Sửa -> Lấy thông tin cũ
if ($id) {
    $stmt = $db->prepare("SELECT * FROM Tour WHERE id = ?");
    $stmt->execute([$id]);
    $tour = $stmt->fetch();
    $isEdit = true;
}

// 3. XỬ LÝ LƯU DỮ LIỆU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $cat_id = $_POST['cat_id'];
    $price = $_POST['price'];
    $desc = $_POST['desc'];
    
    // Xử lý Upload Ảnh
    $image_url = $tour['HinhAnh']; // Mặc định giữ ảnh cũ
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/tours/";
        // Tạo tên file ngẫu nhiên để không trùng
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_name = "tour_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $new_name)) {
            $image_url = "uploads/tours/" . $new_name; // Lưu đường dẫn tương đối vào DB
        }
    }

    try {
        if ($isEdit) {
            // Cập nhật
            $sql = "UPDATE Tour SET TenTour=?, idDanhMuc=?, Gia=?, MoTa=?, HinhAnh=? WHERE id=?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $cat_id, $price, $desc, $image_url, $id]);
        } else {
            // Thêm mới
            $sql = "INSERT INTO Tour (TenTour, idDanhMuc, Gia, MoTa, HinhAnh) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $cat_id, $price, $desc, $image_url]);
        }
        echo "<script>alert('Lưu thành công!'); window.location='tours.php';</script>";
    } catch (Exception $e) {
        echo "<div class='card' style='color:red'>Lỗi: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2><?php echo $isEdit ? 'Chỉnh sửa Tour' : 'Thêm Tour Mới'; ?></h2>
    
    <form method="POST" action="" enctype="multipart/form-data" style="margin-top: 20px;">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div style="margin-bottom: 15px;">
                    <label style="font-weight:bold;">Tên Tour:</label>
                    <input type="text" name="name" required class="form-control" style="width:100%; padding:10px; margin-top:5px; border:1px solid #ddd; border-radius:4px;"
                           value="<?php echo htmlspecialchars($tour['TenTour']); ?>">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight:bold;">Danh mục:</label>
                    <select name="cat_id" required style="width:100%; padding:10px; margin-top:5px; border:1px solid #ddd; border-radius:4px;">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $tour['idDanhMuc'] == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['TenDanhMuc']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight:bold;">Giá (VNĐ):</label>
                    <input type="number" name="price" required style="width:100%; padding:10px; margin-top:5px; border:1px solid #ddd; border-radius:4px;"
                           value="<?php echo $tour['Gia']; ?>">
                </div>
            </div>

            <div>
                <div style="margin-bottom: 15px;">
                    <label style="font-weight:bold;">Hình ảnh:</label>
                    <input type="file" name="image" accept="image/*" style="margin-top:5px;">
                    <?php if ($isEdit && !empty($tour['HinhAnh'])): ?>
                        <div style="margin-top: 10px;">
                            <img src="/DoAn_TourDuLich/<?php echo $tour['HinhAnh']; ?>" width="100%" style="border-radius:4px; border:1px solid #eee;">
                            <p style="font-size:12px; color:#666;">Ảnh hiện tại</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight:bold;">Mô tả ngắn:</label>
                    <textarea name="desc" rows="5" style="width:100%; padding:10px; margin-top:5px; border:1px solid #ddd; border-radius:4px;"><?php echo htmlspecialchars($tour['MoTa']); ?></textarea>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu Tour</button>
            <a href="tours.php" class="btn btn-warning">Hủy bỏ</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>