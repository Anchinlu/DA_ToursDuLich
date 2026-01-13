<?php 
include 'includes/header.php'; 
require_once 'config/db_connect.php';

// 1. LẤY 4 TOUR MỚI NHẤT
try {
    $sql = "SELECT * FROM Tour ORDER BY id DESC LIMIT 4";
    $stmt = $db->query($sql);
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $tours = []; 
}

// 2. LẤY DANH MỤC
try {
    $sqlCat = "SELECT * FROM DanhMuc LIMIT 6"; 
    $stmtCat = $db->prepare($sqlCat);
    $stmtCat->execute();
    $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

// 4. LẤY GIÁ CAO NHẤT TRONG DB ĐỂ TẠO BỘ LỌC
try {
    $sqlPrice = "SELECT MAX(Gia) as MaxGia FROM Tour";
    $stmtPrice = $db->prepare($sqlPrice);
    $stmtPrice->execute();
    $maxPriceRow = $stmtPrice->fetch(PDO::FETCH_ASSOC);
    $maxDbPrice = $maxPriceRow['MaxGia'] ?? 10000000; // Mặc định 10tr nếu ko có tour
} catch (Exception $e) {
    $maxDbPrice = 10000000;
}

// Tạo các mốc giá đề xuất (Đơn vị: VNĐ)
$priceMilestones = [500000, 1000000, 2000000, 3000000, 5000000, 7000000, 10000000, 15000000, 20000000];

// 3. HÀM CHỌN ICON
function getIconCategory($name) {
    $name = mb_strtolower($name, 'UTF-8');
    if (strpos($name, 'biển') !== false) return 'fa-water';
    if (strpos($name, 'núi') !== false) return 'fa-mountain';
    if (strpos($name, 'nghỉ dưỡng') !== false) return 'fa-spa';
    if (strpos($name, 'văn hóa') !== false) return 'fa-gopuram';
    if (strpos($name, 'sinh thái') !== false) return 'fa-tree';
    if (strpos($name, 'food') !== false || strpos($name, 'ẩm thực') !== false) return 'fa-utensils';
    if (strpos($name, 'team') !== false) return 'fa-users';
    return 'fa-map-signs';
}
?>

<section class="hero-section">
    <div class="hero-slideshow">
        <div class="slide active" style="background-image: url('https://images.pexels.com/photos/2422265/pexels-photo-2422265.jpeg?auto=compress&cs=tinysrgb&w=1600');">
            <div class="overlay"></div>
            <div class="container h-100">
                <div class="hero-content">
                    <span class="badge anim-item delay-1">Khám phá thiên nhiên</span>
                    <h1 class="hero-title anim-item delay-2">TRẢI NGHIỆM CUỘC SỐNG <br>TRONG RỪNG</h1>
                    <p class="hero-desc anim-item delay-3">Hòa mình vào thiên nhiên hoang dã, khám phá rừng nguyên sinh.</p>
                    <button class="btn-primary anim-item delay-4">Xem chi tiết</button>
                </div>
            </div>
        </div>
        <div class="slide" style="background-image: url('https://images.pexels.com/photos/1365428/pexels-photo-1365428.jpeg?auto=compress&cs=tinysrgb&w=1600');">
            <div class="overlay"></div>
            <div class="container h-100">
                <div class="hero-content">
                    <span class="badge anim-item delay-1" style="background: #e67e22;">Leo núi & Mạo hiểm</span>
                    <h1 class="hero-title anim-item delay-2">CHINH PHỤC <br>NHỮNG ĐỈNH CAO</h1>
                    <p class="hero-desc anim-item delay-3">Thử thách bản thân với những cung đường trekking tuyệt đẹp.</p>
                    <button class="btn-primary anim-item delay-4">Đặt tour ngay</button>
                </div>
            </div>
        </div>
        <div class="slide" style="background-image: url('https://images.pexels.com/photos/2166553/pexels-photo-2166553.jpeg?auto=compress&cs=tinysrgb&w=1600');">
            <div class="overlay"></div>
            <div class="container h-100">
                <div class="hero-content">
                    <span class="badge anim-item delay-1" style="background: #039be5;">Biển xanh vẫy gọi</span>
                    <h1 class="hero-title anim-item delay-2">THƯ GIÃN <br>BÊN BỜ BIỂN XANH</h1>
                    <p class="hero-desc anim-item delay-3">Tận hưởng kỳ nghỉ dưỡng đẳng cấp tại những bãi biển đẹp nhất.</p>
                    <button class="btn-primary anim-item delay-4">Khám phá ngay</button>
                </div>
            </div>
        </div>
        <div class="slide" style="background-image: url('https://images.pexels.com/photos/1659438/pexels-photo-1659438.jpeg?auto=compress&cs=tinysrgb&w=1600');">
            <div class="overlay"></div>
            <div class="container h-100">
                <div class="hero-content">
                    <span class="badge anim-item delay-1" style="background: #43a047;">Camping & Glamping</span>
                    <h1 class="hero-title anim-item delay-2">ĐÊM NGỦ <br>DƯỚI NGÀN VÌ SAO</h1>
                    <p class="hero-desc anim-item delay-3">Trải nghiệm cắm trại tiện nghi, đốt lửa trại và ngắm sao đêm.</p>
                    <button class="btn-primary anim-item delay-4">Đặt lều ngay</button>
                </div>
            </div>
        </div>
    </div>

    <form action="pages/search.php" method="GET" class="search-box-wrapper anim-item delay-4">
        <div class="search-item">
            <label class="search-label"><i class="fas fa-map-marker-alt"></i> Từ khóa</label>
            <input type="text" name="k" id="search-keyword" class="search-input" placeholder="Bạn muốn đi đâu?">
        </div>
        <div class="search-item">
            <label class="search-label"><i class="fas fa-route"></i> Danh mục</label>
            <select name="c" id="search-category" class="search-input" style="background:transparent; border:none;">
                <option value="">Tất cả</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['TenDanhMuc']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="search-item">
            <label class="search-label"><i class="far fa-calendar-alt"></i> Ngày đi</label>
            <input type="date" name="d" class="search-input">
        </div>
        <div class="search-item">
            <label class="search-label"><i class="fas fa-tag"></i> Ngân sách (Tối đa)</label>
            <select name="p" class="search-input" style="background:transparent; border:none; cursor:pointer;">
                <option value="">Tất cả mức giá</option>
                
                <?php foreach($priceMilestones as $price): ?>
                    <?php if ($price <= $maxDbPrice * 1.5): ?>
                        <option value="<?php echo $price; ?>">
                            Dưới <?php echo number_format($price, 0, ',', '.'); ?>đ
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if($maxDbPrice > end($priceMilestones)): ?>
                     <option value="<?php echo $maxDbPrice; ?>">
                        Dưới <?php echo number_format($maxDbPrice, 0, ',', '.'); ?>đ
                     </option>
                <?php endif; ?>
            </select>
        </div>
        <div class="search-item">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i> TÌM KIẾM</button>
        </div>
    </form>
</section>

<section class="adventure-section">
    <div class="container">
        <div class="text-center" style="margin-bottom: 50px;">
            <span class="section-tag" style="background:#333; color:#f59e0b;">Hoạt động nổi bật</span>
            <h2 class="section-title" style="color:white;">Cảm giác phiêu lưu</h2>
        </div>

        <div class="adventure-wrapper">
            
            <div class="adv-tabs">
                <?php if(count($categories) > 0): ?>
                    <?php foreach($categories as $index => $cat): ?>
                        <?php 
                            $link = "search.php?c=" . $cat['id']; 
                            $activeClass = ($index == 0) ? 'active' : '';
                            
                            // SỬA LỖI MÀU CHỮ Ở ĐÂY:
                            // Nếu active -> Chữ trắng. Nếu không -> Chữ đen (#333)
                            $textStyle = ($index == 0) ? 'color: white;' : 'color: #333;';
                        ?>
                        <a href="<?php echo $link; ?>" class="adv-tab-item <?php echo $activeClass; ?>" style="text-decoration: none; <?php echo $textStyle; ?> display: flex; align-items: center; gap: 10px;">
                            <i class="fas <?php echo getIconCategory($cat['TenDanhMuc']); ?>"></i> 
                            <?php echo htmlspecialchars($cat['TenDanhMuc']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="adv-tab-item" style="color: #333;">Chưa có danh mục</div>
                <?php endif; ?>
            </div>

            <div class="adv-content">
                <div class="adv-text-block">
                    <i class="fas fa-campground adv-icon-large" style="color: #4CAF50; font-size: 40px; margin-bottom: 20px;"></i>
                    <h3 class="adv-title">Cuộc phiêu lưu thực sự và tận hưởng những chuyến đi mơ ước của bạn.</h3>
                    <p class="adv-desc">Chúng tôi đặt trái tim của mình vào việc tạo ra những trải nghiệm phiêu lưu độc đáo, nơi bạn có thể tận hưởng sự kết nối thân thiết với tự nhiên.</p>
                    
                    <div class="skill-box">
                        <div class="skill-info"><span>Khách hàng hài lòng</span><span>90%</span></div>
                        <div class="progress-bg"><div class="progress-bar" style="width: 90%;"></div></div>
                    </div>
                    <div class="skill-box">
                        <div class="skill-info"><span>Đánh giá tích cực</span><span>98%</span></div>
                        <div class="progress-bg"><div class="progress-bar" style="width: 98%;"></div></div>
                    </div>
                </div>

                <div class="adv-image-block">
                    <img src="https://images.pexels.com/photos/2398220/pexels-photo-2398220.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Camping" style="border-radius: 10px;">
                </div>
            </div>
        </div>
    </div>
</section>

<div class="cta-strip">
    <div class="container">
        <div class="cta-content">
            <div class="cta-text">
                <i class="fas fa-globe-asia cta-icon"></i>
                <div>
                    <span style="font-size: 12px; text-transform:uppercase; opacity:0.8;">Phiêu lưu độc đáo</span>
                    <h3>Sẵn sàng phiêu lưu và tận hưởng thiên nhiên</h3>
                </div>
            </div>
            <a href="search.php" class="btn-white">Khám phá ngay</a>
        </div>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="text-center">
            <span class="section-tag">Trải nghiệm tuyệt vời</span>
            <h2 class="section-title">Các địa điểm thú vị</h2>
        </div>

        <div class="adventure-grid">
            <?php if (isset($tours) && count($tours) > 0): ?>
                <?php foreach ($tours as $tour): ?>
                    <a href="tour_detail.php?id=<?php echo $tour['id']; ?>" class="card-adventure">
                        <span class="card-badge">Đặc sắc</span>
                        <?php $imgSrc = !empty($tour['HinhAnh']) ? $tour['HinhAnh'] : 'https://images.pexels.com/photos/2161449/pexels-photo-2161449.jpeg'; ?>
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($tour['TenTour']); ?>">
                        
                        <div class="card-overlay">
                            <div class="card-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <h4 class="card-title"><?php echo htmlspecialchars($tour['TenTour']); ?></h4>
                            <div class="card-meta">
                                <span><i class="fas fa-map-marker-alt"></i> Việt Nam</span>
                                <span style="color: #4dd0e1; font-weight:700;">
                                    <i class="fas fa-tag"></i> <?php echo number_format($tour['Gia'], 0, ',', '.'); ?>đ
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; grid-column: 1/-1;">Chưa có tour nào.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-padding" style="background: #f9f9f9; padding-top: 0;">
    <div class="container">
        <div class="service-grid">
            <div class="service-card">
                <div class="srv-icon"><i class="fas fa-map-marked-alt"></i></div>
                <div class="srv-content">
                    <h4>Chuyến đi tùy chỉnh</h4>
                    <p>Có thể chọn địa điểm, thời gian và hoạt động mà bạn muốn tham gia.</p>
                </div>
            </div>
            <div class="service-card">
                <div class="srv-icon"><i class="fas fa-user-shield"></i></div>
                <div class="srv-content">
                    <h4>Hướng dẫn viên chuyên nghiệp</h4>
                    <p>Đội ngũ hướng dẫn viên có kiến thức sâu rộng về văn hóa địa phương.</p>
                </div>
            </div>
            <div class="service-card">
                <div class="srv-icon"><i class="far fa-star"></i></div>
                <div class="srv-content">
                    <h4>Chất lượng dịch vụ cao</h4>
                    <p>Chúng tôi đảm bảo rằng mọi khía cạnh đều được chăm sóc tận tâm.</p>
                </div>
            </div>
            <div class="service-card">
                <div class="srv-icon"><i class="fas fa-globe"></i></div>
                <div class="srv-content">
                    <h4>Địa điểm đa dạng</h4>
                    <p>Cung cấp các địa điểm du lịch hấp dẫn trên khắp thế giới phù hợp sở thích.</p>
                </div>
            </div>
            <div class="service-card">
                <div class="srv-icon"><i class="fas fa-camera-retro"></i></div>
                <div class="srv-content">
                    <h4>Trải nghiệm độc đáo</h4>
                    <p>Không chỉ đưa bạn đến điểm du lịch phổ biến mà còn mang đến trải nghiệm khác biệt.</p>
                </div>
            </div>
            <div class="service-card">
                <div class="srv-icon"><i class="fas fa-headset"></i></div>
                <div class="srv-content">
                    <h4>Hỗ trợ dịch vụ 24/7</h4>
                    <p>Đội ngũ chăm sóc khách hàng luôn sẵn sàng giải đáp mọi thắc mắc của bạn.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/slider.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const keywordInput = document.getElementById('search-keyword');
    const categorySelect = document.getElementById('search-category');
    
    // 1. Định nghĩa từ khóa liên quan (Bạn có thể thêm tùy thích)
    // Cấu trúc: 'từ khóa bạn đoán người dùng gõ' : 'Tên danh mục tương ứng trong Select option'
    const keywordMapping = {
        'biển': ['biển', 'đảo', 'bơi', 'lặn', 'hải sản', 'sea', 'beach'],
        'núi': ['núi', 'leo', 'đỉnh', 'rừng', 'trekking', 'mountain', 'hill'],
        'nghỉ dưỡng': ['nghỉ dưỡng', 'resort', 'spa', 'relax', 'khách sạn'],
        'văn hóa': ['văn hóa', 'di tích', 'chùa', 'đền', 'lịch sử', 'culture'],
        'sinh thái': ['sinh thái', 'vườn', 'chim', 'cây', 'eco'],
        'ẩm thực': ['ẩm thực', 'ăn', 'food', 'món', 'nhậu'],
        'team': ['team', 'building', 'đoàn', 'công ty', 'nhóm']
    };

    // 2. Lắng nghe sự kiện khi người dùng gõ
    if (!keywordInput || !categorySelect) return;

    keywordInput.addEventListener('input', function() {
        const userInput = this.value.toLowerCase().trim();
        
        // Nếu ô trống thì reset về "Tất cả"
        if (userInput === '') {
            categorySelect.value = ""; 
            return;
        }

        // 3. Logic so khớp
        // Cách A: So khớp trực tiếp với Tên Danh Mục (Ví dụ gõ "Biển" -> chọn "Du lịch Biển")
        for (let i = 0; i < categorySelect.options.length; i++) {
            const optionText = categorySelect.options[i].text.toLowerCase();
            if (userInput.length >= 2 && optionText.includes(userInput)) {
                categorySelect.selectedIndex = i;
                return; // Tìm thấy thì dừng luôn
            }
        }

        // Cách B: So khớp với bộ từ điển keywordMapping ở trên (Ví dụ gõ "bơi" -> chọn "Biển")
        for (const [categoryKey, keywords] of Object.entries(keywordMapping)) {
            // Kiểm tra xem từ người dùng gõ có nằm trong danh sách từ khóa không
            const isMatch = keywords.some(key => userInput.includes(key));
            
            if (isMatch) {
                // Tìm option trong select box có chứa từ khóa chính (categoryKey)
                for (let i = 0; i < categorySelect.options.length; i++) {
                    const optionText = categorySelect.options[i].text.toLowerCase();
                    if (optionText.includes(categoryKey)) {
                        categorySelect.selectedIndex = i;
                        return;
                    }
                }
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>