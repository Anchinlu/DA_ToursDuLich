<?php include 'includes/header.php'; 

// --- HÀM GHI LOG LỊCH SỬ (QUAN TRỌNG: PHẢI CÓ) ---
function logHistory($db, $orderId, $action, $detail = '') {
    try {
        $stmt = $db->prepare("INSERT INTO LichSuDonHang (idDonHang, HanhDong, ChiTiet) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $action, $detail]);
    } catch (Exception $e) {
        // Bỏ qua lỗi log để không ảnh hưởng luồng chính
    }
}

// --- 1. XỬ LÝ FORM TỪ MODAL (Cập nhật Hủy/Hoãn) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_incident') {
    $id = $_POST['order_id'];
    $status = $_POST['incident_type']; 
    $reason = $_POST['reason'];
    
    // Lấy số ngày hoãn từ form (nếu không chọn thì là 0)
    $delay_days = isset($_POST['delay_days']) ? intval($_POST['delay_days']) : 0;

    try {
        // Cập nhật cả trạng thái, lý do VÀ SỐ NGÀY HOÃN
        $stmt = $db->prepare("UPDATE DonDatTour SET TrangThai = ?, LyDoHuy = ?, SoNgayHoan = ? WHERE id = ?");
        $stmt->execute([$status, $reason, $delay_days, $id]);
        
        // Ghi Log hành động của Admin
        logHistory($db, $id, "Admin cập nhật: $status", $reason);

        echo "<script>alert('Đã cập nhật trạng thái!'); window.location='orders.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi cập nhật: " . $e->getMessage() . "');</script>";
    }
}

