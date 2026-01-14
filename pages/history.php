<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) { echo "<script>window.location='../auth/login.php';</script>"; exit; }
$user_id = $_SESSION['user_id'];

// --- 1. XỬ HỦY ĐƠN & YÊU CẦU HOÀN TIỀN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['cancel_pending_id'])) {
        $oid = $_POST['cancel_pending_id'];
        $stmt = $db->prepare("UPDATE DonDatTour SET TrangThai = 'Đã hủy', LyDoHuy = 'Khách chủ động hủy khi chưa thanh toán' WHERE id = ? AND TrangThai = 'Chờ xử lý'");
        $stmt->execute([$oid]);
        echo "<script>alert('Đã hủy đơn hàng!'); window.location='history.php';</script>";
    }

    if (isset($_POST['refund_order_id'])) {
        $oid = $_POST['refund_order_id'];
        $bankInfo = "Yêu cầu hoàn tiền về: " . $_POST['bank_name'] . " - STK: " . $_POST['bank_number'] . " - Chủ TK: " . $_POST['bank_user'];
        
        $stmt = $db->prepare("UPDATE DonDatTour SET TrangThai = 'Yêu cầu hoàn tiền', LyDoHuy = ? WHERE id = ?");
        $stmt->execute([$bankInfo, $oid]);
        echo "<script>alert('Đã gửi yêu cầu hoàn tiền! Admin sẽ xử lý trong 24h.'); window.location='history.php';</script>";
    }
}

try {
    $sql = "SELECT d.*, t.TenTour, t.HinhAnh, t.id as TourID 
            FROM DonDatTour d 
            JOIN Tour t ON d.idTour = t.id 
            WHERE d.idNguoiDung = ? 
            ORDER BY d.id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $orders = []; }
?>

