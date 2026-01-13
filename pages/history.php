<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) { echo "<script>window.location='login.php';</script>"; exit; }
$user_id = $_SESSION['user_id'];

// Lấy danh sách (CHÚ Ý: Lấy thêm cột SoNgayHoan)
try {
    $sql = "SELECT d.*, t.TenTour, t.HinhAnh 
            FROM DonDatTour d 
            JOIN Tour t ON d.idTour = t.id 
            WHERE d.idNguoiDung = ? 
            ORDER BY d.NgayDat DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $orders = []; }
?>

<div class="container section-padding">
    <h2 class="section-title text-center">LỊCH SỬ CHUYẾN ĐI</h2>
    <div style="max-width: 950px; margin: 0 auto;">
        <?php foreach ($orders as $item): ?>
            <div class="history-card" style="background:white; margin-bottom:20px; padding:20px; border-radius:10px; border:1px solid #eee; display:flex; gap:20px;">
                
                <?php
                    $thumb = !empty($item['HinhAnh']) ? $item['HinhAnh'] : 'assets/images/default-tour.jpg';
                    if (!(strpos($thumb, 'http') === 0) && strpos($thumb, '/') !== 0) {
                        $thumb = '/DoAn_TourDuLich/' . $thumb;
                    }
                ?>
                <img src="<?php echo $thumb; ?>" style="width:140px; height:100px; object-fit:cover; border-radius:8px;">
                
                <div style="flex:1;">
                    <h4 style="margin:0 0 5px;"><?php echo htmlspecialchars($item['TenTour']); ?></h4>
                    <p style="color:#666; font-size:13px;">Khởi hành: <strong><?php echo date('d/m/Y', strtotime($item['NgayKhoiHanh'])); ?></strong></p>
                    <p style="color:#666; font-size:13px;">Tổng tiền: <strong style="color:red;"><?php echo number_format($item['TongGia']); ?>đ</strong></p>
                    
                    <div style="margin-top:10px;">
                        <?php 
                            $stt = $item['TrangThai'];
                            $color = '#333'; $bg = '#eee';
                            if($stt == 'Chờ xử lý') { $bg='#fff3cd'; $color='#856404'; }
                            if($stt == 'Đã xác nhận') { $bg='#d4edda'; $color='#155724'; }
                            if($stt == 'Đã hủy') { $bg='#f8d7da'; $color='#721c24'; }
                            if($stt == 'Tạm hoãn') { $bg='#fff3e0'; $color='#e65100'; }
                        ?>
                        <span style="background:<?php echo $bg; ?>; color:<?php echo $color; ?>; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                            <?php echo $stt; ?>
                        </span>
                    </div>
                </div>

                <div style="width:300px; border-left:1px solid #eee; padding-left:20px;">
                    
                    <?php if ($stt == 'Tạm hoãn'): ?>
                        <div style="background:#fff8e1; border:1px solid #ffe082; padding:15px; border-radius:8px;">
                            <strong style="color:#e65100; font-size:13px;">⚠ Yêu cầu đổi lịch</strong>
                            
                            <?php if ($item['SoNgayHoan'] > 0): ?>
                                <?php 
                                    $oldDate = new DateTime($item['NgayKhoiHanh']);
                                    $delayDays = intval($item['SoNgayHoan']);
                                    $newEstimatedDate = $oldDate->modify("+$delayDays days")->format('d/m/Y');
                                ?>
                                <div style="margin:5px 0;">
                                    <small>Hoãn: <b><?php echo $delayDays; ?> ngày</b></small><br>
                                    <small>Dự kiến đi: <b style="color:red;"><?php echo $newEstimatedDate; ?></b></small>
                                </div>
                            <?php else: ?>
                                <div style="margin:5px 0;"><small>Thời gian hoãn: <b>Chưa xác định</b></small></div>
                            <?php endif; ?>

                            <p style="font-size:11px; color:#666; font-style:italic;">"<?php echo htmlspecialchars($item['LyDoHuy']); ?>"</p>
                            
                            <form action="reschedule.php" method="POST" style="margin-top:10px;">
                                <input type="hidden" name="order_id" value="<?php echo $item['id']; ?>">
                                <input type="date" name="new_date" required min="<?php echo date('Y-m-d'); ?>" style="padding:5px; width:60%; font-size:12px;">
                                <button type="submit" class="btn-primary" style="padding:5px 10px; font-size:12px;">Đổi</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ($stt == 'Đã hủy'): ?>
                        <p style="color:red; font-size:12px; margin-top:10px;">Lý do: <?php echo htmlspecialchars($item['LyDoHuy']); ?></p>
                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>