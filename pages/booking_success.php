<?php include '../includes/header.php'; ?>
<?php require_once '../config/db_connect.php'; ?>

<style>
    .success-box {
        max-width: 600px; margin: 80px auto; text-align: center;
        padding: 40px; background: #fff; border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #eee;
    }
    .icon-success { font-size: 60px; color: #2ecc71; margin-bottom: 20px; }
    .btn-home { background: #333; color: white; padding: 10px 25px; border-radius: 5px; text-decoration: none; }
</style>

<div class="container">
    <div class="success-box">
        <i class="fas fa-check-circle icon-success"></i>
        <h2 style="margin-bottom: 15px; color: #2d3436;">Đặt Tour Thành Công!</h2>
        <p style="color: #666; margin-bottom: 30px;">
            Cảm ơn bạn đã tin tưởng LOCY Travel. <br>
            Nhân viên của chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận lịch trình.
        </p>
        
        <div style="display:flex; justify-content:center; gap:15px;">
            <a href="index.php" class="btn-home">Về Trang Chủ</a>
            <a href="history.php" class="btn-primary" style="text-decoration:none;">Xem Lịch Sử Đặt</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>