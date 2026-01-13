<?php 
include '../includes/header.php'; 
require_once '../config/db_connect.php';

$cats = $db->query("SELECT * FROM DanhMuc")->fetchAll();
$keyword = isset($_GET['k']) ? trim($_GET['k']) : '';
$cat_id = isset($_GET['c']) ? $_GET['c'] : '';
$max_price = isset($_GET['p']) ? $_GET['p'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$sql = "SELECT t.*, d.TenDanhMuc 
        FROM Tour t 
        LEFT JOIN DanhMuc d ON t.idDanhMuc = d.id 
        WHERE 1=1";
$params = [];

if (!empty($keyword)) {
    $sql .= " AND (t.TenTour LIKE ? OR t.MoTa LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
if (!empty($cat_id)) {
    $sql .= " AND t.idDanhMuc = ?";
    $params[] = $cat_id;
}
if (!empty($max_price)) {
    $sql .= " AND t.Gia <= ?";
    $params[] = $max_price;
}

switch ($sort) {
    case 'price_asc': $sql .= " ORDER BY t.Gia ASC"; break;
    case 'price_desc': $sql .= " ORDER BY t.Gia DESC"; break;
    default: $sql .= " ORDER BY t.id DESC"; break;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<style>
    .mini-hero {
        position: relative;
        height: 250px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mini-hero-bg {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-size: cover;
        background-position: center;
        z-index: 1;
        animation: zoomEffect 20s infinite alternate; 
    }
    .mini-hero-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.4);
        z-index: 2;
    }
    .mini-hero-content {
        position: relative;
        z-index: 3;
        text-align: center;
        color: #fff;
    }
    .mini-hero-content h1 {
        font-size: 32px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    @keyframes zoomEffect {
        from { transform: scale(1); }
        to { transform: scale(1.1); }
    }

    .search-section { background: #f4f6f8; padding: 30px 0; border-bottom: 1px solid #e1e1e1; }
    .search-box-container {
        background: #fff; padding: 20px; border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        display: flex; gap: 15px; flex-wrap: wrap; align-items: end;
    }
    .form-group-custom { flex: 1; min-width: 200px; }
    .form-group-custom label { font-weight: 600; font-size: 14px; margin-bottom: 8px; display: block; color: #333; }
    .input-with-icon { position: relative; }
    .input-with-icon i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888; }
    .input-with-icon input, .input-with-icon select {
        width: 100%; padding: 12px 15px 12px 40px;
        border: 1px solid #ddd; border-radius: 5px; font-size: 14px; outline: none; transition: border-color 0.3s;
    }
    .input-with-icon input:focus { border-color: #0866FF; }
    
    .btn-search-submit {
        background: #0866FF; color: white; border: none; padding: 12px 25px;
        border-radius: 5px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px;
        transition: background 0.3s; height: 45px;
    }
    .btn-search-submit:hover { background: #004ecc; }

    .filter-bar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee;
    }
    .sort-select { padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; outline: none; }

    .tour-grid-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr); 
        gap: 30px;
    }

    @media (max-width: 992px) {
        .tour-grid-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
    }
    @media (max-width: 600px) {
        .tour-grid-container {
            grid-template-columns: 1fr;
        }
        .search-box-container { flex-direction: column; gap: 10px; }
        .form-group-custom { width: 100%; }
        .btn-search-submit { width: 100%; justify-content: center; }
    }
    .tour-card-new {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
        height: 100%; 
        border: 1px solid #f0f0f0; 
    }
    .tour-card-new:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.12);
    }
    
    .tc-header {
        position: relative;
        padding-top: 66.66%; 
        overflow: hidden;
    }
    .tc-img {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    .tour-card-new:hover .tc-img { transform: scale(1.1); }
    
    .badge-special {
        position: absolute; top: 15px; left: 0;
        background: #FF3366; color: white; padding: 5px 12px;
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        border-top-right-radius: 20px; border-bottom-right-radius: 20px;
        z-index: 2; box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
    }

    .media-actions {
        position: absolute; bottom: 10px; right: 10px;
        display: flex; gap: 8px; z-index: 2;
    }
    .media-icon {
        width: 32px; height: 32px;
        background: rgba(0,0,0,0.6); color: #fff; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; cursor: pointer; backdrop-filter: blur(2px);
        transition: background 0.3s;
    }
    .media-icon:hover { background: #0866FF; }

    .tc-body { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
    .tc-cat {
        color: #0866FF; font-size: 11px; font-weight: 700;
        text-transform: uppercase; margin-bottom: 5px; display: block;
    }
    .tc-title {
        font-size: 15px; font-weight: 700; margin: 0 0 8px 0;
        line-height: 1.4; color: #333;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        height: 42px;
    }
    .tc-rating { color: #FFC107; font-size: 12px; margin-bottom: 10px; }
    
    .tc-meta {
        display: flex; gap: 15px; color: #666; font-size: 12px;
        margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;
    }
    .tc-meta span { display: flex; align-items: center; gap: 4px; }

    .tc-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
    .tc-price-label { font-size: 11px; color: #888; text-decoration: line-through; }
    .tc-price { color: #d32f2f; font-size: 16px; font-weight: 800; }
    .btn-view-detail {
        padding: 6px 12px; border: 1px solid #0866FF; color: #0866FF;
        border-radius: 4px; font-weight: 600; font-size: 12px;
        text-decoration: none; transition: all 0.2s;
    }
    .btn-view-detail:hover { background: #0866FF; color: #fff; }
</style>

<div class="mini-hero">
    <div class="mini-hero-bg" style="background-image: url('../assets/images/banner_search.jpg');"></div> 
    <div class="mini-hero-overlay"></div>
    <div class="mini-hero-content">
        <h1>Khám Phá Hành Trình Của Bạn</h1>
        <p style="margin-top:10px; font-size:16px; opacity:0.9;">Hơn 500+ tour du lịch hấp dẫn đang chờ đón</p>
    </div>
</div>

<section class="search-section">
    <div class="container">
        <form action="search.php" method="GET" class="search-box-container">
            <div class="form-group-custom">
                <label>Bạn muốn đi đâu?</label>
                <div class="input-with-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="k" placeholder="Nhập tên địa điểm..." value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
            </div>

            <div class="form-group-custom">
                <label>Loại hình</label>
                <div class="input-with-icon">
                    <i class="fas fa-list"></i>
                    <select name="c">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach($cats as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat_id == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['TenDanhMuc']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group-custom">
                <label>Ngân sách</label>
                <div class="input-with-icon">
                    <i class="fas fa-wallet"></i>
                    <input type="number" name="p" placeholder="Ví dụ: 5000000" value="<?php echo $max_price; ?>">
                </div>
            </div>

            <button type="submit" class="btn-search-submit">
                <i class="fas fa-search"></i> TÌM KIẾM
            </button>
        </form>
    </div>
</section>

<section class="section-padding" style="background: #fff; padding-top: 40px; padding-bottom: 60px;">
    <div class="container">
        
        <div class="filter-bar">
            <div>
                <h3 style="margin:0; font-size:20px; color:#333;">
                    <?php echo empty($keyword) ? "Tất cả các tour" : "Kết quả cho: \"" . htmlspecialchars($keyword) . "\""; ?>
                </h3>
                <span style="color:#666; font-size:14px;">Tìm thấy <strong><?php echo count($results); ?></strong> kết quả</span>
            </div>
            
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="font-weight:600; color:#555;">Sắp xếp:</span>
                <form id="sortForm" action="" method="GET">
                    <input type="hidden" name="k" value="<?php echo htmlspecialchars($keyword); ?>">
                    <input type="hidden" name="c" value="<?php echo $cat_id; ?>">
                    <input type="hidden" name="p" value="<?php echo $max_price; ?>">
                    
                    <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                    </select>
                </form>
            </div>
        </div>

        <?php if (count($results) > 0): ?>
            <div class="tour-grid-container">
                <?php foreach ($results as $tour): ?>
                    <?php 
                        $imgSrc = !empty($tour['HinhAnh']) ? $tour['HinhAnh'] : '../assets/images/default-tour.jpg';
                        if (!(strpos($imgSrc, 'http') === 0)) {
                            $imgSrc = '/DoAn_TourDuLich/' . $imgSrc;
                        }
                        
                        $soSao = rand(4, 5); 
                        $soNgay = rand(2, 4) . "N" . rand(1, 3) . "Đ"; 
                        $soCho = rand(3, 10); 
                    ?>
                    
                    <div class="tour-card-new">
                        <div class="tc-header">
                            <span class="badge-special">Đặc sắc</span>
                            <a href="tour_detail.php?id=<?php echo $tour['id']; ?>">
                                <img src="<?php echo $imgSrc; ?>" class="tc-img" alt="<?php echo htmlspecialchars($tour['TenTour']); ?>">
                            </a>
                            <div class="media-actions">
                                <div class="media-icon" title="Xem ảnh"><i class="fas fa-camera"></i></div>
                                <div class="media-icon" title="Xem video"><i class="fas fa-video"></i></div>
                            </div>
                        </div>
                        
                        <div class="tc-body">
                            <span class="tc-cat"><?php echo htmlspecialchars($tour['TenDanhMuc'] ?? 'TOUR HOT'); ?></span>
                            
                            <h3 class="tc-title">
                                <a href="tour_detail.php?id=<?php echo $tour['id']; ?>" style="color:inherit; text-decoration:none;">
                                    <?php echo htmlspecialchars($tour['TenTour']); ?>
                                </a>
                            </h3>

                            <div class="tc-rating">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="<?php echo $i <= $soSao ? 'fas' : 'far'; ?> fa-star"></i>
                                <?php endfor; ?>
                                <span style="color:#999; font-size:11px;">(<?php echo rand(10, 50); ?> đánh giá)</span>
                            </div>

                            <div class="tc-meta">
                                <span><i class="far fa-clock"></i> <?php echo $soNgay; ?></span>
                                <span><i class="fas fa-user-friends"></i> Còn <?php echo $soCho; ?> chỗ</span>
                            </div>

                            <div class="tc-footer">
                                <div>
                                    <div class="tc-price-label">Giá: <?php echo number_format($tour['Gia'] * 1.15); ?>đ</div>
                                    <div class="tc-price"><?php echo number_format($tour['Gia'], 0, ',', '.'); ?>đ</div>
                                </div>
                                <a href="tour_detail.php?id=<?php echo $tour['id']; ?>" class="btn-view-detail">
                                    Chi tiết <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding: 60px 0;">
                <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" style="opacity:0.4; margin-bottom:15px;">
                <h3 style="color:#666; font-size:18px;">Không tìm thấy tour phù hợp!</h3>
                <a href="search.php" class="btn-search-submit" style="display:inline-block; width:auto; margin-top:15px; background:#888;">
                    Xóa bộ lọc
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    const banners = [
        'https://res.cloudinary.com/dmaeuom2i/image/upload/v1768296075/trang-an-2767455_1280_fflvty.jpg',
        'https://res.cloudinary.com/dmaeuom2i/image/upload/v1768295995/vietnam-5150143_1280_ajvimh.jpg',
        'https://res.cloudinary.com/dmaeuom2i/image/upload/v1768295725/pexels-th-hugo-349378163-35564084_zxqdho.jpg'
    ];
    const randomBg = banners[Math.floor(Math.random() * banners.length)];
    document.querySelector('.mini-hero-bg').style.backgroundImage = `url('${randomBg}')`;
</script>

<?php include '../includes/footer.php'; ?>