<style>
    body { background-color: #f5f7fa; }
    .history-tabs { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
    .tab-btn { padding: 10px 20px; border: none; background: white; border-radius: 30px; font-weight: 600; color: #666; cursor: pointer; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .tab-btn.active, .tab-btn:hover { background: #0866FF; color: white; box-shadow: 0 4px 10px rgba(8,102,255,0.3); }

    .history-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; transition: transform 0.2s; border: 1px solid #eee; }
    .history-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    
    .hc-img { width: 220px; object-fit: cover; min-height: 160px; }
    .hc-body { flex: 1; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; }
    .hc-top { display: flex; justify-content: space-between; align-items: flex-start; }
    .tour-name { font-size: 18px; font-weight: 700; color: #333; margin: 0 0 5px; }
    .tour-date { font-size: 14px; color: #666; display: flex; align-items: center; gap: 5px; }
    .price-tag { font-size: 16px; font-weight: 700; color: #d32f2f; }
    
    .hc-actions { margin-top: 15px; padding-top: 15px; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .badge-status { padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    
    /* Màu trạng thái */
    .stt-pending { background: #fff3cd; color: #856404; } /* Chờ xử lý */
    .stt-confirmed { background: #d1e7dd; color: #0f5132; } /* Đã xác nhận */
    .stt-cancelled { background: #f8d7da; color: #842029; } /* Đã hủy */
    .stt-delay { background: #ffe69c; color: #997404; } /* Tạm hoãn */
    .stt-refund { background: #cff4fc; color: #055160; } /* Yêu cầu hoàn tiền */

    .btn-action { padding: 8px 15px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; display: inline-flex; align-items: center; gap: 5px; }
    .btn-outline-danger { border: 1px solid #dc3545; color: #dc3545; background: white; }
    .btn-outline-danger:hover { background: #dc3545; color: white; }
    .btn-refund { background: #6c757d; color: white; }
    .btn-refund:hover { background: #5c636a; }
    .btn-primary-review { background: #0866FF; color: white; }
    .btn-primary-review:hover { background: #0056b3; }

    /* MODAL CHUNG */
    .custom-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; }
    .modal-box { background: white; width: 500px; padding: 30px; border-radius: 10px; animation: slideDown 0.3s; position: relative; }
    .star-rating { font-size: 30px; color: #ddd; cursor: pointer; }
    .star-rating.active { color: #ffc107; }
    
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @media (max-width: 768px) { .history-card { flex-direction: column; } .hc-img { width: 100%; height: 180px; } .hc-top { flex-direction: column; gap: 10px; } }
</style>

<div class="container section-padding">
    <h2 class="section-title text-center" style="margin-bottom:30px;">Hành Trình Của Bạn</h2>
    
    <div class="history-tabs">
        <button class="tab-btn active" onclick="filterOrders('all')">Tất cả</button>
        <button class="tab-btn" onclick="filterOrders('pending')">Chờ duyệt</button>
        <button class="tab-btn" onclick="filterOrders('upcoming')">Sắp đi</button>
        <button class="tab-btn" onclick="filterOrders('completed')">Hoàn thành</button>
        <button class="tab-btn" onclick="filterOrders('refund')">Hoàn tiền/Hủy</button>
    </div>

    <div style="max-width: 900px; margin: 0 auto;">
        <?php if (count($orders) == 0): ?>
            <div style="text-align:center; padding:50px;">
                <p>Bạn chưa đặt chuyến đi nào!</p>
                <a href="index.php" class="btn-primary" style="padding:10px 20px;">Khám phá ngay</a>
            </div>
        <?php endif; ?>

        <?php foreach ($orders as $item): ?>
            <?php
                // Xử lý ảnh
                $thumb = !empty($item['HinhAnh']) ? $item['HinhAnh'] : '../assets/images/default-tour.jpg';
                if (!(strpos($thumb, 'http') === 0) && strpos($thumb, '/') !== 0) $thumb = '/DoAn_TourDuLich/' . $thumb;

                // Xử lý Trạng thái & Filter tag
                $stt = $item['TrangThai'];
                $sttClass = 'stt-pending';
                $filterTag = 'pending'; 

                if($stt == 'Đã xác nhận') { $sttClass = 'stt-confirmed'; $filterTag = 'upcoming'; }
                if($stt == 'Đã hủy') { $sttClass = 'stt-cancelled'; $filterTag = 'refund'; }
                if($stt == 'Tạm hoãn') { $sttClass = 'stt-delay'; $filterTag = 'refund'; }
                if($stt == 'Yêu cầu hoàn tiền') { $sttClass = 'stt-refund'; $filterTag = 'refund'; }
                
                $isCompleted = false;
                if ($stt == 'Đã xác nhận' && strtotime($item['NgayKhoiHanh']) < time()) {
                    $filterTag = 'completed';
                    $isCompleted = true;
                }
            ?>

            <div class="history-card" data-status="<?php echo $filterTag; ?>">
                <img src="<?php echo $thumb; ?>" class="hc-img">
                <div class="hc-body">
                    <div>
                        <div class="hc-top">
                            <div>
                                <h4 class="tour-name"><?php echo htmlspecialchars($item['TenTour']); ?></h4>
                                <div class="tour-date"><i class="far fa-calendar-alt"></i> Khởi hành: <strong><?php echo date('d/m/Y', strtotime($item['NgayKhoiHanh'])); ?></strong></div>
                                <div style="font-size:13px; color:#666; margin-top:5px;">
                                    Số người: <?php echo ($item['SoNguoiLon'] + $item['SoTreEm']); ?> | Mã đơn: #<?php echo $item['id']; ?>
                                </div>
                            </div>
                            <div class="price-tag"><?php echo number_format($item['TongGia']); ?>đ</div>
                        </div>
                    </div>

                    <div class="hc-actions">
                        <span class="badge-status <?php echo $sttClass; ?>"><?php echo $stt; ?></span>
                        
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="tour_detail.php?id=<?php echo $item['TourID']; ?>" class="btn-action" style="background:#f0f2f5; color:#333;">Chi tiết</a>

                            <?php if ($stt == 'Chờ xử lý'): ?>
                                <form method="POST" onsubmit="return confirm('Hủy đơn hàng này?');">
                                    <input type="hidden" name="cancel_pending_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn-action btn-outline-danger"><i class="fas fa-times"></i> Hủy đơn</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($isCompleted): ?>
                                <button onclick="openReviewModal(<?php echo $item['TourID']; ?>, '<?php echo htmlspecialchars($item['TenTour']); ?>')" class="btn-action btn-primary-review"><i class="fas fa-star"></i> Đánh giá</button>
                            <?php endif; ?>
                            
                            <?php if ($stt == 'Tạm hoãn'): ?>
                                <button class="btn-action" style="background:#ffe69c; color:#997404;" onclick="document.getElementById('reschedule-<?php echo $item['id']; ?>').style.display='block'">
                                    <i class="fas fa-clock"></i> Đổi lịch
                                </button>
                                <button class="btn-action btn-refund" onclick="openRefundModal(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-undo"></i> Hoàn tiền
                                </button>
                            <?php endif; ?>

                             <?php if ($stt == 'Yêu cầu hoàn tiền'): ?>
                                <small style="color:#055160; font-style:italic;">Đang chờ Admin xử lý...</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($stt == 'Tạm hoãn'): ?>
                        <div id="reschedule-<?php echo $item['id']; ?>" style="display:none; margin-top:10px; background:#fff8e1; padding:10px; border-radius:6px; font-size:13px;">
                            <strong style="color:#e65100;">Admin báo hoãn:</strong> "<?php echo htmlspecialchars($item['LyDoHuy']); ?>"
                            <form action="reschedule.php" method="POST" style="margin-top:5px; display:flex; gap:10px;">
                                <input type="hidden" name="order_id" value="<?php echo $item['id']; ?>">
                                <input type="date" name="new_date" required min="<?php echo date('Y-m-d'); ?>" style="padding:5px; border:1px solid #ddd; border-radius:4px;">
                                <button type="submit" class="btn-primary" style="padding:5px 10px; font-size:12px;">Xác nhận đổi</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="custom-modal" id="reviewModal">
    <div class="modal-box">
        <span onclick="closeModal('reviewModal')" style="position:absolute; right:15px; top:10px; cursor:pointer; font-size:20px;">&times;</span>
        <h3 style="text-align:center;">Đánh giá chuyến đi</h3>
        <p id="reviewTourName" style="text-align:center; color:#666; margin-bottom:20px;"></p>
        <form action="../process/submit_review.php" method="POST">
            <input type="hidden" name="tour_id" id="reviewTourId">
            <input type="hidden" name="rating" id="ratingValue" value="5">
            <div style="text-align:center; margin-bottom:20px;">
                <i class="fas fa-star star-rating active" data-val="1"></i>
                <i class="fas fa-star star-rating active" data-val="2"></i>
                <i class="fas fa-star star-rating active" data-val="3"></i>
                <i class="fas fa-star star-rating active" data-val="4"></i>
                <i class="fas fa-star star-rating active" data-val="5"></i>
            </div>
            <textarea name="comment" rows="4" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;" placeholder="Chia sẻ cảm nhận..."></textarea>
            <button type="submit" class="btn-primary" style="width:100%;">Gửi đánh giá</button>
        </form>
    </div>
</div>

<div class="custom-modal" id="refundModal">
    <div class="modal-box">
        <span onclick="closeModal('refundModal')" style="position:absolute; right:15px; top:10px; cursor:pointer; font-size:20px;">&times;</span>
        <h3 style="text-align:center; color:#dc3545;">Yêu cầu hoàn tiền</h3>
        <p style="text-align:center; font-size:14px; color:#666; margin-bottom:20px;">Vui lòng cung cấp thông tin tài khoản để chúng tôi hoàn lại tiền cọc cho bạn.</p>
        
        <form method="POST">
            <input type="hidden" name="refund_order_id" id="refundOrderId">
            <div style="margin-bottom:15px;">
                <label style="font-weight:600; font-size:13px;">Tên Ngân Hàng:</label>
                <input type="text" name="bank_name" required placeholder="VD: MB Bank, Vietcombank..." style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="font-weight:600; font-size:13px;">Số Tài Khoản:</label>
                <input type="text" name="bank_number" required placeholder="VD: 0987654321" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="font-weight:600; font-size:13px;">Tên Chủ Thẻ:</label>
                <input type="text" name="bank_user" required placeholder="VD: NGUYEN VAN A" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; text-transform:uppercase;">
            </div>
            <button type="submit" class="btn-primary" style="width:100%; background:#dc3545;">Gửi yêu cầu</button>
        </form>
    </div>
</div>

<script>
    // JS LỌC TAB
    function filterOrders(status) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        const cards = document.querySelectorAll('.history-card');
        cards.forEach(card => {
            if (status === 'all' || card.dataset.status === status) card.style.display = 'flex';
            else card.style.display = 'none';
        });
    }

    // JS MODAL ĐÁNH GIÁ
    const stars = document.querySelectorAll('.star-rating');
    const ratingInput = document.getElementById('ratingValue');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const val = this.dataset.val;
            ratingInput.value = val;
            stars.forEach(s => {
                if(s.dataset.val <= val) s.classList.add('active');
                else s.classList.remove('active');
            });
        });
    });

    function openReviewModal(tourId, tourName) {
        document.getElementById('reviewTourId').value = tourId;
        document.getElementById('reviewTourName').innerText = tourName;
        document.getElementById('reviewModal').style.display = 'flex';
    }

    // JS MODAL HOÀN TIỀN
    function openRefundModal(orderId) {
        document.getElementById('refundOrderId').value = orderId;
        document.getElementById('refundModal').style.display = 'flex';
    }

    function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }
</script>

<?php include '../includes/footer.php'; ?>