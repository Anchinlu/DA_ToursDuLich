<?php include 'includes/header.php'; 

$userCount = $db->query("SELECT COUNT(*) FROM NguoiDung WHERE VaiTro != 'admin'")->fetchColumn();
$tourCount = $db->query("SELECT COUNT(*) FROM Tour")->fetchColumn();
$orderPending = $db->query("SELECT COUNT(*) FROM DonDatTour WHERE TrangThai = 'Chờ xử lý'")->fetchColumn();
$revenue = $db->query("SELECT SUM(TongGia) FROM DonDatTour WHERE TrangThai = 'Đã xác nhận'")->fetchColumn();

$sqlChart1 = "SELECT t.TenTour, SUM(d.TongGia) as DoanhThu 
              FROM DonDatTour d 
              JOIN Tour t ON d.idTour = t.id 
              WHERE d.TrangThai = 'Đã xác nhận' 
              GROUP BY d.idTour 
              ORDER BY DoanhThu DESC 
              LIMIT 5";
$chart1Data = $db->query($sqlChart1)->fetchAll(PDO::FETCH_ASSOC);

$labels1 = [];
$data1 = [];
foreach($chart1Data as $item) {
    $labels1[] = $item['TenTour'];
    $data1[] = $item['DoanhThu'];
}

$sqlChart2 = "SELECT TrangThai, COUNT(*) as SoLuong FROM DonDatTour GROUP BY TrangThai";
$chart2Data = $db->query($sqlChart2)->fetchAll(PDO::FETCH_ASSOC);

$labels2 = [];
$data2 = [];
foreach($chart2Data as $item) {
    $labels2[] = $item['TrangThai'];
    $data2[] = $item['SoLuong'];
}
?>

<h2 style="margin-bottom: 20px;">Tổng Quan Hệ Thống</h2>
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?php echo $userCount; ?></h3>
            <p>Khách hàng</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-map-marked-alt"></i></div>
        <div class="stat-info">
            <h3><?php echo $tourCount; ?></h3>
            <p>Tour hiện có</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-info">
            <h3><?php echo $orderPending; ?></h3>
            <p>Đơn chờ duyệt</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-purple"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
            <h3 style="font-size:22px;">
                <?php echo number_format($revenue ?? 0, 0, ',', '.'); ?>đ
            </h3>
            <p>Tổng doanh thu</p>
        </div>
    </div>
</div>

<div class="grid-charts">
    <div class="chart-container">
        <h3 style="margin-bottom:15px; font-size:18px;">Top Tour Doanh Thu Cao</h3>
        <canvas id="revenueChart"></canvas>
    </div>

    <div class="chart-container">
        <h3 style="margin-bottom:15px; font-size:18px;">Tỷ Lệ Đơn Hàng</h3>
        <canvas id="statusChart"></canvas>
    </div>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h3 style="font-size:18px;">Đơn Hàng Mới Nhất</h3>
        <a href="orders.php" class="btn btn-primary">Xem tất cả</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Khách Hàng</th>
                <th>Tổng Tiền</th>
                <th>Trạng Thái</th>
                <th>Ngày Đặt</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $latestOrders = $db->query("SELECT d.*, u.TenDayDu FROM DonDatTour d JOIN NguoiDung u ON d.idNguoiDung = u.id ORDER BY d.NgayDat DESC LIMIT 5")->fetchAll();
            foreach($latestOrders as $order): 
                $color = 'bg-orange';
                if($order['TrangThai'] == 'Đã xác nhận') $color = 'bg-green';
                if($order['TrangThai'] == 'Đã hủy') $color = 'bg-red';
            ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['TenDayDu']); ?></td>
                <td style="font-weight:bold;"><?php echo number_format($order['TongGia']); ?>đ</td>
                <td><span class="status-badge <?php echo $color; ?>"><?php echo $order['TrangThai']; ?></span></td>
                <td><?php echo date('d/m/Y', strtotime($order['NgayDat'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx1 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels1); ?>, 
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?php echo json_encode($data1); ?>, 
                backgroundColor: '#4f46e5',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    const ctx2 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($labels2); ?>,
            datasets: [{
                data: <?php echo json_encode($data2); ?>,
                backgroundColor: [
                    '#f59e0b',
                    '#10b981',
                    '#ef4444'  
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>