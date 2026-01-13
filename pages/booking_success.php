<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$order_id = $_GET['id'];

// Lấy thông tin đơn vừa đặt để hiện số tiền
$stmt = $db->prepare("SELECT d.*, t.TenTour FROM DonDatTour d JOIN Tour t ON d.idTour = t.id WHERE d.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) die("Đơn hàng không tồn tại");
?>

<div class="container section-padding" style="text-align:center; max-width:800px;">
    
    <div style="margin-bottom:30px;">
        <i class="fas fa-check-circle" style="font-size: 60px; color: #4CAF50;"></i>
        <h2 style="margin-top:20px; color: #4CAF50;">Đặt tour thành công!</h2>
        <p>Mã đơn hàng của bạn là: <strong>#<?php echo $order['id']; ?></strong></p>
    </div>

    <div style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 2px solid #0866FF;">
        <h3 style="color:#0866FF;">Thanh toán để giữ chỗ</h3>
        <p>Vui lòng chuyển khoản hoặc quét mã QR bên dưới để hoàn tất.</p>
        
        <div style="display:flex; justify-content:center; gap:30px; margin-top:20px; flex-wrap:wrap;">
            <div>
                <img src="https://img.vietqr.io/image/MB-0987654321-compact.png?amount=<?php echo $order['TongGia']; ?>&addInfo=THANHTOAN TOUR <?php echo $order['id']; ?>" 
                     style="width: 250px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div style="text-align:left; display:flex; flex-direction:column; justify-content:center;">
                <p><strong>Ngân hàng:</strong> MB Bank (Quân Đội)</p>
                <p><strong>Số tài khoản:</strong> 0987654321</p>
                <p><strong>Chủ tài khoản:</strong> CONG TY DU LICH CHINLIU</p>
                <p><strong>Số tiền:</strong> <span style="color:red; font-size:18px; font-weight:bold;"><?php echo number_format($order['TongGia']); ?> VND</span></p>
                <p><strong>Nội dung CK:</strong> THANHTOAN TOUR <?php echo $order['id']; ?></p>
            </div>
        </div>

        <div style="margin-top:30px;">
            <p style="font-size:13px; color:#666; margin-bottom:15px;">
                * Hệ thống sẽ tự động xác nhận sau khi nhận được tiền.<br>
                * Vui lòng kiểm tra thông báo,xin cảm ơn quý khách.
            </p>
            <a href="history.php" class="btn-primary">Tôi đã chuyển khoản xong</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>