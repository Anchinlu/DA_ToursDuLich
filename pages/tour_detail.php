<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('Không tìm thấy tour!'); window.location='index.php';</script>";
    exit;
}
$tour_id = $_GET['id'];

try {
    // Lấy thông tin Tour
    $sql = "SELECT t.*, d.TenDanhMuc 
            FROM Tour t 
            LEFT JOIN DanhMuc d ON t.idDanhMuc = d.id 
            WHERE t.id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$tour_id]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tour) {
        echo "<script>alert('Tour không tồn tại!'); window.location='index.php';</script>";
        exit;
    }

    // Lấy danh sách đánh giá & tính trung bình sao
    $reviews = [];
    $avgStar = 0;
    $totalReview = 0;

    try {
        $stmtReview = $db->prepare("
            SELECT d.*, u.TenDayDu, u.Avatar 
            FROM DanhGia d 
            JOIN NguoiDung u ON d.idNguoiDung = u.id 
            WHERE d.idTour = ? 
            ORDER BY d.NgayDanhGia DESC");
        $stmtReview->execute([$tour_id]);
        $reviews = $stmtReview->fetchAll();
        $totalReview = count($reviews);

        if ($totalReview > 0) {
            $sumStar = 0;
            foreach ($reviews as $rv) {
                $sumStar += $rv['SoSao'];
            }
            $avgStar = round($sumStar / $totalReview, 1); 
        }

    } catch (Exception $e) { }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>

<style>
    /* CSS Riêng cho trang chi tiết */
    .tour-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 10px 0; }
    
    /* CSS Đánh giá */
    .review-section { margin-top: 30px; border-top: 1px dashed #eee; padding-top: 30px; }
    .review-header { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; background: #f9f9f9; padding: 20px; border-radius: 8px; }
    .avg-rating { font-size: 40px; font-weight: 800; color: #333; line-height: 1; }
    .avg-stars { color: #f59e0b; font-size: 18px; margin: 5px 0; }
    
    /* Widget chọn sao */
    .star-rating { direction: rtl; display: inline-flex; }
    .star-rating input { display: none; }
    .star-rating label { font-size: 24px; color: #ddd; cursor: pointer; transition: 0.2s; padding: 0 2px; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #f59e0b; }

    /* Comment List */
    .comment-item { display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px; }
    .user-avatar { width: 45px; height: 45px; border-radius: 50%; background: #eee; object-fit: cover; }
    .comment-name { font-weight: 700; font-size: 14px; margin-bottom: 2px; }
    .comment-stars { color: #f59e0b; font-size: 11px; margin-bottom: 5px; }
    .comment-text { color: #555; font-size: 13.5px; line-height: 1.5; }
    .comment-date { font-size: 11px; color: #999; margin-top: 5px; }
</style>

<div class="breadcrumb">
    <div class="container">
        <a href="index.php">Trang chủ</a> <span>/</span> 
        <a href="#"><?php echo htmlspecialchars($tour['TenDanhMuc']); ?></a> <span>/</span> 
        <?php echo htmlspecialchars($tour['TenTour']); ?>
    </div>
</div>

<section class="section-padding" style="padding-top: 20px;">
    <div class="container">
        
        <div class="tour-header">
            <h1 class="tour-title"><?php echo htmlspecialchars($tour['TenTour']); ?></h1>
            <div class="tour-meta">
                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($tour['TenDanhMuc']); ?></span>
                <span><i class="fas fa-star" style="color:orange"></i> <?php echo $avgStar > 0 ? $avgStar : '5.0'; ?> (<?php echo $totalReview; ?> đánh giá)</span>
            </div>
        </div>

        <div class="tour-layout">
            <div class="tour-content">
                <?php
                    // Xử lý ảnh đại diện (Cloudinary hoặc Local)
                    $mainImg = !empty($tour['HinhAnh']) ? $tour['HinhAnh'] : '../assets/images/default-tour.jpg';
                    if (!(strpos($mainImg, 'http') === 0) && strpos($mainImg, '/') !== 0) {
                        $mainImg = '/DoAn_TourDuLich/' . $mainImg;
                    }
                ?>
                <img src="<?php echo $mainImg; ?>" class="tour-image-main" alt="Hình ảnh tour" onerror="this.src='https://via.placeholder.com/800x400?text=Image+Not+Found'">
                
                <div class="content-block">
                    <h3 class="content-title">Chi tiết Chương trình Tour</h3>
                    <div class="content-text" style="font-size: 16px; line-height: 1.6; color:#333;">
                        <?php echo $tour['MoTa']; ?> 
                    </div>
                </div>

                <div class="content-block review-section">
                    <h3 class="content-title">Đánh giá từ khách hàng</h3>
                    
                    <div class="review-header">
                        <div style="text-align:center;">
                            <div class="avg-rating"><?php echo $avgStar > 0 ? $avgStar : '0.0'; ?></div>
                            <div class="avg-stars">
                                <?php for($i=1; $i<=5; $i++) echo ($i <= $avgStar) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star" style="color:#ddd"></i>'; ?>
                            </div>
                            <div style="font-size:12px; color:#666;"><?php echo $totalReview; ?> lượt nhận xét</div>
                        </div>
                        <div style="flex:1; border-left:1px solid #ddd; padding-left:20px; color:#555; font-size:14px;">
                            Chia sẻ trải nghiệm thực tế của bạn để giúp những du khách khác có lựa chọn tốt nhất.
                        </div>
                    </div>

                    <?php 
                        $allowReview = false;
                        $msgReview = "";

                        if(isset($_SESSION['user_id'])) {
                            // Chỉ cho phép đánh giá nếu đã đi tour này (Trạng thái: Đã xác nhận)
                            $checkOrder = $db->prepare("SELECT id FROM DonDatTour WHERE idNguoiDung = ? AND idTour = ? AND TrangThai = 'Đã xác nhận'");
                            $checkOrder->execute([$_SESSION['user_id'], $tour_id]);
                            
                            if($checkOrder->rowCount() > 0) {
                                $allowReview = true;
                            } else {
                                $msgReview = "Bạn cần đặt và hoàn thành chuyến đi này mới có thể viết đánh giá.";
                            }
                        } else {
                            $msgReview = 'Vui lòng <a href="../auth/login.php" style="font-weight:bold; color:var(--primary-color);">Đăng nhập</a> để viết đánh giá.';
                        }
                    ?>

                    <?php if($allowReview): ?>
                        <form action="../process/submit_review.php" method="POST" style="background:#fff; border:1px solid #eee; padding:20px; border-radius:8px; margin-bottom:30px;">
                            <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                            <h4 style="margin-top:0; font-size:16px;">Viết đánh giá của bạn</h4>
                            
                            <div style="margin-bottom:10px;">
                                <div class="star-rating">
                                    <input type="radio" name="rating" id="star5" value="5" checked><label for="star5" title="Tuyệt vời"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" id="star4" value="4"><label for="star4" title="Tốt"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" id="star3" value="3"><label for="star3" title="Bình thường"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" id="star2" value="2"><label for="star2" title="Tệ"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" id="star1" value="1"><label for="star1" title="Rất tệ"><i class="fas fa-star"></i></label>
                                </div>
                            </div>

                            <textarea name="comment" rows="3" placeholder="Nhập nội dung đánh giá..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-family:inherit;" required></textarea>
                            <button type="submit" class="btn-primary" style="margin-top:10px; padding:8px 20px; font-size:13px;">Gửi đánh giá</button>
                        </form>
                    <?php else: ?>
                        <div style="text-align:center; padding:15px; background:#f0f9ff; border:1px dashed #0ea5e9; border-radius:6px; margin-bottom:20px; color:#555;">
                            <i class="fas fa-info-circle" style="color:#0ea5e9;"></i> <?php echo $msgReview; ?>
                        </div>
                    <?php endif; ?>

                    <div class="reviews-list">
                        <?php if(count($reviews) > 0): ?>
                            <?php foreach($reviews as $rv): ?>
                                <div class="comment-item">
                                    <?php 
                                        $uAvatar = !empty($rv['Avatar']) ? $rv['Avatar'] : '';
                                        // Logic avatar giống header
                                        if(empty($uAvatar)) $uAvatar = 'https://ui-avatars.com/api/?name='.urlencode($rv['TenDayDu']).'&background=random';
                                        elseif(strpos($uAvatar, 'http')!==0 && strpos($uAvatar, '/')!==0) $uAvatar = '/DoAn_TourDuLich/uploads/avatars/'.$uAvatar;
                                        elseif(strpos($uAvatar, 'uploads/')===0) $uAvatar = '/DoAn_TourDuLich/'.$uAvatar;
                                    ?>
                                    <img src="<?php echo $uAvatar; ?>" class="user-avatar">
                                    <div class="comment-content">
                                        <div class="comment-name"><?php echo htmlspecialchars($rv['TenDayDu']); ?></div>
                                        <div class="comment-stars">
                                            <?php for($i=1; $i<=5; $i++) echo ($i <= $rv['SoSao']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                        </div>
                                        <div class="comment-text"><?php echo nl2br(htmlspecialchars($rv['BinhLuan'])); ?></div>
                                        <div class="comment-date"><?php echo date('d/m/Y H:i', strtotime($rv['NgayDanhGia'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align:center; color:#999; font-style:italic;">Chưa có đánh giá nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tour-sidebar">
                <div class="booking-box">
                    <div class="price-tag">
                        <?php echo number_format($tour['Gia'], 0, ',', '.'); ?>đ <span>/ khách</span>
                    </div>

                    <form action="../process/booking_process.php" method="POST" class="booking-form">
                        <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                        <input type="hidden" id="tour_price" value="<?php echo $tour['Gia']; ?>">

                        <div class="form-group">
                            <label><i class="far fa-calendar-alt"></i> Ngày khởi hành:</label>
                            <input type="date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user-friends"></i> Người lớn:</label>
                            <input type="number" name="adults" id="adults" value="1" min="1" onchange="calcTotal()">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-child"></i> Trẻ em (50%):</label>
                            <input type="number" name="children" id="children" value="0" min="0" onchange="calcTotal()">
                        </div>

                        <div class="total-price">
                            <span>Tổng cộng:</span>
                            <span id="total_display" style="color:var(--primary-color)">
                                <?php echo number_format($tour['Gia'], 0, ',', '.'); ?>đ
                            </span>
                        </div>

                        <div style="margin-top: 20px;">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button type="submit" class="btn-primary" style="width:100%">ĐẶT TOUR NGAY</button>
                            <?php else: ?>
                                <a href="../auth/login.php" class="btn-primary" style="display:block; text-align:center; background:#666;">
                                    Đăng nhập để đặt
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
function calcTotal() {
    const price = parseInt(document.getElementById('tour_price').value);
    const adults = parseInt(document.getElementById('adults').value) || 0;
    const children = parseInt(document.getElementById('children').value) || 0;

    // Trẻ em tính 50% giá
    const total = (price * adults) + (price * 0.5 * children);

    // Format tiền tệ (VNĐ)
    const formatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    
    document.getElementById('total_display').innerText = formatted;
}
</script>

<?php include '../includes/footer.php'; ?>