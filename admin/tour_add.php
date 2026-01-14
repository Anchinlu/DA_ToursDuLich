<?php 
include 'includes/header.php'; 
require_once '../config/db_connect.php';

// --- 1. TÍCH HỢP CLOUDINARY ---
if (file_exists('../config/cloudinary_setup.php')) {
    require_once '../config/cloudinary_setup.php';
}
use Cloudinary\Api\Upload\UploadApi;

$id = isset($_GET['id']) ? $_GET['id'] : '';
$tour = ['TenTour' => '', 'idDanhMuc' => '', 'Gia' => '', 'MoTa' => '', 'HinhAnh' => ''];
$isEdit = false;

// Lấy danh sách danh mục
$cats = $db->query("SELECT * FROM DanhMuc")->fetchAll();

// Nếu là Sửa -> Lấy thông tin cũ
if ($id) {
    $stmt = $db->prepare("SELECT * FROM Tour WHERE id = ?");
    $stmt->execute([$id]);
    $tour = $stmt->fetch();
    $isEdit = true;
}

// --- 2. XỬ LÝ LƯU DỮ LIỆU ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $cat_id = $_POST['cat_id'];
    $price = $_POST['price'];
    $desc = $_POST['desc']; // Nội dung từ CKEditor 5
    
    $image_url = $tour['HinhAnh']; 
    
    // XỬ LÝ UPLOAD ẢNH
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        try {
            $upload = (new UploadApi())->upload($_FILES['image']['tmp_name'], [
                'folder' => 'chinliu_tours',
                'resource_type' => 'image'
            ]);
            $image_url = $upload['secure_url'];
        } catch (Exception $e) {
            echo "<script>alert('Lỗi Cloudinary: " . $e->getMessage() . "');</script>";
        }
    }

    try {
        if ($isEdit) {
            $sql = "UPDATE Tour SET TenTour=?, idDanhMuc=?, Gia=?, MoTa=?, HinhAnh=? WHERE id=?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $cat_id, $price, $desc, $image_url, $id]);
        } else {
            $sql = "INSERT INTO Tour (TenTour, idDanhMuc, Gia, MoTa, HinhAnh) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $cat_id, $price, $desc, $image_url]);
        }
        echo "<script>alert('Lưu tour thành công!'); window.location='tours.php';</script>";
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Lỗi Database: " . $e->getMessage() . "</div>";
    }
}
?>

<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<style>
    /* Chỉnh chiều cao cho khung soạn thảo CKEditor 5 */
    .ck-editor__editable { min-height: 400px; }
    
    .form-group { margin-bottom: 15px; }
    .form-control { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-primary { background: #0866FF; color: white; }
    .btn-secondary { background: #6c757d; color: white; }
    .btn:hover { opacity: 0.9; }
</style>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2><?php echo $isEdit ? '✏️ Chỉnh sửa Tour' : '➕ Thêm Tour Mới'; ?></h2>
        <a href="tours.php" class="btn btn-secondary">Quay lại</a>
    </div>
    
    <form method="POST" action="" enctype="multipart/form-data" style="margin-top: 20px;">
        
        <div class="row" style="display:flex; gap:20px;">
            <div style="flex: 1;">
                <div class="form-group">
                    <label>Tên Tour:</label>
                    <input type="text" name="name" class="form-control" required 
                           placeholder="Ví dụ: Tour Đà Nẵng - Hội An"
                           value="<?php echo htmlspecialchars($tour['TenTour']); ?>">
                </div>

                <div class="form-group">
                    <label>Danh mục:</label>
                    <select name="cat_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $tour['idDanhMuc'] == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['TenDanhMuc']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Giá Tour (VNĐ):</label>
                    <input type="number" name="price" class="form-control" required 
                           value="<?php echo $tour['Gia']; ?>">
                </div>

                <div class="form-group">
                    <label>Hình ảnh đại diện:</label>
                    <input type="file" name="image" accept="image/*" class="form-control" onchange="previewImage(this)">
                    
                    <div style="margin-top:10px; border:1px dashed #ccc; padding:5px; width:fit-content; border-radius:5px;">
                        <?php 
                            // FIX 2: Dùng placehold.co thay cho via.placeholder để tránh lỗi mạng
                            $showImg = !empty($tour['HinhAnh']) ? $tour['HinhAnh'] : 'https://placehold.co/300x200?text=Chưa+có+ảnh';
                            
                            if (!empty($tour['HinhAnh']) && strpos($tour['HinhAnh'], 'http') !== 0) {
                                $showImg = '/DoAn_TourDuLich/' . $tour['HinhAnh'];
                            }
                        ?>
                        <img id="preview-img" src="<?php echo $showImg; ?>" style="max-width: 100%; height: 150px; object-fit: cover;">
                    </div>
                </div>
            </div>

            <div style="flex: 2;">
                <div class="form-group">
                    <label style="font-size:16px; color:#0866FF; font-weight:bold;">
                        <i class="fas fa-list-alt"></i> Chi tiết Lịch trình & Mô tả:
                    </label>
                    <textarea name="desc" id="editor_desc">
                        <?php echo $tour['MoTa']; ?>
                    </textarea>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px; text-align:right;">
            <button type="submit" class="btn btn-primary" style="padding:10px 30px; font-size:16px;">
                <i class="fas fa-save"></i> <?php echo $isEdit ? 'Cập nhật Tour' : 'Lưu Tour Mới'; ?>
            </button>
        </div>
    </form>
</div>

<script>
    ClassicEditor
        .create(document.querySelector('#editor_desc'))
        .catch(error => {
            console.error(error);
        });

    // Preview ảnh
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'includes/footer.php'; ?>