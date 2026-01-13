<?php
// Include Header (Chứa Sidebar và giao diện chung)
include 'includes/header.php'; 
require_once '../config/db_connect.php';

// --- 1. KIỂM TRA QUYỀN ADMIN (Sửa lại cho khớp với hệ thống của bạn) ---
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập thì đá về login
    echo "<script>window.location.href='../auth/login.php';</script>";
    exit;
}

// Kiểm tra quyền (Nếu trong header.php chưa check)
// $chk = $db->prepare("SELECT VaiTro FROM NguoiDung WHERE id = ?");
// $chk->execute([$_SESSION['user_id']]);
// if ($chk->fetchColumn() !== 'admin') { echo "<script>window.location.href='../index.php';</script>"; exit; }


// --- 2. XỬ LÝ: CẬP NHẬT TRẠNG THÁI ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Duyệt (Đánh dấu đã xong)
    if ($_GET['action'] == 'approve') {
        $stmt = $db->prepare("UPDATE HoTro SET TrangThai = 'da_xong' WHERE id = ?");
        $stmt->execute([$id]);
        echo "<script>window.location.href='support.php';</script>";
        exit;
    }

    // Xóa báo cáo
    if ($_GET['action'] == 'delete') {
        $stmt = $db->prepare("DELETE FROM HoTro WHERE id = ?");
        $stmt->execute([$id]);
        echo "<script>window.location.href='support.php';</script>";
        exit;
    }
}

// --- 3. LẤY DANH SÁCH BÁO CÁO ---
$sql = "SELECT h.*, u.TenDayDu, u.TenDangNhap, u.Avatar 
        FROM HoTro h 
        JOIN NguoiDung u ON h.idNguoiDung = u.id 
        ORDER BY field(h.TrangThai, 'cho_xu_ly', 'da_xong'), h.NgayGui DESC";
$reports = $db->query($sql)->fetchAll();
?>

<style>
    .page-title { margin-bottom: 25px; color: #333; font-weight: 700; border-left: 5px solid #4f46e5; padding-left: 15px; }
    
    /* Card chứa bảng */
    .table-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden; /* Để bo góc bảng */
        border: 1px solid #f3f4f6;
    }

    table { width: 100%; border-collapse: collapse; }
    
    /* Header bảng */
    thead tr { background-color: #f9fafb; border-bottom: 2px solid #e5e7eb; }
    th { text-align: left; padding: 16px; font-size: 13px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
    
    /* Body bảng */
    td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #e5e7eb; color: #374151; font-size: 14px; }
    tr:last-child td { border-bottom: none; }
    tr:hover { background-color: #f9fafb; transition: background 0.2s; }

    /* Cột người gửi */
    .user-info { display: flex; align-items: center; gap: 12px; }
    .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; }
    .user-meta div { line-height: 1.4; }
    .user-name { font-weight: 600; color: #111827; }
    .user-email { font-size: 12px; color: #6b7280; }

    /* Cột nội dung */
    .report-title { font-weight: 600; color: #4f46e5; margin-bottom: 4px; display: block; }
    .report-desc { color: #4b5563; font-size: 13px; max-width: 450px; line-height: 1.5; }
    .report-time { font-size: 12px; color: #9ca3af; margin-top: 5px; display: block; }

    /* Badge Trạng thái */
    .status-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
    .badge-pending { background-color: #fef3c7; color: #92400e; } /* Vàng */
    .badge-done { background-color: #d1fae5; color: #065f46; }    /* Xanh */
    
    /* Nút hành động */
    .action-btn { 
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 6px; 
        transition: all 0.2s; color: white; border: none; cursor: pointer; text-decoration: none;
    }
    .btn-approve { background-color: #10b981; margin-right: 5px; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); }
    .btn-approve:hover { background-color: #059669; transform: translateY(-2px); }
    
    .btn-delete { background-color: #ef4444; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3); }
    .btn-delete:hover { background-color: #dc2626; transform: translateY(-2px); }

    /* Responsive */
    @media (max-width: 768px) {
        .report-desc { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    }
</style>

<div class="main-content" style="padding: 20px;">
    
    <h2 class="page-title">📬 Quản lý Hỗ trợ & Báo cáo sự cố</h2>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th width="25%">Người gửi</th>
                    <th width="45%">Nội dung báo cáo</th>
                    <th width="15%">Trạng thái</th>
                    <th width="15%">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($reports) > 0): ?>
                    <?php foreach($reports as $r): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <?php 
                                        $avt = $r['Avatar'];
                                        // Xử lý link ảnh
                                        if(empty($avt)) $avt = '../assets/images/default-avatar.png';
                                        elseif(strpos($avt, 'http') !== 0 && strpos($avt, 'uploads/') !== 0) $avt = '../uploads/avatars/'.$avt;
                                        elseif(strpos($avt, 'uploads/') === 0) $avt = '../'.$avt;
                                    ?>
                                    <img src="<?php echo $avt; ?>" class="user-avatar" onerror="this.src='../assets/images/default-avatar.png'">
                                    <div class="user-meta">
                                        <div class="user-name"><?php echo htmlspecialchars($r['TenDayDu']); ?></div>
                                        <div class="user-email"><?php echo htmlspecialchars($r['TenDangNhap']); ?></div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="report-title"><?php echo htmlspecialchars($r['TieuDe']); ?></span>
                                <div class="report-desc"><?php echo nl2br(htmlspecialchars($r['NoiDung'])); ?></div>
                                <span class="report-time"><i class="far fa-clock"></i> <?php echo date('H:i - d/m/Y', strtotime($r['NgayGui'])); ?></span>
                            </td>

                            <td>
                                <?php if($r['TrangThai'] == 'cho_xu_ly'): ?>
                                    <span class="status-badge badge-pending">⏳ Chờ xử lý</span>
                                <?php else: ?>
                                    <span class="status-badge badge-done">✅ Đã xong</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if($r['TrangThai'] == 'cho_xu_ly'): ?>
                                    <a href="support.php?action=approve&id=<?php echo $r['id']; ?>" class="action-btn btn-approve" title="Đánh dấu đã xử lý xong">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="support.php?action=delete&id=<?php echo $r['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa báo cáo này?');" title="Xóa báo cáo">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #9ca3af;">
                            <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 10px; display:block;"></i>
                            Hiện không có yêu cầu hỗ trợ nào!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>