// --- 2. XỬ LÝ DUYỆT ĐƠN NHANH ---
if (isset($_GET['id']) && isset($_GET['status']) && $_GET['status'] == 'Đã xác nhận') {
    try {
        $id = $_GET['id'];
        $stmt = $db->prepare("UPDATE DonDatTour SET TrangThai = 'Đã xác nhận' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Ghi Log
        logHistory($db, $id, "Admin duyệt đơn", "Xác nhận nhanh từ danh sách");

        echo "<script>window.location='orders.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi duyệt đơn: " . $e->getMessage() . "');</script>";
    }
}

// --- 3. LẤY DANH SÁCH ĐƠN HÀNG ---
$orders = [];
try {
    $sql = "SELECT d.*, u.TenDayDu, t.TenTour, d.NgayKhoiHanh 
            FROM DonDatTour d
            JOIN NguoiDung u ON d.idNguoiDung = u.id
            JOIN Tour t ON d.idTour = t.id
            ORDER BY d.NgayDat DESC";
    $stmt = $db->query($sql);
    if ($stmt) $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<div class='card' style='color:red;'>LỖI SQL: " . $e->getMessage() . "</div>";
}
?>

<style>
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
    .modal-content { background-color: #fff; margin: 5% auto; padding: 25px; border-radius: 8px; width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: slideDown 0.3s ease; }
    @keyframes slideDown { from {transform: translateY(-50px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
    .close { float: right; font-size: 24px; font-weight: bold; cursor: pointer; color: #aaa; }
    .close:hover { color: #000; }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size:13px; color:#555; }
    .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; }
    
    .template-select { border-color: #00897B !important; color: #00695C; font-weight: 600; background: #e0f2f1; }
</style>

<div class="card">
    <h2>Quản lý Đơn Đặt Tour</h2>
    
    <table>
        <thead>
            <tr>
                <th>Mã</th>
                <th>Khách hàng</th>
                <th>Tour & Ngày đi</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Thông báo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['TenDayDu']); ?></td>
                    <td>
                        <span style="color:#00897B; font-weight:600;"><?php echo htmlspecialchars($order['TenTour']); ?></span><br>
                        <?php if(!empty($order['NgayKhoiHanh'])): ?>
                            <small><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($order['NgayKhoiHanh'])); ?></small>
                        <?php endif; ?>
                    </td>
                    <td style="color:#d32f2f; font-weight:bold;"><?php echo number_format($order['TongGia']); ?>đ</td>
                    <td>
                        <?php 
                            $stt = $order['TrangThai'];
                            $color = 'bg-orange';
                            if($stt == 'Đã xác nhận') $color = 'bg-green';
                            if($stt == 'Đã hủy') $color = 'bg-red';
                            if($stt == 'Tạm hoãn') $color = 'bg-warning';
                        ?>
                        <span class="status-badge <?php echo $color; ?>"><?php echo $stt; ?></span>
                    </td>
                    <td style="font-size:12px; color:#666; max-width: 200px; font-style:italic;">
                        <?php echo htmlspecialchars($order['LyDoHuy'] ?? ''); ?>
                    </td>
                    <td style="display:flex; gap:5px;">
                        
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm" style="background:#6366f1; color:white;" title="Xem chi tiết & Lịch sử">
                            <i class="fas fa-eye"></i>
                        </a>

                        <?php if ($order['TrangThai'] == 'Chờ xử lý'): ?>
                            <a href="orders.php?id=<?php echo $order['id']; ?>&status=Đã xác nhận" class="btn btn-sm btn-primary" title="Duyệt"><i class="fas fa-check"></i></a>
                        <?php endif; ?>

                        <?php if ($order['TrangThai'] != 'Đã hủy'): ?>
                            <button onclick="openModal(<?php echo $order['id']; ?>)" class="btn btn-sm btn-warning" title="Xử lý sự cố"><i class="fas fa-exclamation-triangle"></i></button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">Chưa có đơn hàng nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="incidentModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 style="margin-top:0; color:#d32f2f;">⚠ Xử lý sự cố đơn hàng</h3>
        
        <form method="POST" action="orders.php">
            <input type="hidden" name="action" value="update_incident">
            <input type="hidden" id="modal_order_id" name="order_id" value="">
            
            <div class="form-group">
                <label>Hành động:</label>
                <select name="incident_type" id="incidentType" onchange="handleActionChange()">
                    <option value="Tạm hoãn">⏳ Tạm hoãn (Yêu cầu chọn lại ngày)</option>
                    <option value="Đã hủy">❌ Hủy đơn (Hủy bỏ hoàn toàn)</option>
                </select>
            </div>

            <div class="form-group" id="delayGroup">
                <label>Gợi ý thời gian hoãn:</label>
                <select name="delay_days" id="delaySelect" onchange="applyDelayReason()">
                    <option value="">-- Chọn nhanh --</option>
                    <option value="3">Khoảng 3 ngày</option>
                    <option value="5">Khoảng 5 ngày</option>
                    <option value="7">Khoảng 1 tuần</option>
                    <option value="14">Khoảng 2 tuần</option>
                    <option value="0">Chưa xác định</option>
                </select>
            </div>

            <div class="form-group">
                <label>✨ Hoặc chọn văn mẫu lý do:</label>
                <select id="templateSelect" class="template-select" onchange="applyTemplate()">
                    <option value="">-- Chọn mẫu lý do --</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nội dung thông báo chi tiết:</label>
                <textarea name="reason" id="reasonText" rows="4" required></textarea>
            </div>

            <div style="text-align:right;">
                <button type="button" onclick="closeModal()" class="btn btn-sm" style="background:#ccc; margin-right:10px;">Đóng</button>
                <button type="submit" class="btn btn-primary">Cập nhật & Gửi</button>
            </div>
        </form>
    </div>
</div>

<script>
    const templatesData = {
        'Tạm hoãn': [
            { label: 'Sự cố xe/tàu', text: 'Do sự cố kỹ thuật về phương tiện di chuyển, lịch trình sẽ bị tạm hoãn. Quý khách vui lòng chọn lại ngày khởi hành mới.' },
            { label: 'Hoãn do thời tiết', text: 'Tạm hoãn lịch trình do điều kiện thời tiết xấu, không đảm bảo an toàn. Quý khách vui lòng chọn ngày khác.' },
            { label: 'Chờ ghép đoàn', text: 'Tạm hoãn để chờ ghép đủ số lượng thành viên tối thiểu cho đoàn. Mong quý khách thông cảm.' }
        ],
        'Đã hủy': [
            { label: 'Hủy do Bão/Thiên tai', text: 'Do điều kiện thời tiết cực đoan (bão/lũ), chuyến đi buộc phải hủy bỏ để đảm bảo an toàn. Chúng tôi sẽ hoàn tiền cọc.' },
            { label: 'Khách yêu cầu hủy', text: 'Đơn hàng đã hủy theo yêu cầu từ phía quý khách hàng.' },
            { label: 'Hết chỗ đột xuất', text: 'Rất xin lỗi quý khách, do sự cố hệ thống nên tour đã hết chỗ. Chúng tôi sẽ hoàn tiền 100%.' }
        ]
    };

    const modal = document.getElementById("incidentModal");
    const incidentTypeSelect = document.getElementById("incidentType");
    const templateSelect = document.getElementById("templateSelect");
    const delayGroup = document.getElementById("delayGroup");
    const delaySelect = document.getElementById("delaySelect");
    const reasonText = document.getElementById("reasonText");

    function handleActionChange() {
        const action = incidentTypeSelect.value;
        delayGroup.style.display = (action == 'Tạm hoãn') ? 'block' : 'none';
        
        const options = templatesData[action];
        templateSelect.innerHTML = '<option value="">-- Chọn mẫu lý do --</option>';
        options.forEach(item => {
            const option = document.createElement("option");
            option.value = item.text;
            option.text = item.label;
            templateSelect.appendChild(option);
        });
        reasonText.value = "";
        delaySelect.value = "";
    }

    function applyDelayReason() {
        const days = delaySelect.value;
        if (days == '0') reasonText.value = "Do sự cố khách quan, chuyến đi tạm hoãn vô thời hạn. Vui lòng chờ thông báo sau.";
        else if (days) reasonText.value = `Do điều kiện khách quan, chuyến đi cần tạm hoãn khoảng ${days} ngày. Quý khách vui lòng bấm nút 'Đổi ngày đi' để chọn lại ngày khởi hành mới.`;
        templateSelect.value = "";
    }

    function applyTemplate() {
        const content = templateSelect.value;
        if (content) {
            reasonText.value = content;
        }
    }

    function openModal(id) { 
        document.getElementById("modal_order_id").value = id; 
        modal.style.display = "block";
        incidentTypeSelect.value = "Tạm hoãn"; 
        handleActionChange(); 
    }
    
    function closeModal() { modal.style.display = "none"; }
    window.onclick = function(e) { if(e.target == modal) closeModal(); }
</script>

<?php include 'includes/footer.php'; ?>