<?php include 'includes/header.php'; 

// --- XỬ LÝ XÓA TOUR ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $db->prepare("SELECT HinhAnh FROM Tour WHERE id = ?");
        $stmt->execute([$id]);
        $tour = $stmt->fetch();
        
        $db->prepare("DELETE FROM Tour WHERE id = ?")->execute([$id]);
        if ($tour && !empty($tour['HinhAnh'])) {
            if (strpos($tour['HinhAnh'], 'http') !== 0 && file_exists("../" . $tour['HinhAnh'])) {
                unlink("../" . $tour['HinhAnh']);
            }
        }

        echo "<script>alert('Đã xóa tour thành công!'); window.location='tours.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}

// --- LẤY DANH SÁCH TOUR ---
$sql = "SELECT t.*, d.TenDanhMuc 
        FROM Tour t 
        LEFT JOIN DanhMuc d ON t.idDanhMuc = d.id 
        ORDER BY t.id DESC";
$stmt = $db->query($sql);
$tours = $stmt->fetchAll();
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2>Quản lý Tour Du Lịch</h2>
        <a href="tour_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Tour Mới</a>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="10%">Hình ảnh</th>
                <th>Tên Tour</th>
                <th>Danh mục</th>
                <th>Giá (VNĐ)</th>
                <th width="15%">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tours as $tour): ?>
            <tr>
                <td>#<?php echo $tour['id']; ?></td>
                <td>
                    <?php 
                        $imgSrc = '';
                        if (!empty($tour['HinhAnh'])) {
                            if (strpos($tour['HinhAnh'], 'http') === 0) {
                                $imgSrc = $tour['HinhAnh'];
                            } else {
                                $imgSrc = '/DoAn_TourDuLich/' . ltrim($tour['HinhAnh'], '/');
                            }
                        } else {
                            // Ảnh mặc định nếu không có ảnh
                            $imgSrc = 'https://placehold.co/60x40?text=No+Img';
                        }
                    ?>
                    <img src="<?php echo $imgSrc; ?>" 
                         width="60" height="40" 
                         style="object-fit:cover; border-radius:4px; border:1px solid #ddd;"
                         onerror="this.src='https://placehold.co/60x40?text=Error'">
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($tour['TenTour']); ?></strong>
                </td>
                <td>
                    <span class="status-badge" style="background:#e0f2fe; color:#0284c7;">
                        <?php echo htmlspecialchars($tour['TenDanhMuc'] ?? 'Chưa phân loại'); ?>
                    </span>
                </td>
                <td style="color:#d32f2f; font-weight:bold;">
                    <?php echo number_format($tour['Gia'], 0, ',', '.'); ?> ₫
                </td>
                <td>
                    <a href="tour_add.php?id=<?php echo $tour['id']; ?>" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                    <a href="tours.php?id=<?php echo $tour['id']; ?>&action=delete" onclick="return confirm('Bạn chắc chắn muốn xóa tour này?');" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>