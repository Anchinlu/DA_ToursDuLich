<?php include 'includes/header.php'; 

if (!isset($_GET['id'])) { echo "<script>window.location='orders.php';</script>"; exit; }
$id = $_GET['id'];

// --- HÀM GHI LỊCH SỬ (LOG) ---
function logHistory($db, $orderId, $action, $detail = '') {
    $stmt = $db->prepare("INSERT INTO LichSuDonHang (idDonHang, HanhDong, ChiTiet) VALUES (?, ?, ?)");
    $stmt->execute([$orderId, $action, $detail]);
}

// --- XỬ LÝ CẬP NHẬT THANH TOÁN ---
if (isset($_POST['update_payment'])) {
    $status = $_POST['payment_status']; // 0 hoặc 1
    $stmt = $db->prepare("UPDATE DonDatTour SET ThanhToan = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    // Ghi log
    $msg = ($status == 1) ? "Xác nhận đã thanh toán" : "Hủy trạng thái thanh toán";
    logHistory($db, $id, "Cập nhật thanh toán", $msg);
    
    echo "<script>alert('Đã cập nhật thanh toán!'); window.location='order_detail.php?id=$id';</script>";
}

// --- LẤY THÔNG TIN ĐƠN HÀNG ---
$sql = "SELECT d.*, u.TenDayDu, u.TenDangNhap, t.TenTour, t.HinhAnh 
        FROM DonDatTour d
        JOIN NguoiDung u ON d.idNguoiDung = u.id
        JOIN Tour t ON d.idTour = t.id
        WHERE d.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$order = $stmt->fetch();

// --- LẤY LỊCH SỬ ---
$stmtHistory = $db->prepare("SELECT * FROM LichSuDonHang WHERE idDonHang = ? ORDER BY NgayTao DESC");
$stmtHistory->execute([$id]);
$historyLogs = $stmtHistory->fetchAll();
?>

<style>
    .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
    .info-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
    
    /* Timeline Style */
    .timeline { border-left: 2px solid #e0e0e0; margin-left: 10px; padding-left: 20px; position: relative; }
    .timeline-item { margin-bottom: 20px; position: relative; }
    .timeline-item::before {
        content: ''; position: absolute; left: -26px; top: 5px;
        width: 10px; height: 10px; background: #00897B; border-radius: 50%;
    }
    .time { font-size: 12px; color: #999; }
    .action { font-weight: 600; color: #333; }
    .desc { font-size: 13px; color: #666; font-style: italic; margin-top: 3px; }

    /* Badge Payment */
    .badge-paid { background: #dcfce7; color: #166534; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; }
    .badge-unpaid { background: #fee2e2; color: #991b1b; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; }
</style>

<div style="margin-bottom: 20px;">
    <a href="orders.php" class="btn btn-sm" style="background:#666; color:white;"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<h2 style="margin-bottom: 20px;">Chi tiết đơn hàng #<?php echo $order['id']; ?></h2>

<div class="detail-grid">
    <div class="left-col">
        
        <div class="info-box">
            <h3 style="border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:15px;">Thông tin chung</h3>
            <div style="display:flex; gap:20px;">
                <?php 
                    $tourImg = 'https://placehold.co/120x120?text=No+Image';
                    if (!empty($order['HinhAnh'])) {
                        if (strpos($order['HinhAnh'], 'http') === 0) {
                            $tourImg = $order['HinhAnh'];
                        } else {
                            $tourImg = '/DoAn_TourDuLich/' . ltrim($order['HinhAnh'], '/');
                        }
                    }
                ?>
                <img src="<?php echo $tourImg; ?>" 
                     width="120" height="90" 
                     style="object-fit:cover; border-radius:8px; border:1px solid #ddd;"
                     onerror="this.src='https://placehold.co/120x90?text=Error'">

                <div>
                    <h4 style="margin:0 0 5px 0;"><?php echo htmlspecialchars($order['TenTour']); ?></h4>
                    <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['TenDayDu']); ?> (<?php echo htmlspecialchars($order['TenDangNhap']); ?>)</p>
                    <p><strong>Ngày khởi hành:</strong> <?php echo date('d/m/Y', strtotime($order['NgayKhoiHanh'])); ?></p>
                    <p><strong>Số lượng:</strong> <?php echo $order['SoNguoiLon']; ?> Lớn, <?php echo $order['SoTreEm']; ?> Trẻ</p>
                    <p style="font-size:18px; color:#d32f2f; margin-top:10px;"><strong>Tổng tiền: <?php echo number_format($order['TongGia']); ?>đ</strong></p>
                </div>
            </div>
        </div>

        <div class="info-box">
            <h3 style="margin-bottom:20px;">Lịch sử đơn hàng</h3>
            <div class="timeline">
                <?php if(count($historyLogs) > 0): ?>
                    <?php foreach($historyLogs as $log): ?>
                        <div class="timeline-item">
                            <div class="time"><?php echo date('d/m/Y H:i', strtotime($log['NgayTao'])); ?></div>
                            <div class="action"><?php echo $log['HanhDong']; ?></div>
                            <?php if($log['ChiTiet']): ?>
                                <div class="desc">"<?php echo htmlspecialchars($log['ChiTiet']); ?>"</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#999;">Chưa có lịch sử ghi nhận.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="right-col">
        <div class="info-box">
            <h3>Trạng thái Đơn</h3>
            <div style="margin: 15px 0;">
                <span class="status-badge" style="background:#eee; color:#333; font-size:14px; padding:8px 15px;">
                    <?php echo $order['TrangThai']; ?>
                </span>
            </div>
            <?php if($order['LyDoHuy']): ?>
                <div style="background:#fff3cd; padding:10px; font-size:13px; border-radius:4px; color:#856404;">
                    <strong>Ghi chú:</strong> <?php echo $order['LyDoHuy']; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="info-box">
            <h3>Thanh toán</h3>
            <div style="margin: 15px 0;">
                <?php if($order['ThanhToan'] == 1): ?>
                    <span class="badge-paid"><i class="fas fa-check"></i> ĐÃ THANH TOÁN</span>
                <?php else: ?>
                    <span class="badge-unpaid"><i class="fas fa-times"></i> CHƯA THANH TOÁN</span>
                <?php endif; ?>
            </div>
            
            <form method="POST">
                <input type="hidden" name="update_payment" value="1">
                <select name="payment_status" style="width:100%; padding:8px; margin-bottom:10px;">
                    <option value="0" <?php echo $order['ThanhToan']==0?'selected':''; ?>>Chưa thanh toán</option>
                    <option value="1" <?php echo $order['ThanhToan']==1?'selected':''; ?>>Đã thanh toán</option>
                </select>
                <button type="submit" class="btn btn-primary" style="width:100%">Cập nhật</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>