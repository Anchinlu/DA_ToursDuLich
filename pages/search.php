<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

// 1. NHẬN DỮ LIỆU TỪ FORM
$keyword = isset($_GET['k']) ? trim($_GET['k']) : '';
$cat_id = isset($_GET['c']) ? $_GET['c'] : '';
$date = isset($_GET['d']) ? $_GET['d'] : '';
$max_price = isset($_GET['p']) ? $_GET['p'] : '';

// 2. XÂY DỰNG CÂU TRUY VẤN ĐỘNG (DYNAMIC QUERY)
$sql = "SELECT * FROM Tour WHERE 1=1"; // 1=1 là mẹo để nối chuỗi AND dễ dàng
$params = [];

// Nếu có từ khóa (Tìm theo tên tour hoặc mô tả)
if (!empty($keyword)) {
    $sql .= " AND (TenTour LIKE ? OR MoTa LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

// Nếu có chọn danh mục
if (!empty($cat_id)) {
    $sql .= " AND idDanhMuc = ?";
    $params[] = $cat_id;
}

// Nếu có nhập giá tối đa
if (!empty($max_price)) {
    $sql .= " AND Gia <= ?";
    $params[] = $max_price;
}

// (Lưu ý: Phần lọc theo Ngày đi cần bảng Lịch trình chi tiết, tạm thời chưa lọc ở đây)

$sql .= " ORDER BY id DESC";

// 3. THỰC THI
$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<div style="background:#f9f9f9; padding:20px 0; border-bottom:1px solid #eee;">
    <div class="container">
        <a href="index.php">Trang chủ</a> <span style="margin:0 10px; color:#ccc;">/</span> 
        Tìm kiếm
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <h2 class="section-title">
            <?php if($keyword): ?>
                Kết quả cho: "<?php echo htmlspecialchars($keyword); ?>"
            <?php else: ?>
                Tất cả kết quả
            <?php endif; ?>
        </h2>
        <p style="margin-bottom:30px; color:#666;">Tìm thấy <strong><?php echo count($results); ?></strong> tour phù hợp.</p>

        <?php if (count($results) > 0): ?>
            <div class="adventure-grid">
                <?php foreach ($results as $tour): ?>
                    <a href="tour_detail.php?id=<?php echo $tour['id']; ?>" class="card-adventure" style="display:block; text-decoration:none;">
                        <span class="card-badge" style="background:var(--accent-orange);">
                            <?php echo number_format($tour['Gia'], 0, ',', '.'); ?>đ
                        </span>
                        
                        <?php
                            $imgSrc = !empty($tour['HinhAnh']) ? $tour['HinhAnh'] : 'assets/images/default-tour.jpg';
                            // Nếu là URL tuyệt đối hoặc đã bắt đầu bằng '/', giữ nguyên. Ngược lại, thêm tiền tố project.
                            if (!(strpos($imgSrc, 'http') === 0) && strpos($imgSrc, '/') !== 0) {
                                $imgSrc = '/DoAn_TourDuLich/' . $imgSrc;
                            }
                        ?>
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($tour['TenTour']); ?>">
                        
                        <div class="card-overlay">
                            <h4 class="card-title"><?php echo htmlspecialchars($tour['TenTour']); ?></h4>
                            <div class="card-meta">
                                <span><i class="fas fa-map-marker-alt"></i> Việt Nam</span>
                                <span style="color:#4dd0e1; font-weight:700;">Chi tiết <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding:50px; background:#f9f9f9; border-radius:10px;">
                <i class="fas fa-search" style="font-size:50px; color:#ccc; margin-bottom:20px;"></i>
                <h3>Không tìm thấy tour nào!</h3>
                <p>Hãy thử tìm với từ khóa khác hoặc xem tất cả các tour.</p>
                <a href="index.php" class="btn-primary" style="margin-top:20px;">Xem tất cả tour</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>